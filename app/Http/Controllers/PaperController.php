<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaperEvaluationRequest;
use App\Http\Requests\PaperSubmitRequest;
use App\Models\Award;
use App\Models\Paper;
use App\Models\PaperEvaluation;
use App\Models\Project;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    public function index(Request $request)
    {
        $user = \Auth::user();
        $query = Paper::with(['submitter', 'evaluations', 'project']);

        if (in_array($user->role, ['pi', 'tm'])) {
            $query->where('submitter_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('journal_index')) {
            $query->where('journal_index', $request->input('journal_index'));
        }

        if ($request->filled('q')) {
            $query->where('title', 'LIKE', '%' . $request->input('q') . '%');
        }

        $papers = $query->latest()->paginate(12)->withQueryString();

        $pendingEvaluationCount = PaperEvaluation::where('evaluator_id', $user->id)->count();
        $awards = in_array($user->role, ['vparttcs', 'rcsc', 'admin', 'coordinator'])
            ? Award::with(['paper', 'recipient'])->latest()->get()
            : collect();

        return view('papers.index', compact('papers', 'pendingEvaluationCount', 'awards'));
    }

    public function create()
    {
        $projects = Project::where('pi_id', \Auth::id())
            ->whereIn('status', ['Completed', 'Active', 'Ongoing'])
            ->orWhere('status', 'Completed')
            ->get()
            ->unique('project_id');

        return view('papers.create', compact('projects'));
    }

    public function store(PaperSubmitRequest $request)
    {
        $filePath = null;
        if ($request->hasFile('paper_file')) {
            $filePath = $request->file('paper_file')->store('papers', 'public');
        }

        $paper = Paper::create([
            'project_id'       => $request->input('project_id'),
            'submitter_id'     => $request->user()->id,
            'title'            => $request->input('title'),
            'abstract_text'    => $request->input('abstract_text'),
            'authors'          => $request->input('authors'),
            'journal_name'     => $request->input('journal_name'),
            'journal_index'    => $request->input('journal_index'),
            'paper_file_path'  => $filePath,
            'publication_date' => $request->input('publication_date'),
            'status'           => 'Submitted',
        ]);

        AuditService::log('PAPER_SUBMITTED', 'Paper', $paper->id, "Publication paper submitted: {$paper->title}");

        \App\Services\NotificationService::notifyManagers('PAPER_SUBMISSION', "New publication submitted: {$paper->title}", ['coordinator', 'vparttcs', 'rcsc']);

        return redirect()->route('papers.show', $paper->id)->with('success', 'Paper registered for journal-index evaluation.');
    }

    public function show($id)
    {
        $paper = Paper::with(['submitter', 'evaluations.evaluator', 'awards.recipient', 'project'])->findOrFail($id);

        $user = \Auth::user();
        if (in_array($user->role, ['pi', 'tm']) && (int) $paper->submitter_id !== (int) $user->id) {
            abort(403, 'You can only view papers you submitted.');
        }

        $hasEvaluated = PaperEvaluation::where('paper_id', $paper->id)->where('evaluator_id', $user->id)->exists();
        $canEvaluate = in_array($user->role, ['reviewer', 'coordinator', 'vparttcs', 'rcsc', 'admin']);

        return view('papers.show', compact('paper', 'hasEvaluated', 'canEvaluate'));
    }

    public function evaluate(PaperEvaluationRequest $request, $id)
    {
        $paper = Paper::findOrFail($id);

        if ($paper->status === 'Approved' || $paper->status === 'Recorded' || $paper->status === 'Rejected') {
            return back()->with('error', 'This paper is no longer in evaluation.');
        }

        PaperEvaluation::updateOrCreate(
            ['paper_id' => $paper->id, 'evaluator_id' => $request->user()->id],
            [
                'decision' => $request->input('decision'),
                'score'    => $request->input('score'),
                'comments' => $request->input('comments'),
            ]
        );

        if ($paper->status === 'Submitted') {
            $paper->update(['status' => 'UnderEvaluation']);
        }

        AuditService::log('PAPER_EVALUATED', 'Paper', $paper->id, "Evaluator {$request->user()->id} scored {$request->input('score')} ({$request->input('decision')}) on {$paper->title}");

        return back()->with('success', 'Evaluation submitted successfully.');
    }

    public function approve($id)
    {
        $paper = Paper::with('evaluations')->findOrFail($id);
        $paper->update([
            'status'             => 'Approved',
            'evaluation_summary' => $paper->evaluations->pluck('comments')->unique()->implode(' | '),
        ]);

        AuditService::log('PAPER_APPROVED', 'Paper', $paper->id, "Paper approved for recording: {$paper->title}");

        \App\Services\NotificationService::notifyManagers('PAPER_APPROVED', "Paper approved: {$paper->title}", ['pi', 'tm']);

        return back()->with('success', 'Paper approved. It is now eligible for award and recording.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => ['required', 'string']]);

        $paper = Paper::findOrFail($id);
        $paper->update([
            'status'             => 'Rejected',
            'evaluation_summary' => $request->input('reason'),
        ]);

        AuditService::log('PAPER_REJECTED', 'Paper', $paper->id, "Paper rejected: {$paper->title} — {$request->input('reason')}");

        return back()->with('success', 'Paper rejected and returned to the submitter.');
    }

    public function award(Request $request, $id)
    {
        $request->validate([
            'recipient_id'  => ['required', 'exists:users,id'],
            'award_name'    => ['required', 'string', 'max:255'],
            'award_amount'  => ['required', 'numeric', 'min:0'],
            'citation'      => ['nullable', 'string'],
            'awarded_at'    => ['required', 'date'],
        ]);

        $paper = Paper::findOrFail($id);

        Award::create([
            'paper_id'     => $paper->id,
            'recipient_id' => $request->input('recipient_id'),
            'awarded_by'   => $request->user()->id,
            'award_name'   => $request->input('award_name'),
            'award_amount' => $request->input('award_amount'),
            'citation'     => $request->input('citation'),
            'awarded_at'   => $request->input('awarded_at'),
        ]);

        $paper->update(['award_status' => 'Awarded']);

        AuditService::log('PAPER_AWARDED', 'Paper', $paper->id, "Award '{$request->input('award_name')}' recorded for {$paper->title}");

        \App\Services\NotificationService::notifyUser((int) $request->input('recipient_id'), 'AWARD', "Congratulations! You received the award: {$request->input('award_name')}");

        return back()->with('success', 'Award recorded successfully.');
    }

    public function record($id)
    {
        $paper = Paper::findOrFail($id);

        if ($paper->status !== 'Approved') {
            return back()->with('error', 'Only approved papers can be recorded.');
        }

        $paper->update(['status' => 'Recorded']);

        AuditService::log('PAPER_RECORDED', 'Paper', $paper->id, "Paper recorded in university publication registry: {$paper->title}");

        return back()->with('success', 'Paper recorded in the university publication registry.');
    }

    public function destroy($id)
    {
        $paper = Paper::findOrFail($id);
        $paper->delete();

        AuditService::log('PAPER_DELETED', 'Paper', $id, "Paper removed: {$paper->title}");

        return back()->with('success', 'Paper record removed.');
    }
}