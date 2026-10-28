@extends('layouts.app')

@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-award me-2 text-dark"></i>RCSC High Budget (&ge;500k ETB) &amp; Defence Portal</h3>

{{-- High Budget Proposal Financial Review Section --}}
<h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>High-Budget Proposals Review (&ge;500k ETB) ({{ $proposals->count() }})</h5>
<div class="row g-3 mb-4">
    @forelse($proposals as $prop)
    <div class="col-12">
        <div class="card card-custom p-4 border-start border-4 border-primary">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <span class="badge bg-primary me-2">{{ $prop->status }}</span>
                    <span class="badge bg-secondary">{{ $prop->thematicArea->name ?? 'General' }}</span>
                    <h5 class="fw-bold text-dark mt-2 mb-1">
                        <a href="{{ route('projects.show', $prop->project_id) }}" class="text-decoration-none text-dark hover-primary">
                            {{ $prop->title }}
                        </a>
                    </h5>
                    <div class="small text-muted">
                        <i class="bi bi-person me-1"></i>PI: <strong>{{ $prop->pi->name ?? 'N/A' }}</strong> ({{ $prop->pi->email ?? '' }})
                        @if($prop->project_code)
                        <span class="ms-3"><i class="bi bi-hash me-1"></i>Code: <code>{{ $prop->project_code }}</code></span>
                        @endif
                    </div>
                </div>
                <div class="text-md-end">
                    <span class="d-block small text-muted">Requested Budget</span>
                    <span class="fs-4 fw-bold text-success">{{ number_format($prop->requested_budget, 2) }} ETB</span>
                </div>
            </div>

            {{-- Abstract with collapsible text --}}
            <div class="mb-3">
                <label class="small fw-bold text-muted d-block mb-1">Proposal Abstract:</label>
                <div class="p-3 bg-light rounded-3 text-secondary small" style="line-height: 1.6;">
                    {{ Str::limit($prop->abstract_text, 280) }}
                    @if(strlen($prop->abstract_text) > 280)
                    <div class="collapse mt-2" id="abstractMore{{ $prop->project_id }}">
                        {{ substr($prop->abstract_text, 280) }}
                    </div>
                    <a class="text-primary text-decoration-none fw-bold ms-1" data-bs-toggle="collapse" href="#abstractMore{{ $prop->project_id }}" role="button" aria-expanded="false">
                        Read more / less
                    </a>
                    @endif
                </div>
            </div>

            {{-- Team Members --}}
            <div class="mb-3">
                <label class="small fw-bold text-muted d-block mb-1">Research Team Members:</label>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border"><i class="bi bi-person-badge me-1"></i>{{ $prop->pi->name ?? 'PI' }} (Principal Investigator)</span>
                    @forelse($prop->members as $member)
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-person me-1"></i>{{ $member->user->name ?? $member->name ?? 'Member' }}
                        @if($member->role_in_project) ({{ $member->role_in_project }}) @endif
                    </span>
                    @empty
                    <span class="small text-muted fst-italic">No additional co-investigators registered.</span>
                    @endforelse
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                @if($prop->proposal_document_url)
                <a href="{{ Storage::url($prop->proposal_document_url) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-file-pdf me-1 text-danger"></i> View Full Proposal Document
                </a>
                @else
                <span class="small text-muted">No PDF attached</span>
                @endif

                <a href="{{ route('projects.show', $prop->project_id) }}" class="btn btn-dark btn-sm">
                    <i class="bi bi-eye me-1"></i> Inspect Project Details &amp; Evaluations
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-custom p-4 text-center text-muted">
            <i class="bi bi-check2-all fs-1 mb-2 text-muted"></i>
            No high-budget proposals currently pending RCSC ratification.
        </div>
    </div>
    @endforelse
</div>

<h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pending RCSC Budget Requests ({{ $pending->count() }})</h5>
<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Project</th>
                    <th>Project Budget</th>
                    <th>Phase</th>
                    <th>Requested Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $r)
                <tr>
                    <td>#REQ-{{ $r->request_id }}</td>
                    <td class="fw-bold">{{ $r->project->title ?? 'N/A' }}
                        @if($r->project && $r->project->proposal_document_url)
                        <a href="{{ Storage::url($r->project->proposal_document_url) }}" target="_blank" class="text-decoration-none ms-1" title="View Proposal PDF">
                            <i class="bi bi-file-pdf text-danger small"></i>
                        </a>
                        @endif
                    </td>
                    <td><span class="fw-bold text-dark">{{ number_format($r->project->requested_budget ?? 0, 2) }} ETB</span></td>
                    <td>Phase {{ $r->milestone_phase }}</td>
                    <td>{{ number_format($r->requested_amount, 2) }} ETB</td>
                    <td><span class="badge bg-warning text-dark">{{ $r->status }}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#rcscDecisionModal{{ $r->request_id }}">
                            <i class="bi bi-check2-square"></i> Review
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No pending RCSC budget requests.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h5 class="fw-bold mb-3"><i class="bi bi-check-circle me-2 text-success"></i>Completed RCSC Decisions ({{ $completed->count() }})</h5>
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Request ID</th>
                    <th>Project</th>
                    <th>Requested Amount</th>
                    <th>Approved Amount</th>
                    <th>Decision</th>
                    <th>Decided At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completed as $r)
                <tr>
                    <td>#REQ-{{ $r->request_id }}</td>
                    <td>{{ $r->project->title ?? 'N/A' }}</td>
                    <td>{{ number_format($r->requested_amount, 2) }} ETB</td>
                    <td>{{ $r->approved_amount ? number_format($r->approved_amount, 2) . ' ETB' : 'N/A' }}</td>
                    <td>
                        @if($r->status === 'Approved')
                            <span class="badge bg-success">{{ $r->status }}</span>
                        @else
                            <span class="badge bg-danger">{{ $r->status }}</span>
                        @endif
                    </td>
                    <td>{{ $r->updated_at ? $r->updated_at->format('M d, Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                        No completed RCSC decisions yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pending as $r)
<div class="modal fade" id="rcscDecisionModal{{ $r->request_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('rcsc.decision', $r->request_id) }}">
                @csrf
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold" style="white-space:normal;">
                        <i class="bi bi-award me-2"></i>RCSC Budget Decision — {{ $r->project->title ?? 'N/A' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Project</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $r->project->title ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Project Budget</label>
                            <input type="text" class="form-control form-control-sm fw-bold text-dark" value="{{ number_format($r->project->requested_budget ?? 0, 2) }} ETB" disabled>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Phase</label>
                            <input type="text" class="form-control form-control-sm" value="Phase {{ $r->milestone_phase }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">Requested Amount</label>
                            <input type="text" class="form-control form-control-sm" value="{{ number_format($r->requested_amount, 2) }} ETB" disabled>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">RCSC Decision <span class="text-danger">*</span></label>
                        <select name="decision" class="form-select form-select-sm" required id="rcscDecision{{ $r->request_id }}" onchange="toggleRcscSubmitBtn({{ $r->request_id }})">
                            <option value="">-- Select Decision --</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Approved Amount (ETB)</label>
                        <input type="number" name="approved_amount" class="form-control form-control-sm" min="0" step="0.01" placeholder="{{ $r->requested_amount }}">
                        <div class="form-text small">Leave blank if approving full amount.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Comments</label>
                        <textarea name="comments" class="form-control form-control-sm" rows="2" placeholder="Optional comments on the decision..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark fw-bold px-3 btn-sm confirm-btn" id="rcscSubmitBtn{{ $r->request_id }}" data-confirm-title="Submit RCSC Decision" data-confirm-message="Submit your RCSC budget decision? This action cannot be undone." data-confirm-icon="bi-check-circle" data-confirm-color="text-dark" data-confirm-btn-text="Yes, Submit" data-confirm-btn-class="btn-dark">
                        <i class="bi bi-check-circle me-1"></i> Submit RCSC Decision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function toggleRcscSubmitBtn(id) {
    var sel = document.getElementById('rcscDecision' + id);
    var btn = document.getElementById('rcscSubmitBtn' + id);
    if (sel.value === 'Approved') {
        btn.className = 'btn btn-success fw-bold px-4 confirm-btn';
    } else if (sel.value === 'Rejected') {
        btn.className = 'btn btn-danger fw-bold px-4 confirm-btn';
    } else {
        btn.className = 'btn btn-dark fw-bold px-4 confirm-btn';
    }
}
</script>
@endsection
