<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Services\LifecycleChangeService;
use App\Http\Requests\ExtensionRequestRequest;
use App\Http\Requests\AmendmentRequestRequest;
use App\Http\Requests\TerminationRequestRequest;
use Exception;

class LifecycleController extends Controller
{
    protected $lifecycleService;

    public function __construct(LifecycleChangeService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    public function requestExtension(ExtensionRequestRequest $request, $projectId)
    {
        $user = Auth::user();
        $project = Project::findOrFail($projectId);

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only request extensions for your own projects.');
        }

        if (!in_array($project->status, ['Active', 'Approved'])) {
            return back()->with('error', 'Extensions can only be requested for Active or Approved projects.');
        }


        try {
            $extension = $this->lifecycleService->requestExtension($project, $request->reason, $request->months);
            return back()->with('success', "Extension request #{$extension->extension_number} submitted for {$extension->approver_role} approval.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function requestAmendment(AmendmentRequestRequest $request, $projectId)
    {
        $user = Auth::user();
        $project = Project::findOrFail($projectId);

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only request amendments for your own projects.');
        }

        if (!in_array($project->status, ['Active', 'Approved'])) {
            return back()->with('error', 'Budget amendments can only be requested for Active or Approved projects.');
        }


        \App\Models\BudgetAmendment::create([
            'project_id' => $project->project_id,
            'delta_amount' => $request->delta_amount,
            'justification' => $request->justification,
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Budget amendment request submitted for RCSC/Coordinator approval.');
    }

    public function terminate(TerminationRequestRequest $request, $projectId)
    {
        $user = Auth::user();
        $project = Project::findOrFail($projectId);

        if ((int) $project->pi_id !== (int) $user->id) {
            abort(403, 'You can only terminate your own projects.');
        }

        if (!in_array($project->status, ['Active', 'Approved'])) {
            return back()->with('error', 'Termination can only be requested for Active or Approved projects.');
        }


        $termination = $this->lifecycleService->terminateProject($project, $request->reason, $request->verified_value);

        return back()->with('success', "Project terminated gracefully. Refund due to Finance Office: {$termination->refund_due} ETB.");
    }
}
