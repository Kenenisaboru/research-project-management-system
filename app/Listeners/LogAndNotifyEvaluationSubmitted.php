<?php

namespace App\Listeners;

use App\Events\EvaluationSubmitted;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyEvaluationSubmitted implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(EvaluationSubmitted $event): void
    {
        $evaluation = $event->evaluation;
        $actor      = $event->actor;

        AuditService::log(
            'EVALUATION_SCORED',
            'Evaluation',
            $evaluation->eval_id,
            "Blind peer review scored ({$event->score}/100 - {$event->decision}) for Project #{$evaluation->project_id}",
            $actor->id
        );

        NotificationService::notifyManagers(
            'EVALUATION_SUBMITTED',
            "Peer review evaluation submitted for Project #{$evaluation->project_id} ({$event->score}/100 - {$event->decision}).",
            ['coordinator']
        );
    }
}
