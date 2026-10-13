<?php

namespace App\Listeners;

use App\Events\ProjectSubmitted;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyProjectSubmitted implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(ProjectSubmitted $event): void
    {
        $project = $event->project;
        $actor   = $event->actor;

        AuditService::log(
            'PROPOSAL_SUBMITTED',
            'Project',
            $project->project_id,
            "PI submitted research proposal '{$project->title}' (ETB " . number_format($project->requested_budget, 2) . ")",
            $actor->id
        );

        // Notify Department Head and Research Coordinators
        NotificationService::notifyManagers(
            'PROPOSAL_SUBMITTED',
            "New proposal '{$project->title}' submitted by {$actor->name} awaiting Department Head screening.",
            ['dh', 'coordinator']
        );
    }
}
