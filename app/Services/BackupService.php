<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public const DIR = 'backups';

    public const TABLES = [
        'colleges', 'departments', 'thematic_areas', 'roles', 'permissions', 'role_permission',
        'users', 'projects', 'project_members', 'evaluations', 'irerc_clearances',
        'budget_requests', 'budget_amendments', 'milestone_reports', 'project_extensions',
        'pi_transfers', 'project_terminations', 'certificates', 'procurement_requests',
        'research_calls', 'papers', 'paper_evaluations', 'awards', 'notifications', 'audit_logs',
        'sessions', 'password_resets', 'failed_jobs', 'personal_access_tokens',
    ];

    /**
     * Create a database backup and return the stored file name.
     */
    public static function create(): string
    {
        Storage::disk('local')->makeDirectory(self::DIR);
        $driver = DB::connection()->getDriverName();
        $stamp  = now()->format('Y-m-d_His_u');

        if ($driver === 'sqlite') {
            $dbPath = DB::connection()->getDatabaseName();
            if ($dbPath === ':memory:' || ! is_file($dbPath)) {
                throw new \RuntimeException('A file-backed SQLite database is required to create a SQLite backup.');
            }

            $name   = "rpms-backup-{$stamp}.sqlite";
            $backupPath = Storage::disk('local')->path(self::DIR . '/' . $name);
            $quotedPath = DB::connection()->getPdo()->quote($backupPath);
            DB::statement("VACUUM INTO {$quotedPath}");

            return $name;
        }

        $name = "rpms-backup-{$stamp}.json";
        if (! Storage::disk('local')->put(self::DIR . '/' . $name, self::dumpJson())) {
            throw new \RuntimeException('Could not write the JSON database backup.');
        }

        return $name;
    }

    public static function dumpJson(): string
    {
        $payload = [
            'version'      => 1,
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

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public static function restoreJson(string $json): void
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Backup file is not valid JSON.', 0, $e);
        }

        if (! is_array($data) || ! isset($data['tables']) || ! is_array($data['tables'])) {
            throw new \InvalidArgumentException('Invalid backup file structure.');
        }

        $tables = $data['tables'];
        if (! $tables) {
            throw new \InvalidArgumentException('Backup file does not contain any table data.');
        }
        $unknownTables = array_diff(array_keys($tables), self::TABLES);
        if ($unknownTables) {
            throw new \InvalidArgumentException('Backup contains unsupported tables.');
        }

        if (isset($data['version'])) {
            if ($data['version'] !== 1) {
                throw new \InvalidArgumentException('Unsupported backup version.');
            }

            $missingTables = array_diff(
                array_filter(self::TABLES, fn ($table) => DB::getSchemaBuilder()->hasTable($table)),
                array_keys($tables)
            );
            if ($missingTables) {
                throw new \InvalidArgumentException('Backup is incomplete and cannot be restored safely.');
            }
        } else {
            $legacyTables = [
                'users', 'roles', 'permissions', 'role_permission', 'colleges', 'departments',
                'thematic_areas', 'projects', 'project_members', 'evaluations', 'irerc_clearances',
                'budget_requests', 'budget_amendments', 'milestone_reports', 'project_extensions',
                'pi_transfers', 'project_terminations', 'certificates', 'procurement_requests',
                'research_calls', 'papers', 'paper_evaluations', 'awards', 'notifications', 'audit_logs',
            ];
            if (array_diff($legacyTables, array_keys($tables))) {
                throw new \InvalidArgumentException('Legacy backup is incomplete and cannot be restored safely.');
            }
        }

        foreach ($tables as $table => $rows) {
            if (! is_array($rows) || ! DB::getSchemaBuilder()->hasTable($table)) {
                throw new \InvalidArgumentException("Backup data for table '{$table}' is invalid or incompatible.");
            }

            $columns = DB::getSchemaBuilder()->getColumnListing($table);
            foreach ($rows as $row) {
                if (! is_array($row) || ! $row) {
                    throw new \InvalidArgumentException("Backup row for table '{$table}' has an invalid shape.");
                }

                foreach ($row as $column => $value) {
                    if (! is_string($column) || ! in_array($column, $columns, true)) {
                        throw new \InvalidArgumentException("Backup row for table '{$table}' has an invalid shape.");
                    }
                    if (is_array($value) || is_object($value)) {
                        throw new \InvalidArgumentException("Backup row for table '{$table}' contains invalid values.");
                    }
                }
            }
        }

        DB::transaction(function () use ($tables) {
            foreach (array_reverse(self::TABLES) as $table) {
                if (array_key_exists($table, $tables)) {
                    DB::table($table)->delete();
                }
            }

            foreach (self::TABLES as $table) {
                if (! array_key_exists($table, $tables)) {
                    continue;
                }

                foreach (array_chunk($tables[$table], 500) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }
        });
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