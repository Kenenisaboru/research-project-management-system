<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PresentationSchedule;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Evaluation;
use App\Models\Department;

class PresentationScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = PresentationSchedule::with(['project.pi', 'arranger'])->orderBy('scheduled_at');

        if ($user->role === 'pi') {
            $query->whereHas('project', fn($q) => $q->where('pi_id', $user->id));
        } elseif ($user->role === 'reviewer') {
            $assignedProjectIds = Evaluation::where('examiner_id', $user->id)->pluck('project_id');
            $query->whereIn('project_id', $assignedProjectIds);
        } elseif (in_array($user->role, ['dh'])) {
            $query->whereHas('project', fn($q) => $q->where('dept_id', $user->dept_id));
        } elseif ($user->role === 'tm') {
            $memberProjectIds = ProjectMember::where('user_id', $user->id)->pluck('project_id');
            $query->whereIn('project_id', $memberProjectIds);
        }
        // coordinator, dean, vparttcs, rcsc, admin, ttcsd see all

        $upcoming = (clone $query)->where('status', 'Scheduled')
            ->where('scheduled_at', '>=', now())
            ->get();

        $past = (clone $query)->where(function ($q) {
            $q->where('status', '!=', 'Scheduled')
              ->orWhere('scheduled_at', '<', now());
        })->latest('scheduled_at')->limit(50)->get();

        $projects = [];
        if (in_array($user->role, ['coordinator', 'admin'])) {
            $projects = Project::whereIn('status', ['Approved', 'Active', 'UnderReview', 'DH_Screened'])
                ->with('pi')
                ->orderBy('title')
                ->get();
        }

        return view('schedules.index', compact('upcoming', 'past', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'   => 'required|exists:projects,project_id',
            'type'         => 'required|in:InitialDefence,BudgetApproval,ProgressReport,FinalDefence',
            'scheduled_at' => 'required|date|after:now',
            'venue'        => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $schedule = PresentationSchedule::create([
            'project_id'   => $request->project_id,
            'type'         => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'venue'        => $request->venue,
            'notes'        => $request->notes,
            'arranged_by'  => Auth::id(),
            'status'       => 'Scheduled',
        ]);

        \App\Services\AuditService::log(
            'PRESENTATION_SCHEDULED',
            'PresentationSchedule',
            $schedule->id,
            'Coordinator arranged ' . $schedule->type . ' for project #' . $schedule->project_id
        );

        return back()->with('success', 'Presentation schedule created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scheduled_at' => 'required|date',
            'venue'        => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:Scheduled,Completed,Cancelled',
        ]);

        $schedule = PresentationSchedule::findOrFail($id);
        $schedule->update($request->only(['scheduled_at', 'venue', 'notes', 'status']));

        return back()->with('success', 'Presentation schedule updated.');
    }

    public function destroy($id)
    {
        $schedule = PresentationSchedule::findOrFail($id);
        $schedule->update(['status' => 'Cancelled']);
        return back()->with('success', 'Presentation schedule cancelled.');
    }
}
