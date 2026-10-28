<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ThematicArea;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Department;
use App\Services\BlindReviewService;
use App\Services\ProjectSearchService;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;

class ProjectController extends Controller
{
    protected $blindReviewService;

    public function __construct(BlindReviewService $blindReviewService)
    {
        $this->blindReviewService = $blindReviewService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'search'        => 'nullable|string|max:255',
            'status'        => 'nullable|string|max:50',
            'thematic_area' => 'nullable',
        ]);

        $query = ProjectSearchService::buildQuery($request, Auth::user());

        if ($request->input('export') === 'csv') {
            return ProjectSearchService::exportCsv($query->get());
        }

        $projects      = $query->paginate(10)->withQueryString();
        $thematicAreas = \App\Models\ThematicArea::where('is_active', true)->get();
        $departments   = \App\Models\Department::orderBy('name')->get();
        $filters       = $request->all();

        return view('projects.index', compact('projects', 'thematicAreas', 'departments', 'filters'));
    }

    public function submit($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'Only the PI can submit their own draft proposal.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected'])) {
            return back()->with('error', 'Only draft, withdrawn, returned, or rejected proposals can be submitted.');
        }

        if (in_array($project->status, ['Returned', 'Rejected', 'Withdrawn'])) {
            Evaluation::where('project_id', $project->project_id)->delete();
            \Illuminate\Support\Facades\DB::table('irerc_clearances')->where('project_id', $project->project_id)->delete();
        }

        $project->update(['status' => 'Submitted', 'current_stage' => 1, 'submitted_at' => now(), 'dh_screened_at' => null, 'under_review_at' => null, 'approved_at' => null, 'activated_at' => null, 'completed_at' => null]);

        \App\Services\AuditService::log('PROPOSAL_SUBMITTED', 'Project', $project->project_id, "PI submitted research proposal '{$project->title}' (ETB " . number_format($project->requested_budget, 2) . ")");

        return back()->with('success', 'Proposal submitted for Department Head screening.');
    }

    public function create()
    {
        $thematicAreas = ThematicArea::where('is_active', true)->get();
        return view('projects.create', compact('thematicAreas'));
    }

    public function store(ProjectStoreRequest $request)
    {
        $user = Auth::user();

        // Handle PDF upload (SDD SCR-03, RSK-02)
        $docPath = null;
        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')
                ->store('proposals', 'public');
        }

        $project = Project::create([
            'title'                 => $request->title,
            'abstract_text'         => $request->abstract_text,
            'thematic_id'           => $request->thematic_id,
            'pi_id'                 => $user->id,
            'dept_id'               => $user->dept_id,
            'requested_budget'      => $request->requested_budget,
            'status'                => 'Draft',
            'current_stage'         => 0,
            'proposal_document_url' => $docPath,
        ]);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Research proposal saved as draft. You can submit it when ready.');
    }

    public function storeDraft(ProjectStoreRequest $request)
    {
        $user = Auth::user();

        $docPath = null;
        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')
                ->store('proposals', 'public');
        }

        $project = Project::create([
            'title'                 => $request->title,
            'abstract_text'         => $request->abstract_text,
            'thematic_id'           => $request->thematic_id,
            'pi_id'                 => $user->id,
            'dept_id'               => $user->dept_id,
            'requested_budget'      => $request->requested_budget,
            'status'                => 'Draft',
            'current_stage'         => 0,
            'proposal_document_url' => $docPath,
        ]);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Draft saved successfully. You can edit or submit it later.');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only edit your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned'])) {
            return back()->with('error', 'Only draft, withdrawn, or returned proposals can be edited.');
        }

        $thematicAreas = \App\Models\ThematicArea::all();

        return view('projects.edit', compact('project', 'thematicAreas'));
    }

    public function update(ProjectUpdateRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only edit your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected'])) {
            return back()->with('error', 'Only draft, withdrawn, returned, or rejected proposals can be edited.');
        }

        $data = [
            'title'            => $request->title,
            'abstract_text'    => $request->abstract_text,
            'thematic_id'      => $request->thematic_id,
            'requested_budget' => $request->requested_budget,
        ];

        if ($request->hasFile('proposal_document')) {
            $docPath = $request->file('proposal_document')
                ->store('proposals', 'public');
            $data['proposal_document_url'] = $docPath;
        }

        $project->update($data);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Proposal updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'pi' || (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only delete your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Withdrawn', 'Returned', 'Rejected'])) {
            return back()->with('error', 'Only draft, withdrawn, returned, or rejected proposals can be deleted.');
        }

        $pid = $project->project_id;

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('procurement_requests')->where('project_id', $pid)->delete();
        DB::table('project_members')->where('project_id', $pid)->delete();
        DB::table('evaluations')->where('project_id', $pid)->delete();
        DB::table('irerc_clearances')->where('project_id', $pid)->delete();
        DB::table('budget_requests')->where('project_id', $pid)->delete();
        DB::table('budget_amendments')->where('project_id', $pid)->delete();
        DB::table('milestone_reports')->where('project_id', $pid)->delete();
        DB::table('project_extensions')->where('project_id', $pid)->delete();
        DB::table('pi_transfers')->where('project_id', $pid)->delete();
        DB::table('project_terminations')->where('project_id', $pid)->delete();
        DB::table('certificates')->where('project_id', $pid)->delete();
        $project->delete();
        DB::statement('PRAGMA foreign_keys = ON');

        return redirect()->route('projects.index')->with('success', 'Draft proposal deleted successfully.');
    }

    public function markComplete($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['coordinator', 'admin'])) {
            abort(403, 'Only coordinators can mark projects as complete.');
        }

        if ($project->status !== 'Active') {
            return back()->with('error', 'Only active projects can be marked as complete.');
        }

        $project->status = 'Completed';
        $project->completed_at = now();
        $project->save();

        return back()->with('success', 'Project marked as completed. A certificate can now be issued.');
    }

    public function show($id)
    {
        $project = Project::with(['pi', 'thematicArea', 'department', 'members.user', 'evaluations', 'budgetRequests', 'extensions'])->findOrFail($id);
        $user = Auth::user();
        $anonymized = null;
        $pendingReviewCount = $project->evaluations->where('decision', 'Pending')->count();

        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only view your own projects.');
        }

        if ($user->role === 'reviewer') {
            $isAssigned = $project->evaluations()->where('examiner_id', $user->id)->exists();
            if (!$isAssigned) {
                abort(403, 'You can only view proposals assigned to you for review.');
            }
            $anonymized = $this->blindReviewService->getAnonymizedProposal($project);
        }

        $workflowGuidance = [
            'Draft' => ['actor' => 'Principal Investigator', 'action' => 'Complete the proposal and submit it for department screening.'],
            'Submitted' => ['actor' => 'Department Head', 'action' => 'Screen the submitted proposal and record the screening decision.'],
            'DH_Screened' => ['actor' => 'Research Coordinator', 'action' => 'Assign peer reviewers to begin the evaluation stage.'],
            'UnderReview' => [
                'actor' => $pendingReviewCount > 0 ? 'Assigned Peer Reviewer(s)' : 'Research Coordinator',
                'action' => $pendingReviewCount > 0
                    ? "{$pendingReviewCount} peer review(s) are still pending."
                    : 'All assigned reviews are complete; record the next governance decision.',
            ],
            'Returned' => ['actor' => 'Principal Investigator', 'action' => 'Address the feedback, update the proposal, and resubmit it.'],
            'Rejected' => ['actor' => 'Principal Investigator', 'action' => 'Review the decision and feedback before deciding whether to revise and resubmit.'],
            'Approved' => ['actor' => 'Principal Investigator / Vice President', 'action' => 'Complete the required ethics clearance and contract signatures before project activation.'],
            'Active' => ['actor' => 'Principal Investigator and Project Team', 'action' => 'Submit progress reports and complete approved project milestones.'],
            'Completed' => ['actor' => 'Research Coordinator', 'action' => 'Issue the completion certificate if one has not already been issued.'],
            'PendingCancellation' => ['actor' => 'Research Administration', 'action' => 'Review and decide the pending withdrawal request.'],
            'Withdrawn' => ['actor' => 'No action pending', 'action' => 'This proposal has been withdrawn.'],
            'Terminated' => ['actor' => 'No action pending', 'action' => 'This project has been terminated.'],
        ][$project->status] ?? ['actor' => 'Research Administration', 'action' => 'Review the project status and contact the responsible office for the next step.'];

        $reviewers = \App\Models\User::where('role', 'reviewer')->get();

        return view('projects.show', compact('project', 'anonymized', 'reviewers', 'workflowGuidance', 'pendingReviewCount'));
    }

    public function coordinatorAssess(Request $request, $id)
    {
        $request->validate([
            'coordinator_assessment' => 'required|string|min:10',
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'coordinator_assessment'  => $request->coordinator_assessment,
            'coordinator_assessed_at' => now(),
        ]);

        \App\Services\AuditService::log('COORDINATOR_ASSESSMENT', 'Project', $project->project_id,
            "Coordinator submitted preliminary assessment for Project #{$project->project_id}");

        return back()->with('success', 'Coordinator preliminary assessment saved. You may now assign peer examiners.');
    }

    public function assignReviewer(Request $request, $id)
    {
        $request->validate([
            'examiner_id'     => 'required|exists:users,id',
            'evaluation_type' => 'nullable|in:Proposal,Progress,FinalDefence',
        ]);

        $project = Project::findOrFail($id);
        $evalType = $request->input('evaluation_type', 'Proposal') ?: 'Proposal';

        if ($evalType === 'Proposal' && !in_array($project->status, ['DH_Screened', 'UnderReview'])) {
            return back()->with('error', 'This project has not been screened by the Department Head yet.');
        }

        $existing = Evaluation::where('project_id', $project->project_id)
            ->where('examiner_id', $request->examiner_id)
            ->where('evaluation_type', $evalType)
            ->first();

        if ($existing) {
            if ($existing->decision === 'Pending') {
                return back()->with('error', 'This reviewer is already assigned for this evaluation type with a pending review.');
            }
            $existing->update([
                'score'             => null,
                'decision'          => 'Pending',
                'final_decision'    => null,
                'revision_comments' => null,
                'comments'          => null,
                'evaluated_at'      => null,
            ]);
        } else {
            Evaluation::create([
                'project_id'      => $project->project_id,
                'examiner_id'     => $request->examiner_id,
                'evaluation_type' => $evalType,
                'score'           => null,
                'decision'        => 'Pending',
                'is_blind_masked' => true,
            ]);
        }

        if ($evalType === 'Proposal' && $project->status === 'DH_Screened') {
            $project->update(['status' => 'UnderReview', 'current_stage' => 2, 'under_review_at' => now()]);
        }

        return back()->with('success', "Examiner assigned successfully for {$evalType} evaluation.");
    }

    public function cancel($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only withdraw your own projects.');
        }

        if (!in_array($project->status, ['Draft', 'Returned', 'Submitted', 'DH_Screened', 'UnderReview'])) {
            return back()->with('error', 'This project cannot be withdrawn at this stage.');
        }

        $project->update([
            'status' => 'Withdrawn',
            'cancelled_at' => now(),
            'cancelled_by_pi' => true,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project withdrawn successfully.');
    }

    public function requestCancel(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only withdraw your own projects.');
        }

        if (!in_array($project->status, ['Approved', 'Active'])) {
            return back()->with('error', 'This project cannot be withdrawn at this stage.');
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $project->update([
            'status' => 'PendingCancellation',
            'cancellation_reason' => $request->reason,
            'cancelled_by_pi' => true,
        ]);

        return back()->with('success', 'Withdrawal request submitted. Awaiting admin approval.');
    }

    public function approveCancel($id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'dean', 'rcsc', 'coordinator'])) {
            abort(403, 'You do not have permission to approve withdrawals.');
        }

        if ($project->status !== 'PendingCancellation') {
            return back()->with('error', 'This project is not pending withdrawal.');
        }

        $project->update([
            'status' => 'Withdrawn',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Project withdrawal approved.');
    }

    public function rejectCancel(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'dean', 'rcsc', 'coordinator'])) {
            abort(403, 'You do not have permission to reject withdrawals.');
        }

        if ($project->status !== 'PendingCancellation') {
            return back()->with('error', 'This project is not pending withdrawal.');
        }

        $previousStatus = $project->approved_at ? 'Approved' : 'UnderReview';

        $project->update([
            'status' => $previousStatus,
            'admin_cancel_notes' => $request->notes ?? null,
        ]);

        return back()->with('success', 'Withdrawal request rejected. Project restored.');
    }
}
