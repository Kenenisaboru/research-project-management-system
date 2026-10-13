<?php

namespace App\Listeners;

use App\Events\PaperApproved;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyPaperApproved implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(PaperApproved $event): void
    {
        $paper = $event->paper;
        $actor = $event->actor;

        AuditService::log(
            'PAPER_APPROVED',
            'Paper',
            $paper->id,
            "Research paper '{$paper->title}' approved by VP/RCSC governance chain",
            $actor->id
        );

        if ($paper->submitted_by) {
            NotificationService::notifyUser(
                (int) $paper->submitted_by,
                'PAPER_APPROVED',
                "Your research paper publication '{$paper->title}' has been reviewed and approved."
            );
        }
    }
}
