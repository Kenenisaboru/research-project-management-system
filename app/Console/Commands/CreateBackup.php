<?php

namespace App\Console\Commands;

use App\Services\AuditService;
use App\Services\BackupService;
use Illuminate\Console\Command;

class CreateBackup extends Command
{
    protected $signature = 'backups:create {--prune : Remove backups older than the retention window}';

    protected $description = 'Create a database backup (and optionally prune old backups)';

    public function handle(): int
    {
        try {
            $name = BackupService::create();
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        AuditService::log('BACKUP_CREATED', 'Backup', null, "Scheduled backup created: {$name}");
        $this->info("Backup created: {$name}");

        if ($this->option('prune')) {
            $days    = (int) config('rpms.backup_retention_days', 30);
            $removed = BackupService::prune($days);
            $this->info("Pruned {$removed} backup(s) older than {$days} days.");
        }

        return self::SUCCESS;
    }
}