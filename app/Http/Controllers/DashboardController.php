<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Evaluation;
use App\Models\BudgetRequest;
use App\Models\User;
use App\Models\ProjectMember;
use App\Models\IRERCClearance;
use App\Models\ProjectExtension;
use App\Models\Department;
use App\Services\CacheService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $stats = CacheService::remember(
            CacheService::dashboardStatsKey($user->id, $role),
            CacheService::TTL_DASHBOARD_STATS,
            fn () => $this->getRoleSpecificStats($user, $role)
        );

        $myProjects = collect();
        $assignedReviews = collect();
        $pendingApprovals = collect();
        $teamProjects = collect();

        $pendingDisbursements = collect();
        $disbursedHistory = collect();
        $recentActivity = collect();
        $pendingCancellations = collect();

        if (in_array($role, ['admin', 'coordinator', 'dean', 'rcsc', 'vparttcs'])) {
            $cancellations = Project::where('status', 'PendingCancellation')
                ->with('pi');

            if (in_array($role, ['coordinator', 'dean'])) {
                $collegeId = Department::where('id', $user->dept_id)->value('college_id');
                $collegeDepartmentIds = Department::where('college_id', $collegeId)->pluck('id');
                $cancellations->whereIn('dept_id', $collegeDepartmentIds);
            }

            $pendingCancellations = $cancellations->latest()->get();
        }

        $recentActivity = \App\Models\AuditLog::with('user')
            ->latest()
            ->limit(5)
            ->get();

        if ($role === 'pi') {
            $myProjects = Project::where('pi_id', $user->id)->with(['thematicArea', 'irercClearance'])->get();
        } elseif ($role === 'reviewer') {
            $assignedReviews = Evaluation::where('examiner_id', $user->id)
                ->with('project')
                ->get();
        } elseif ($role === 'tm') {
            $teamProjects = ProjectMember::where('user_id', $user->id)
                ->with(['project.pi'])
                ->get();
        } elseif ($role === 'finance') {
            $pendingDisbursements = BudgetRequest::where('status', 'Approved')
                ->where(function ($q) {
                    $q->where('milestone_phase', 'like', 'Tranche%')
                      ->orWhere('milestone_phase', 'Budget Amendment');
                })
                ->whereHas('project', function ($q) {
                    $q->whereIn('status', ['Active', 'Approved', 'Completed']);
                })
                ->with(['project.pi'])
                ->latest()
                ->get();

            $disbursedHistory = BudgetRequest::where('status', 'Released')
                ->with(['project.pi'])
                ->latest()
                ->limit(10)
                ->get();
        } elseif (in_array($role, ['dh', 'coordinator', 'dean', 'vparttcs', 'rcsc', 'irerc'])) {
            $pendingApprovals = Project::where('status', '!=', 'Draft')->where('status', '!=', 'Withdrawn')->with(['pi', 'thematicArea'])->latest()->limit(10)->get();
        }

        $statusDistribution = [
            'In Progress' => Project::whereIn('status', ['Active', 'Approved'])->count(),
            'Submitted'   => Project::whereIn('status', ['Submitted', 'DH_Screened'])->count(),
            'Under Review'=> Project::where('status', 'UnderReview')->count(),
            'On Hold'     => Project::whereIn('status', ['Returned', 'PendingCancellation'])->count(),
            'Completed'   => Project::where('status', 'Completed')->count(),
        ];
        $totalProjectsCount = array_sum($statusDistribution);
        if ($totalProjectsCount === 0) {
            $totalProjectsCount = max(1, Project::count());
        }

        $recentProjectsList = Project::with(['pi', 'thematicArea'])->latest()->limit(5)->get();

        $alerts = \App\Services\AlertsService::all();

        return view('dashboard', compact(
            'user', 'role', 'stats', 'myProjects', 'assignedReviews', 
            'pendingApprovals', 'teamProjects', 'pendingDisbursements', 
            'disbursedHistory', 'recentActivity', 'pendingCancellations', 
            'alerts', 'statusDistribution', 'totalProjectsCount', 'recentProjectsList'
        ));
    }

    private function getRoleSpecificStats($user, $role)
    {
        if ($role === 'pi') {
            $myProjects = Project::where('pi_id', $user->id);
            return [
                'my_projects' => $myProjects->count(),
                'draft' => (clone $myProjects)->where('status', 'Draft')->count(),
                'submitted' => (clone $myProjects)->where('status', 'Submitted')->count(),
                'active' => (clone $myProjects)->where('status', 'Active')->count(),
                'completed' => (clone $myProjects)->where('status', 'Completed')->count(),
                'total_budget' => (clone $myProjects)->sum('requested_budget'),
                'approved_budget' => (clone $myProjects)->sum('approved_budget'),
                'pending_extensions' => ProjectExtension::whereHas('project', fn($q) => $q->where('pi_id', $user->id))->where('status', 'Pending')->count(),
                'next_defence' => \App\Models\PresentationSchedule::whereHas('project', fn($q) => $q->where('pi_id', $user->id))
                    ->where('status', 'Scheduled')
                    ->where('scheduled_at', '>=', now())
                    ->orderBy('scheduled_at')
                    ->first(),
            ];
        } elseif ($role === 'reviewer') {
            return [
                'pending_reviews' => Evaluation::where('examiner_id', $user->id)->where('decision', 'Pending')->count(),
                'pending_proposal_reviews' => Evaluation::where('examiner_id', $user->id)->where(fn($q) => $q->where('evaluation_type', 'Proposal')->orWhereNull('evaluation_type'))->where('decision', 'Pending')->count(),
                'pending_progress_reviews' => Evaluation::where('examiner_id', $user->id)->where('evaluation_type', 'Progress')->where('decision', 'Pending')->count(),
                'pending_final_reviews' => Evaluation::where('examiner_id', $user->id)->where('evaluation_type', 'FinalDefence')->where('decision', 'Pending')->count(),
                'completed_reviews' => Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->count(),
                'avg_score' => Evaluation::where('examiner_id', $user->id)->where('decision', '!=', 'Pending')->avg('score') ?? 0,
            ];
        } elseif ($role === 'dh') {
            return [
                'pending_screening' => Project::where('status', 'Submitted')->where('dept_id', $user->dept_id)->count(),
                'screened_today' => Project::where('status', 'DH_Screened')->where('dept_id', $user->dept_id)->whereDate('updated_at', today())->count(),
                'dept_projects' => Project::where('dept_id', $user->dept_id)->count(),
            ];
        } elseif ($role === 'coordinator') {
            return [
                'need_reviewer' => Project::where('status', 'DH_Screened')->count(),
                'under_review' => Project::where('status', 'UnderReview')->count(),
                'total_projects' => Project::where('status', '!=', 'Draft')->count(),
            ];
        } elseif ($role === 'dean') {
            return [
                'pending_approval' => BudgetRequest::where('approval_tier', 'Dean')->where('status', 'Pending')->count(),
                'approved' => Project::where('requested_budget', '<', 500000)->whereIn('status', ['Approved', 'Active', 'Completed'])->count(),
                'total_value' => Project::where('requested_budget', '<', 500000)->where('status', '!=', 'Draft')->sum('requested_budget'),
            ];
        } elseif ($role === 'irerc') {
            return [
                'pending_ethics' => IRERCClearance::where('status', 'Pending')->count(),
                'cleared' => IRERCClearance::where('status', 'Approved')->count(),
                'total_reviews' => IRERCClearance::count(),
            ];
        } elseif (in_array($role, ['rcsc', 'vparttcs'])) {
            return [
                'rcsc_pending' => BudgetRequest::where('approval_tier', 'RCSC_VP')->where('status', 'Pending')->count(),
                'pending_contracts' => Project::where('status', 'Approved')->whereNull('vp_signature_date')->count(),
                'approved' => Project::whereIn('status', ['Approved', 'Active', 'Completed'])->count(),
                'high_budget' => Project::where('requested_budget', '>=', 500000)->where('status', '!=', 'Draft')->count(),
            ];
        } elseif ($role === 'finance') {
            $pendingRelease = BudgetRequest::where('status', 'Approved')
                ->where(function ($query) {
                    $query->where('milestone_phase', 'like', 'Tranche%')
                        ->orWhere('milestone_phase', 'Budget Amendment');
                })
                ->whereHas('project', fn ($query) => $query->whereIn('status', ['Active', 'Approved', 'Completed']));

            return [
                'pending_release' => (clone $pendingRelease)->count(),
                'disbursed' => BudgetRequest::where('status', 'Released')->sum('approved_amount'),
                'transactions' => BudgetRequest::where('status', 'Released')->count(),
            ];
        } elseif ($role === 'admin') {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'projects' => Project::where('status', '!=', 'Draft')->count(),
                'evaluations' => Evaluation::count(),
                'thematic_areas' => \App\Models\ThematicArea::count(),
                'departments' => \App\Models\Department::count(),
                'audit_logs' => \App\Models\AuditLog::count(),
            ];
        } elseif ($role === 'tm') {
            return [
                'team_projects' => ProjectMember::where('user_id', $user->id)->count(),
                'active_projects' => ProjectMember::where('user_id', $user->id)->whereHas('project', fn($q) => $q->where('status', 'Active'))->count(),
            ];
        } elseif ($role === 'ttcsd') {
            return [
                'total_projects'       => Project::where('status', '!=', 'Draft')->count(),
                'active_projects'      => Project::where('status', 'Active')->count(),
                'delayed_projects'     => Project::where('status', 'Active')->where('activated_at', '<', now()->subMonths(12))->count(),
                'pending_terminations' => \App\Models\ProjectTermination::where('status', 'Pending')->count(),
                'pending_amendments'   => \App\Models\BudgetAmendment::where('status', 'Pending')->count(),
                'pending_extensions'   => \App\Models\ProjectExtension::where('status', 'Pending')->count(),
            ];
        }

        return [
            'total_projects' => Project::where('status', '!=', 'Draft')->count(),
            'active_projects' => Project::where('status', 'Active')->count(),
            'pending_reviews' => Evaluation::where('decision', 'Pending')->count(),
            'pending_budgets' => BudgetRequest::where('status', 'Pending')->count(),
        ];
    }
}
