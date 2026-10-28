<?php

namespace App\Http\Controllers;

use App\Models\BudgetRequest;
use App\Models\Paper;
use App\Models\ProcurementRequest;
use App\Models\Project;
use App\Models\ThematicArea;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Reports Hub index page.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * 1. Annual Research Output Report
     */
    public function annualOutput(Request $request): Response
    {
        $year = (int) $request->input('year', date('Y'));

        $projects = Project::where('status', '!=', 'Draft')
            ->whereYear('created_at', $year)
            ->with(['thematicArea', 'pi', 'department'])
            ->get();

        $papers = Paper::whereYear('created_at', $year)
            ->with(['project', 'author'])
            ->get();

        $totalGrantsValue = $projects->sum('requested_budget');
        $approvedGrantsValue = $projects->sum('approved_budget');
        $completedCount = $projects->where('status', 'Completed')->count();

        $data = [
            'year'                => $year,
            'projects'            => $projects,
            'papers'              => $papers,
            'totalGrantsValue'    => $totalGrantsValue,
            'approvedGrantsValue' => $approvedGrantsValue,
            'completedCount'      => $completedCount,
            'generatedAt'         => now()->format('M d, Y H:i'),
            'generatedBy'         => Auth::user()->name,
            'refNumber'           => "GU-RPMS-AOR-{$year}-" . strtoupper(substr(uniqid(), -4)),
        ];

        $pdf = Pdf::loadView('reports.templates.annual_output', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download("GU_Annual_Research_Output_{$year}.pdf");
    }

    /**
     * 2. Grant Expenditure & Financial Audit Report
     */
    public function financialAudit(Request $request): Response
    {
        $year = (int) $request->input('year', date('Y'));

        $disbursements = BudgetRequest::whereYear('created_at', $year)
            ->where('status', 'Released')
            ->with(['project.pi', 'project.thematicArea'])
            ->get();

        $pendingDisbursements = BudgetRequest::whereYear('created_at', $year)
            ->where('status', 'Approved')
            ->with(['project.pi'])
            ->get();

        $totalDisbursed = $disbursements->sum('approved_amount');
        $totalPending   = $pendingDisbursements->sum('approved_amount');

        $procurements = ProcurementRequest::whereYear('created_at', $year)
            ->whereIn('status', ['Approved', 'Purchased'])
            ->with('project')
            ->get();

        $data = [
            'year'                 => $year,
            'disbursements'        => $disbursements,
            'pendingDisbursements' => $pendingDisbursements,
            'totalDisbursed'       => $totalDisbursed,
            'totalPending'         => $totalPending,
            'procurements'         => $procurements,
            'generatedAt'          => now()->format('M d, Y H:i'),
            'generatedBy'          => Auth::user()->name,
            'refNumber'            => "GU-RPMS-FIN-{$year}-" . strtoupper(substr(uniqid(), -4)),
        ];

        $pdf = Pdf::loadView('reports.templates.financial_audit', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download("GU_Financial_Audit_Report_{$year}.pdf");
    }

    /**
     * 3. Thematic Area Impact Analysis
     */
    public function thematicImpact(Request $request): Response
    {
        $thematics = DB::table('thematic_areas')
            ->leftJoin('projects', function ($join) {
                $join->on('thematic_areas.id', '=', 'projects.thematic_id')
                     ->where('projects.status', '!=', 'Draft');
            })
            ->select(
                'thematic_areas.id',
                'thematic_areas.title',
                'thematic_areas.category',
                DB::raw('count(projects.project_id) as total_projects'),
                DB::raw('sum(case when projects.status = "Active" then 1 else 0 end) as active_projects'),
                DB::raw('sum(case when projects.status = "Completed" then 1 else 0 end) as completed_projects'),
                DB::raw('sum(coalesce(projects.approved_budget, 0)) as total_funding')
            )
            ->groupBy('thematic_areas.id', 'thematic_areas.title', 'thematic_areas.category')
            ->get();

        $data = [
            'thematics'   => $thematics,
            'totalFunded' => $thematics->sum('total_funding'),
            'generatedAt' => now()->format('M d, Y H:i'),
            'generatedBy' => Auth::user()->name,
            'refNumber'   => 'GU-RPMS-THM-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4)),
        ];

        $pdf = Pdf::loadView('reports.templates.thematic_impact', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('GU_Thematic_Area_Impact_Report_' . date('Y') . '.pdf');
    }

    /**
     * 4. Researcher Performance Summary
     */
    public function researcherPerformance(Request $request): Response
    {
        $researchers = User::whereIn('role', ['pi', 'tm'])
            ->has('projects')
            ->withCount([
                'projects as total_proposals',
                'projects as completed_proposals' => function ($q) {
                    $q->where('status', 'Completed');
                },
                'projects as active_proposals' => function ($q) {
                    $q->where('status', 'Active');
                },
            ])
            ->with('department')
            ->orderByDesc('completed_proposals')
            ->limit(50)
            ->get();

        $data = [
            'researchers' => $researchers,
            'generatedAt' => now()->format('M d, Y H:i'),
            'generatedBy' => Auth::user()->name,
            'refNumber'   => 'GU-RPMS-RPR-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4)),
        ];

        $pdf = Pdf::loadView('reports.templates.researcher_performance', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('GU_Researcher_Performance_Summary_' . date('Y') . '.pdf');
    }
<<<<<<< HEAD

    /**
     * 5. Coordinator Operational & Periodic Portfolio Report
     */
    public function coordinatorOperational(Request $request)
    {
        $period = $request->input('period', 'quarterly');
        if (!in_array($period, ['quarterly', 'biannual', 'annual'])) {
            $period = 'quarterly';
        }

        $startDate = match ($period) {
            'quarterly' => now()->subMonths(3),
            'biannual'  => now()->subMonths(6),
            'annual'    => now()->subYear(),
        };

        $user = Auth::user();
        $query = Project::where('status', '!=', 'Draft')
            ->with(['pi', 'department', 'presentationSchedules']);

        if ($user->dept_id && !in_array($user->role, ['admin', 'vparttcs', 'rcsc'])) {
            $query->where('dept_id', $user->dept_id);
        }

        $projects = $query->get();

        $activeProjects = $projects->where('status', 'Active');
        $activeCount = $activeProjects->count();
        $activeBudget = $activeProjects->sum(fn($p) => $p->approved_budget ?: $p->requested_budget);

        $completedProjects = $projects->where('status', 'Completed');
        $completedCount = $completedProjects->count();
        $completedBudget = $completedProjects->sum(fn($p) => $p->approved_budget ?: $p->requested_budget);

        $reviewProjects = $projects->whereIn('status', ['UnderReview', 'DH_Screened', 'Dean_Review', 'RCSC_Review']);
        $reviewCount = $reviewProjects->count();
        $reviewBudget = $reviewProjects->sum('requested_budget');

        $delayedProjects = $activeProjects->filter(fn($p) => $p->activated_at && $p->activated_at < now()->subMonths(12));
        $delayedCount = $delayedProjects->count();
        $delayedBudget = $delayedProjects->sum(fn($p) => $p->approved_budget ?: $p->requested_budget);

        $projectIds = $projects->pluck('project_id');

        $milestonesCount = \App\Models\MilestoneReport::whereIn('project_id', $projectIds)
            ->where('created_at', '>=', $startDate)
            ->count();

        $totalDisbursed = \App\Models\BudgetRequest::whereIn('project_id', $projectIds)
            ->where('status', 'Released')
            ->where('created_at', '>=', $startDate)
            ->sum('approved_amount');

        $challenges = \App\Models\ProjectChallenge::whereIn('project_id', $projectIds)
            ->latest()
            ->limit(15)
            ->get();

        $data = [
            'period'          => $period,
            'startDate'       => $startDate,
            'projects'        => $projects,
            'activeCount'     => $activeCount,
            'activeBudget'    => $activeBudget,
            'completedCount'  => $completedCount,
            'completedBudget' => $completedBudget,
            'reviewCount'     => $reviewCount,
            'reviewBudget'    => $reviewBudget,
            'delayedCount'    => $delayedCount,
            'delayedBudget'   => $delayedBudget,
            'milestonesCount' => $milestonesCount,
            'totalDisbursed'  => $totalDisbursed,
            'challenges'      => $challenges,
            'generatedAt'     => now()->format('M d, Y H:i'),
            'generatedBy'     => $user->name,
            'refNumber'       => 'GU-RPMS-COP-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4)),
        ];

        if ($request->input('format') === 'html' || $request->input('view') === '1') {
            return view('reports.coordinator_operational', $data);
        }

        $pdf = Pdf::loadView('reports.templates.coordinator_operational', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download("GU_Coordinator_Operational_{$period}_" . date('Y') . ".pdf");
    }
=======
>>>>>>> upstream/main
}
