<?php

namespace App\Listeners;

use App\Events\BudgetDisbursed;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAndNotifyBudgetDisbursed implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BudgetDisbursed $event): void
    {
        $budget = $event->budgetRequest;
        $actor  = $event->actor;

        AuditService::log(
            'BUDGET_DISBURSED',
            'BudgetRequest',
            $budget->id,
            "Finance released payment of ETB " . number_format($event->amount, 2) . " via {$event->paymentMethod} for Project #{$budget->project_id}",
            $actor->id
        );

        $project = $budget->project;
        if ($project && $project->pi_id) {
            NotificationService::notifyUser(
                (int) $project->pi_id,
                'BUDGET_DISBURSED',
                "Disbursement of ETB " . number_format($event->amount, 2) . " ({$budget->milestone_phase}) has been processed and released via {$event->paymentMethod}."
            );
        }
    }
}
