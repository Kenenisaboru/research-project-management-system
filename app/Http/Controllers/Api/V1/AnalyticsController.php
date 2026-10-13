<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\BudgetRequest;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\Paper;
use App\Models\Project;
use App\Models\ThematicArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class AnalyticsController
 *
 * Provides aggregated institutional research analytics and financial KPIs for API consumers.
 */
class AnalyticsController extends ApiController
{
    /**
     * Retrieve institutional research and financial analytics summary.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Projects breakdown by status
        $statusBreakdown = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 2. Financial allocations vs disbursements
        $totalRequested = (float) Project::where('status', '!=', 'Draft')->sum('requested_budget');
        $totalApproved  = (float) Project::whereNotNull('approved_budget')->sum('approved_budget');
        $totalDisbursed = (float) BudgetRequest::where('status', 'Released')->sum('approved_amount');

        // 3. Projects per thematic area
        $thematicDistribution = ThematicArea::withCount('projects')
            ->get()
            ->map(fn ($area) => [
                'id'             => $area->id,
                'title'          => $area->title,
                'projects_count' => $area->projects_count,
            ]);

        // 4. Research publication outputs
        $totalPapers = Paper::count();
        $indexedPapers = Paper::whereNotIn('journal_index', ['None', ''])->whereNotNull('journal_index')->count();

        return $this->success([
            'overview' => [
                'total_projects'  => Project::where('status', '!=', 'Draft')->count(),
                'active_projects' => Project::where('status', 'Active')->count(),
                'completed'       => Project::where('status', 'Completed')->count(),
                'total_papers'    => $totalPapers,
                'indexed_papers'  => $indexedPapers,
            ],
            'financials' => [
                'total_requested_etb' => $totalRequested,
                'total_approved_etb'  => $totalApproved,
                'total_disbursed_etb' => $totalDisbursed,
                'disbursement_rate'   => $totalApproved > 0 ? round(($totalDisbursed / $totalApproved) * 100, 2) : 0,
            ],
            'status_distribution'   => $statusBreakdown,
            'thematic_distribution' => $thematicDistribution,
        ], 'Institutional analytics retrieved successfully.');
    }
}
