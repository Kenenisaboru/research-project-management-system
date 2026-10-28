@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-clipboard-check me-2 text-dark"></i>Blind Peer Review</h3>

@if($evaluation->is_blind_masked)
<div class="alert alert-secondary border-start border-4 border-dark mb-4">
    <i class="bi bi-eye-slash me-2 text-dark"></i>
    <strong>Double-Blind Review Active:</strong> PI identity is hidden to ensure impartial evaluation.
</div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 p-4 mb-4">
            <h5 class="fw-bold mb-3">Proposal Details</h5>
            <p><strong>Project ID:</strong> #{{ $evaluation->project_id }}</p>
            <p><strong>Title:</strong> {{ $evaluation->project->title ?? 'N/A' }}</p>
            <p><strong>Abstract:</strong></p>
            <div class="bg-light p-3 rounded">{{ $evaluation->project->abstract_text ?? 'N/A' }}</div>
            @if($evaluation->project->proposal_document_url)
            <p class="mt-3"><a href="{{ Storage::url($evaluation->project->proposal_document_url) }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-pdf me-1"></i>View Proposal PDF</a></p>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold mb-3">Evaluation Rubric</h5>

            @if($evaluation->decision !== 'Pending')
            <div class="p-3 bg-light rounded border mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Evaluation Type:</span>
                    <span class="badge bg-dark">{{ $evaluation->evaluation_type ?? 'Proposal' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Score:</span>
                    <span class="fw-bold fs-5">{{ $evaluation->score !== null ? $evaluation->score . ' / 100' : 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Decision:</span>
                    <span class="badge bg-secondary">{{ $evaluation->decision }}</span>
                </div>
                @if($evaluation->final_decision)
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Final Verdict:</span>
                    <span class="badge bg-success">{{ $evaluation->final_decision }}</span>
                </div>
                @endif
                @if($evaluation->revision_comments)
                <div class="mb-2">
                    <span class="small text-muted d-block mb-1">Revision Comments:</span>
                    <p class="small mb-0 text-muted">{{ $evaluation->revision_comments }}</p>
                </div>
                @endif
                <div class="mb-2">
                    <span class="small text-muted d-block mb-1">Comments:</span>
                    <p class="small mb-0">{{ $evaluation->comments }}</p>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-muted">Evaluated:</span>
                    <span class="small">{{ $evaluation->evaluated_at ? $evaluation->evaluated_at->format('M d, Y H:i') : 'N/A' }}</span>
                </div>
            </div>
            @else
                @if(($evaluation->evaluation_type ?? 'Proposal') === 'FinalDefence')
                    @include('evaluations.form_final')
                @elseif(($evaluation->evaluation_type ?? 'Proposal') === 'Progress')
                    @include('evaluations.form_progress')
                @else
                    @include('evaluations.form_proposal')
                @endif
            @endif
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('evaluations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Assignments</a>
</div>
@endsection
