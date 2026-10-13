<?php

namespace App\Listeners;

use App\Events\ProjectCompleted;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener: Log audit event and notify stakeholders when a project is marked Completed.
 *
 * @implements ShouldQueue
 */
class LogAndNotifyProjectCompleted implements ShouldQueue
{
    use InteractsWithQueue;

    /** @var string Queue name */
    public string $queue = 'notifications';

    /**
     * Handle the event.
     *
     * @param  ProjectCompleted  $event
     * @return void
     */
    public function handle(ProjectCompleted $event): void
    {
        $project = $event->project;

        // Audit log
        AuditService::log(
            'PROJECT_COMPLETED',
            'Project',
            $project->project_id,
            "Project #{$project->project_id} '{$project->title}' has been marked as Completed."
        );

        // Notify the PI
        if ($project->pi_id) {
            NotificationService::notifyUser(
                $project->pi_id,
                'PROJECT_COMPLETED',
                "Congratulations! Project '{$project->title}' has been marked as Completed.",
                'project',
                'normal',
                route('projects.show', $project->project_id)
            );
        }

        // Notify coordinator and admin
        $notifyRoles = ['coordinator', 'admin', 'rcsc', 'vparttcs'];
        $users = \App\Models\User::whereIn('role', $notifyRoles)->get();
        foreach ($users as $user) {
            NotificationService::notifyUser(
                $user->id,
                'PROJECT_COMPLETED',
                "Project '{$project->title}' has been completed successfully.",
                'project',
                'low',
                route('projects.show', $project->project_id)
            );
        }
    }
}
