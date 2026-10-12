<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    private const DIR = 'backups';

    private const TABLES = [
        'users', 'roles', 'permissions', 'role_permission', 'colleges', 'departments',
        'thematic_areas', 'projects', 'project_members', 'evaluations', 'irerc_clearances',
        'budget_requests', 'budget_amendments', 'milestone_reports', 'project_extensions',
        'pi_transfers', 'project_terminations', 'certificates', 'procurement_requests',
        'research_calls', 'papers', 'paper_evaluations', 'awards', 'notifications', 'audit_logs',
    ];

    public function index()
    {
        Storage::disk('local')->makeDirectory(self::DIR);

        $files = collect(Storage::disk('local')->files(self::DIR))
            ->map(function ($path) {
                return [
                    'name'     => basename($path),
                    'size'     => Storage::disk('local')->size($path),
                    'modified' => Storage::disk('local')->lastModified($path),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        $driver   = DB::connection()->getDriverName();
        $dbSize   = null;
        if ($driver === 'sqlite') {
            $dbPath = DB::connection()->getDatabaseName();
            $dbSize = file_exists($dbPath) ? filesize($dbPath) : null;
        }

        return view('admin.backups', compact('files', 'driver', 'dbSize'));
    }

    public function create()
    {
        Storage::disk('local')->makeDirectory(self::DIR);
        $driver = DB::connection()->getDriverName();
        $stamp  = now()->format('Y-m-d_His');

        if ($driver === 'sqlite') {
            $dbPath = DB::connection()->getDatabaseName();
            if (! file_exists($dbPath)) {
                return back()->with('error', 'SQLite database file not found.');
            }
            $name = "rpms-backup-{$stamp}.sqlite";
            Storage::disk('local')->put(self::DIR . '/' . $name, file_get_contents($dbPath));
        } elseif ($driver === 'mysql') {
            $name = "rpms-backup-{$stamp}.sql";
            $sql  = $this->dumpMysql();
            if ($sql === null) {
                $name = "rpms-backup-{$stamp}.json";
                Storage::disk('local')->put(self::DIR . '/' . $name, $this->dumpJson());
            } else {
                Storage::disk('local')->put(self::DIR . '/' . $name, $sql);
            }
        } else {
            $name = "rpms-backup-{$stamp}.json";
            Storage::disk('local')->put(self::DIR . '/' . $name, $this->dumpJson());
        }

        AuditService::log('BACKUP_CREATED', 'Backup', null, "Database backup created: {$name} ({$driver})");

        return back()->with('success', "Backup created successfully: {$name}");
    }

    public function download($file)
    {
        $path = self::DIR . '/' . basename($file);
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
        $path = self::DIR . '/' . basename($file);
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            AuditService::log('BACKUP_DELETED', 'Backup', null, "Backup deleted: {$file}");
        }

        return back()->with('success', 'Backup file removed.');
    }

    private function dumpJson(): string
    {
        $payload = [
            'generated_at' => now()->toIso8601String(),
            'driver'       => DB::connection()->getDriverName(),
            'tables'       => [],
        ];

        foreach (self::TABLES as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }
            $payload['tables'][$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function restoreJson(string $json): void
    {
        $data = json_decode($json, true);
        if (! is_array($data) || ! isset($data['tables'])) {
            throw new \RuntimeException('Invalid backup file structure.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (self::TABLES as $table) {
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

    private function dumpMysql(): ?string
    {
        $db   = DB::connection()->getConfig();
        $host = $db['host'] ?? '127.0.0.1';
        $port = $db['port'] ?? '3306';
        $user = $db['username'] ?? '';
        $pass = $db['password'] ?? '';
        $name = $db['database'] ?? '';

        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s 2>NUL',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            $pass !== '' ? '--password=' . escapeshellarg($pass) : '',
            escapeshellarg($name)
        );

        $output = @shell_exec($cmd);

        return is_string($output) && strlen($output) > 100 ? $output : null;
    }
}