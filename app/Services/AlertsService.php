<?php

namespace App\Services;

use App\Models\BudgetRequest;
use App\Models\Project;

class AlertsService
{
    /**
     * Delayed / overdue projects.
     *
     * An "Active" project is considered overdue when more than 12 months have
     * passed since activation without completion (configurable via setting
     * rpms.overdue_months, default 12) OR any requested milestone/report is past due.
     */
    public static function overdueProjects()
    {
        $graceMonths = (int) config('rpms.overdue_months', 12);

        return Project::where('status', 'Active')
            ->whereNull('completed_at')
            ->where('activated_at', '<', now()->subMonths($graceMonths))
            ->with('pi')
            ->get();
    }

    /**
     * Projects whose total released budget exceeds the approved/requested budget.
     */
    public static function overBudgetProjects()
    {
        return Project::with('pi')->whereIn('status', ['Active', 'Ongoing'])->get()->filter(function (Project $project) {
            $released = (float) BudgetRequest::where('project_id', $project->project_id)
                ->whereNotNull('released_at')
                ->sum('amount');

            $cap = (float) ($project->approved_budget ?: $project->requested_budget);

            return $cap > 0 && $released > $cap;
        })->values();
    }

    /**
     * Count of combined risk alerts (used by the notifications bell).
     */
    public static function backupRiskCount(): int
    {
        return static::overdueProjects()->count() + static::overBudgetProjects()->count();
    }

    /**
     * All risk alerts including per-project reason labels.
     *
     * @return array<int, array{project: Project, type: string, reason: string}>
     */
    public static function all(): array
    {
        $alerts = [];

        foreach (static::overdueProjects() as $project) {
            $alerts[] = [
                'project' => $project,
                'type'    => 'overdue',
                'reason'  => 'Project active beyond the expected completion window.',
            ];
        }

        foreach (static::overBudgetProjects() as $project) {
            $alerts[] = [
                'project' => $project,
                'type'    => 'over_budget',
                'reason'  => 'Released funds exceed the approved budget.',
            ];
        }

        return $alerts;
    }
}