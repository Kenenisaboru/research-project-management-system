<?php

namespace App\Listeners;

use App\Events\ProjectApproved;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyProjectApproved implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(ProjectApproved $event): void
    {
        $project = $event->project;
        $actor   = $event->actor;

        AuditService::log(
            'PROJECT_APPROVED',
            'Project',
            $project->project_id,
            "Project '{$project->title}' approved via {$event->tier} tier with ETB " . number_format($project->approved_budget, 2),
            $actor->id
        );

        NotificationService::notifyUser(
            (int) $project->pi_id,
            'PROJECT_APPROVED',
            "Congratulations! Your research proposal '{$project->title}' has been officially approved with a budget of ETB " . number_format($project->approved_budget, 2) . ". Please proceed to contract signing."
        );
    }
}
