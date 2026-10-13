<?php

namespace App\Jobs;

use App\Services\AuditService;
use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProcessBackupJob
 *
 * Background job for asynchronous database snapshot creation and auto-pruning.
 */
class ProcessBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param  int|null  $adminUserId
     * @param  int  $pruneDays
     */
    public function __construct(
        public ?int $adminUserId = null,
        public int $pruneDays = 30
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $filename = BackupService::create();
        $pruned   = BackupService::prune($this->pruneDays);

        AuditService::log(
            'BACKUP_CREATED_ASYNC',
            'Backup',
            null,
            "Async backup '{$filename}' created successfully. Pruned {$pruned} expired backup(s).",
            $this->adminUserId
        );
    }
}
