<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public const DIR = 'backups';

    public const TABLES = [
        'users', 'roles', 'permissions', 'role_permission', 'colleges', 'departments',
        'thematic_areas', 'projects', 'project_members', 'evaluations', 'irerc_clearances',
        'budget_requests', 'budget_amendments', 'milestone_reports', 'project_extensions',
        'pi_transfers', 'project_terminations', 'certificates', 'procurement_requests',
        'research_calls', 'papers', 'paper_evaluations', 'awards', 'notifications', 'audit_logs',
    ];

    /**
     * Create a database backup and return the stored file name.
     */
    public static function create(): string
    {
        Storage::disk('local')->makeDirectory(self::DIR);
        $driver = DB::connection()->getDriverName();
        $stamp  = now()->format('Y-m-d_His');

        if ($driver === 'sqlite') {
            $dbPath = DB::connection()->getDatabaseName();
            $name   = "rpms-backup-{$stamp}.sqlite";
            Storage::disk('local')->put(self::DIR . '/' . $name, file_get_contents($dbPath));

            return $name;
        }

        if ($driver === 'mysql' && ($sql = self::dumpMysql()) !== null) {
            $name = "rpms-backup-{$stamp}.sql";
            Storage::disk('local')->put(self::DIR . '/' . $name, $sql);

            return $name;
        }

        $name = "rpms-backup-{$stamp}.json";
        Storage::disk('local')->put(self::DIR . '/' . $name, self::dumpJson());

        return $name;
    }

    public static function dumpJson(): string
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

    private static function dumpMysql(): ?string
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

    /**
     * Remove backups older than the configured retention window.
     */
    public static function prune(int $days): int
    {
        Storage::disk('local')->makeDirectory(self::DIR);
        $cutoff  = now()->subDays($days)->getTimestamp();
        $removed = 0;

        foreach (Storage::disk('local')->files(self::DIR) as $path) {
            if (Storage::disk('local')->lastModified($path) < $cutoff) {
                Storage::disk('local')->delete($path);
                $removed++;
            }
        }

        return $removed;
    }
}