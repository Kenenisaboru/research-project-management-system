<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\AuditLog;
use App\Models\BudgetRequest;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\IRERCClearance;
use App\Models\Project;
use App\Models\ProjectExtension;
use App\Models\ProjectMember;
use App\Models\User;
use App\Services\AlertsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DashboardController
 *
 * Provides real-time dashboard statistics and operational indicators for the authenticated user.
 */
class DashboardController extends ApiController
{
    /**
     * Get role-tailored dashboard metrics and system alert summaries.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = $user->role;

        $stats = $this->getStatsForRole($user, $role);
        $alerts = AlertsService::all();

        return $this->success([
            'role'   => $role,
            'stats'  => $stats,
            'alerts' => $alerts,
        ], 'Dashboard metrics retrieved successfully.');
    }

    /**
     * Compile role-specific statistical summaries.
     *
     * @param  User    $user
     * @param  string  $role
     * @return array<string, mixed>
     */
    protected function getStatsForRole(User $user, string $role): array
    {
        if ($role === 'pi') {
            $base = Project::where('pi_id', $user->id);
            return [
                'my_projects'        => $base->count(),
                'draft'              => (clone $base)->where('status', 'Draft')->count(),
                'submitted'          => (clone $base)->where('status', 'Submitted')->count(),
                'active'             => (clone $base)->where('status', 'Active')->count(),
                'completed'          => (clone $base)->where('status', 'Completed')->count(),
                'total_budget'       => (float) (clone $base)->sum('requested_budget'),
                'approved_budget'    => (float) (clone $base)->sum('approved_budget'),
                'pending_extensions' => ProjectExtension::whereHas('project', fn ($q) => $q->where('pi_id', $user->id))->where('status', 'Pending')->count(),
            ];
        }

        if ($role === 'reviewer') {
            return [
                'pending_reviews'   => Evaluation::where('examiner_id', $user->id)->where('decision', 'Pending')->count(),
                'completed_reviews' => Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->count(),
                'avg_score'         => round((float) (Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->avg('score') ?? 0), 2),
            ];
        }

        if ($role === 'dh') {
            return [
                'pending_screening' => Project::where('status', 'Submitted')->where('dept_id', $user->dept_id)->count(),
                'screened_today'    => Project::where('status', 'DH_Screened')->where('dept_id', $user->dept_id)->whereDate('updated_at', today())->count(),
                'dept_projects'     => Project::where('dept_id', $user->dept_id)->count(),
            ];
        }

        if ($role === 'finance') {
            return [
                'pending_release' => BudgetRequest::where('status', 'Approved')
                    ->where(fn ($q) => $q->where('milestone_phase', 'like', 'Tranche%')->orWhere('milestone_phase', 'Budget Amendment'))
                    ->whereHas('project', fn ($q) => $q->whereIn('status', ['Active', 'Approved', 'Completed']))
                    ->count(),
                'disbursed'       => (float) BudgetRequest::where('status', 'Released')->sum('approved_amount'),
                'transactions'    => BudgetRequest::where('status', 'Released')->count(),
            ];
        }

        if ($role === 'admin') {
            return [
                'total_users'    => User::count(),
                'active_users'   => User::where('status', 'active')->count(),
                'projects'       => Project::where('status', '!=', 'Draft')->count(),
                'evaluations'    => Evaluation::count(),
                'audit_logs'     => AuditLog::count(),
            ];
        }

        return [
            'total_projects'  => Project::where('status', '!=', 'Draft')->count(),
            'active_projects' => Project::where('status', 'Active')->count(),
            'pending_reviews' => Evaluation::where('decision', 'Pending')->count(),
            'pending_budgets' => BudgetRequest::where('status', 'Pending')->count(),
        ];
    }
}
