<?php

namespace App\Listeners;

use App\Events\ExtensionApproved;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyExtensionApproved implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(ExtensionApproved $event): void
    {
        $ext   = $event->extension;
        $actor = $event->actor;

        AuditService::log(
            'EXTENSION_APPROVED',
            'ProjectExtension',
            $ext->id,
            "Extension #{$ext->extension_number} ({$ext->months} months) approved for Project #{$ext->project_id}",
            $actor->id
        );

        $project = $ext->project;
        if ($project && $project->pi_id) {
            NotificationService::notifyUser(
                (int) $project->pi_id,
                'EXTENSION_APPROVED',
                "Your timeline extension request of {$ext->months} months for project '{$project->title}' has been approved."
            );
        }
    }
}
