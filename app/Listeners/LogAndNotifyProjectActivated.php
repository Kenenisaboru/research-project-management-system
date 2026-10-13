<?php

namespace App\Listeners;

use App\Events\ProjectActivated;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyProjectActivated implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(ProjectActivated $event): void
    {
        $project = $event->project;
        $actor   = $event->actor;

        AuditService::log(
            'PROJECT_ACTIVATED',
            'Project',
            $project->project_id,
            "Project '{$project->title}' activated following bilateral contract authorization.",
            $actor->id
        );

        NotificationService::notifyUser(
            (int) $project->pi_id,
            'PROJECT_ACTIVATED',
            "Your project '{$project->title}' is now Active. Tranche 1 (30% advance) is queued for Finance disbursement."
        );

        NotificationService::notifyManagers(
            'TRANCHE_QUEUED',
            "Project '{$project->title}' is active. Advance tranche is ready for processing.",
            ['finance']
        );
    }
}
