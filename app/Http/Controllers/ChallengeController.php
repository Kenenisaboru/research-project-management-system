<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectChallenge;
use App\Models\ProjectMember;

class ChallengeController extends Controller
{
    public function index($projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)->with('pi')->firstOrFail();

        // Access control
        if ($user->role === 'pi' && (int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only view challenges for your own projects.');
        }
        if ($user->role === 'tm') {
            $isMember = ProjectMember::where('project_id', $projectId)->where('user_id', $user->id)->exists();
            if (!$isMember) {
                abort(403, 'You are not a member of this project.');
            }
        }

        $challenges = ProjectChallenge::where('project_id', $projectId)->latest()->get();

        return view('challenges.index', compact('project', 'challenges'));
    }

    public function store(Request $request, $projectId)
    {
        $user = Auth::user();
        $project = Project::where('project_id', $projectId)->firstOrFail();

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'Only the PI can submit challenges for this project.');
        }

        if (!in_array($project->status, ['Active', 'Approved'])) {
            return back()->with('error', 'Challenges can only be submitted for active or approved projects.');
        }

        $request->validate([
            'challenge_type'    => 'required|in:Technical,Financial',
            'description'       => 'required|string|min:20',
            'proposed_solution' => 'nullable|string',
        ]);

        ProjectChallenge::create([
            'project_id'        => $projectId,
            'pi_id'             => $user->id,
            'challenge_type'    => $request->challenge_type,
            'description'       => $request->description,
            'proposed_solution' => $request->proposed_solution,
            'status'            => 'Pending',
        ]);

        \App\Services\AuditService::log('CHALLENGE_SUBMITTED', 'ProjectChallenge', $projectId,
            "PI submitted a {$request->challenge_type} challenge for Project #{$projectId}");

        return back()->with('success', 'Challenge submitted to the coordinator successfully.');
    }

    public function acknowledge(Request $request, $id)
    {
        $request->validate([
            'coordinator_response' => 'required|string',
            'status'               => 'required|in:Acknowledged,Resolved',
        ]);

        $challenge = ProjectChallenge::findOrFail($id);
        $challenge->update([
            'status'               => $request->status,
            'coordinator_response' => $request->coordinator_response,
        ]);

        return back()->with('success', 'Challenge ' . strtolower($request->status) . ' successfully.');
    }
}
