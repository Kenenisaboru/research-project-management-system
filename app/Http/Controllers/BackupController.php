<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        } catch (\Throwable $e) {
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
        $temp = $file->getRealPath();

        try {
            if ($ext === 'sqlite') {
                if (DB::connection()->getDriverName() !== 'sqlite') {
                    return back()->with('error', 'A .sqlite backup can only be restored into a SQLite database.');
                }
                $dbPath = DB::connection()->getDatabaseName();
                copy($dbPath, $dbPath . '.pre-restore-' . now()->format('YmdHis'));
                copy($temp, $dbPath);
            } elseif ($ext === 'json') {
                $this->restoreJson(file_get_contents($temp));
            } else {
                return back()->with('error', 'Unsupported backup format. Upload a .json or .sqlite backup file.');
            }
        } catch (\Throwable $e) {
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

    private function restoreJson(string $json): void
    {
        $data = json_decode($json, true);
        if (! is_array($data) || ! isset($data['tables'])) {
            throw new \RuntimeException('Invalid backup file structure.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (BackupService::TABLES as $table) {
            if (! isset($data['tables'][$table]) || ! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }
            DB::table($table)->truncate();
            foreach (array_chunk($data['tables'][$table], 500) as $chunk) {
                DB::table($table)->insert($chunk);
            }
        }
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $e) {
            // SQLite / non-MySQL engines ignore this.
        }
    }
}