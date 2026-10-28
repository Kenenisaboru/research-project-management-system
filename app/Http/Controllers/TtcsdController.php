<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\BudgetAmendment;
use App\Models\ProjectExtension;
use App\Models\ProjectTermination;
use App\Models\MilestoneReport;
use App\Models\Department;

class TtcsdController extends Controller
{
    public function dashboard()
    {
        $totalProjects      = Project::where('status', '!=', 'Draft')->count();
        $activeProjects     = Project::where('status', 'Active')->count();

        // Delayed: active for more than 12 months without completion
        $delayedProjects    = Project::where('status', 'Active')
            ->where('activated_at', '<', now()->subMonths(12))
            ->with('pi', 'department')
            ->get();

        $pendingTerminations = ProjectTermination::where('status', 'Pending')
            ->with('project.pi')
            ->latest()
            ->get();

        $pendingAmendments   = BudgetAmendment::where('status', 'Pending')
            ->with('project.pi')
            ->latest()
            ->get();

        $pendingExtensions   = ProjectExtension::where('status', 'Pending')
            ->with('project.pi')
            ->latest()
            ->get();

        $recentProjects = Project::whereIn('status', ['Active', 'Approved'])
            ->where('requested_budget', '>=', 500000)
            ->with(['pi', 'department', 'thematicArea'])
            ->latest()
            ->limit(10)
            ->get();

        return view('ttcsd.dashboard', compact(
            'totalProjects', 'activeProjects', 'delayedProjects',
            'pendingTerminations', 'pendingAmendments', 'pendingExtensions', 'recentProjects'
        ));
    }

    public function inspectProject($id)
    {
        $project = Project::where('project_id', $id)
            ->with([
                'pi', 'department', 'thematicArea',
                'members.user', 'milestoneReports',
                'budgetRequests', 'budgetAmendments',
                'extensions', 'termination', 'evaluations.examiner',
            ])
            ->firstOrFail();

        $challenges = \App\Models\ProjectChallenge::where('project_id', $id)->latest()->get();

        return view('ttcsd.project_inspect', compact('project', 'challenges'));
    }

    public function processTermination(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:Approved,Rejected',
            'notes'    => 'nullable|string',
        ]);

        $termination = ProjectTermination::findOrFail($id);
        $termination->update(['status' => $request->decision]);

        if ($request->decision === 'Approved') {
            Project::where('project_id', $termination->project_id)
                ->update(['status' => 'Cancelled', 'cancelled_at' => now()]);
        }

        \App\Services\AuditService::log('TTCSD_TERMINATION_DECISION', 'ProjectTermination', $id,
            "TTCSD " . $request->decision . " termination for project #{$termination->project_id}. Notes: " . $request->notes);

        return back()->with('success', "Termination request {$request->decision} by TTCSD.");
    }

    public function processBudgetAmendment(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:Approved,Rejected',
            'notes'    => 'nullable|string',
        ]);

        $amendment = BudgetAmendment::findOrFail($id);
        $amendment->update([
            'status'      => $request->decision,
            'approved_by' => Auth::id(),
        ]);

        if ($request->decision === 'Approved') {
            $project = Project::where('project_id', $amendment->project_id)->first();
            if ($project) {
                $oldBudget = $project->approved_budget ?: $project->requested_budget;
                $project->update(['approved_budget' => $oldBudget + $amendment->delta_amount]);
            }
        }

        return back()->with('success', "Budget amendment {$request->decision} by TTCSD.");
    }

    public function processExtension(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:Approved,Rejected',
            'notes'    => 'nullable|string',
        ]);

        $extension = ProjectExtension::findOrFail($id);
        $extension->update([
            'status'      => $request->decision,
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', "Time extension {$request->decision} by TTCSD.");
    }
}
