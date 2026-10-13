<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function index()
    {
        Storage::disk('local')->makeDirectory(BackupService::DIR);

        $files = collect(Storage::disk('local')->files(BackupService::DIR))
            ->map(function ($path) {
                return [
                    'name'     => basename($path),
                    'size'     => Storage::disk('local')->size($path),
                    'modified' => Storage::disk('local')->lastModified($path),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        $driver = DB::connection()->getDriverName();
        $dbSize = null;
        if ($driver === 'sqlite') {
            $dbPath = DB::connection()->getDatabaseName();
            $dbSize = file_exists($dbPath) ? filesize($dbPath) : null;
        }

        return view('admin.backups', compact('files', 'driver', 'dbSize'));
    }

    public function create()
    {
        try {
            $name = BackupService::create();
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }

        AuditService::log('BACKUP_CREATED', 'Backup', null, "Database backup created: {$name}");

        return back()->with('success', "Backup created successfully: {$name}");
    }

    public function download($file)
    {
        $path = BackupService::DIR . '/' . basename($file);
        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Backup file not found.');
        }

        AuditService::log('BACKUP_DOWNLOADED', 'Backup', null, "Backup downloaded: {$file}");

        return Storage::disk('local')->download($path);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:102400'],
        ]);

        $file = $request->file('backup_file');
        $ext  = strtolower($file->getClientOriginalExtension());

        try {
            if ($ext === 'sqlite') {
                if (DB::connection()->getDriverName() !== 'sqlite') {
                    return back()->with('error', 'A .sqlite backup can only be restored into a SQLite database.');
                }
                $this->restoreSqlite($file->getRealPath());
            } elseif ($ext === 'json') {
                BackupService::create();
                BackupService::restoreJson($file->get());
            } else {
                return back()->with('error', 'Unsupported backup format. Upload a .json or .sqlite backup file.');
            }
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        AuditService::log('BACKUP_RESTORED', 'Backup', null, "Database restored from uploaded file: {$file->getClientOriginalName()}");

        return back()->with('success', 'Database restored successfully from backup.');
    }

    public function destroy($file)
    {
        $path = BackupService::DIR . '/' . basename($file);
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            AuditService::log('BACKUP_DELETED', 'Backup', null, "Backup deleted: {$file}");
        }

        return back()->with('success', 'Backup file removed.');
    }

    private function restoreSqlite(string $uploadedPath): void
    {
        $dbPath = DB::connection()->getDatabaseName();
        if ($dbPath === ':memory:' || ! is_file($dbPath)) {
            throw new \RuntimeException('A file-backed SQLite database is required to restore a SQLite backup.');
        }

        $database = new \PDO('sqlite:' . $uploadedPath);
        $database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $integrity = $database->query('PRAGMA integrity_check')->fetchColumn();
        $backupTableNames = $database->query("SELECT name FROM sqlite_master WHERE type = 'table'")->fetchAll(\PDO::FETCH_COLUMN);
        if ($integrity !== 'ok') {
            throw new \RuntimeException('Uploaded SQLite backup failed its integrity check.');
        }

        $requiredTables = array_filter(BackupService::TABLES, fn ($table) => DB::getSchemaBuilder()->hasTable($table));
        $requiredTables[] = 'migrations';
        if (array_diff($requiredTables, $backupTableNames)) {
            throw new \RuntimeException('Uploaded SQLite backup does not contain the required application tables.');
        }
        foreach ($requiredTables as $table) {
            $quotedTable = '"' . str_replace('"', '""', $table) . '"';
            $backupColumns = $database->query("PRAGMA table_info({$quotedTable})")->fetchAll(\PDO::FETCH_COLUMN, 1);
            $currentColumns = DB::getSchemaBuilder()->getColumnListing($table);
            if (array_diff($currentColumns, $backupColumns)) {
                throw new \RuntimeException("Uploaded SQLite backup has an incompatible '{$table}' table schema.");
            }
        }
        $database = null;

        BackupService::create();

        $connectionName = DB::getDefaultConnection();
        $replacementPath = $dbPath . '.restore-' . Str::random(12);
        $displacedPath = $dbPath . '.old-' . Str::random(12);

        if (! copy($uploadedPath, $replacementPath)) {
            throw new \RuntimeException('Could not stage the uploaded SQLite backup.');
        }

        try {
            DB::disconnect($connectionName);
            if (! rename($dbPath, $displacedPath)) {
                throw new \RuntimeException('Could not safely replace the current SQLite database.');
            }
            if (! rename($replacementPath, $dbPath)) {
                if (! rename($displacedPath, $dbPath)) {
                    throw new \RuntimeException("Could not install the uploaded database or roll back; the original database remains at {$displacedPath}.");
                }
                throw new \RuntimeException('Could not install the uploaded SQLite backup.');
            }

            if (is_file($displacedPath) && ! unlink($displacedPath)) {
                logger()->warning('Pre-restore SQLite database file could not be removed.', ['path' => $displacedPath]);
            }
        } finally {
            if (is_file($displacedPath) && ! is_file($dbPath)) {
                if (! rename($displacedPath, $dbPath)) {
                    logger()->critical('SQLite restore rollback failed; original database remains at the displaced path.', [
                        'path' => $displacedPath,
                    ]);
                }
            }
            if (is_file($replacementPath)) {
                unlink($replacementPath);
            }
            DB::purge($connectionName);
        }
    }
}