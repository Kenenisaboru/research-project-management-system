<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResearchCallRequest;
use App\Models\Department;
use App\Models\ResearchCall;
use App\Models\ThematicArea;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchCall::with(['department', 'thematicArea', 'postedBy']);

        if ($request->filled('call_type')) {
            $query->where('call_type', $request->input('call_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', '!=', 'Draft');
        }

        $calls = $query->latest()->paginate(12)->withQueryString();

        $departments = Department::orderBy('name')->get();
        $thematicAreas = ThematicArea::where('is_active', true)->orderBy('title')->get();

        return view('calls.index', compact('calls', 'departments', 'thematicAreas'));
    }

    public function store(ResearchCallRequest $request)
    {
        $call = ResearchCall::create($request->validated() + [
            'posted_by' => $request->user()->id,
            'status'    => $request->input('status', 'Open'),
        ]);

        AuditService::log('CALL_CREATED', 'ResearchCall', $call->id, "Call for {$request->input('call_type')} posted: {$call->title}");

        return back()->with('success', 'Research call published successfully.');
    }

    public function update(ResearchCallRequest $request, $id)
    {
        $call = ResearchCall::findOrFail($id);
        $call->update($request->validated());

        AuditService::log('CALL_UPDATED', 'ResearchCall', $call->id, "Research call updated: {$call->title}");

        return back()->with('success', 'Research call updated successfully.');
    }

    public function setStatus(Request $request, $id)
    {
        $request->validate(['status' => ['required', 'in:Open,Closed,Draft']]);

        $call = ResearchCall::findOrFail($id);
        $call->update(['status' => $request->input('status')]);

        AuditService::log('CALL_STATUS_CHANGED', 'ResearchCall', $call->id, "Research call '{$call->title}' status set to {$request->input('status')}");

        return back()->with('success', "Research call set to {$request->input('status')}.");
    }

    public function destroy($id)
    {
        $call = ResearchCall::findOrFail($id);
        $call->delete();

        AuditService::log('CALL_DELETED', 'ResearchCall', $id, "Research call deleted: {$call->title}");

        return back()->with('success', 'Research call removed.');
    }
}