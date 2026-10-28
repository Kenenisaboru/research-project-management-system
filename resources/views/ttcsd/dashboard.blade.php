@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-2 text-dark"></i>TTCSD / RPED Directorate Portal</h3>
        <p class="text-muted mb-0 small">Technology Transfer &amp; Community Services Directorate — Project oversight, terminations, and budget adjustments.</p>
    </div>
    <a href="{{ route('schedules.index') }}" class="btn btn-outline-dark fw-bold btn-sm">
        <i class="bi bi-calendar3 me-1"></i> Defence Timetable
    </a>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <span class="text-muted small fw-bold text-uppercase">Total Research Portfolio</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalProjects }}</h3>
            <small class="text-muted">Registered projects</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <span class="text-muted small fw-bold text-uppercase">Active Projects</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ $activeProjects }}</h3>
            <small class="text-muted">In execution</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <span class="text-muted small fw-bold text-uppercase">Delayed / At-Risk</span>
            <h3 class="fw-bold text-danger mt-1 mb-0">{{ $delayedProjects->count() }}</h3>
            <small class="text-muted">&gt; 12 months active</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <span class="text-muted small fw-bold text-uppercase">Pending Actions</span>
            <h3 class="fw-bold text-warning mt-1 mb-0">{{ $pendingTerminations->count() + $pendingAmendments->count() + $pendingExtensions->count() }}</h3>
            <small class="text-muted">Terminations / Amendments</small>
        </div>
    </div>
</div>

{{-- Delayed & At-Risk Projects Table --}}
<div class="card card-custom mb-4 border-start border-4 border-danger">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Delayed &amp; At-Risk Projects ({{ $delayedProjects->count() }})</h5>
        <span class="badge bg-danger">Requires TTCSD Intervention</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Project Title</th>
                    <th>PI &amp; Department</th>
                    <th>Activated Date</th>
                    <th>Budget</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($delayedProjects as $dp)
                <tr>
                    <td>#{{ $dp->project_id }}</td>
                    <td class="fw-bold">
                        <a href="{{ route('ttcsd.project', $dp->project_id) }}" class="text-decoration-none text-dark">
                            {{ $dp->title }}
                        </a>
                    </td>
                    <td>{{ $dp->pi->name ?? 'N/A' }} <small class="text-muted d-block">{{ $dp->department->name ?? '' }}</small></td>
                    <td>{{ $dp->activated_at ? $dp->activated_at->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ number_format($dp->approved_budget ?: $dp->requested_budget, 2) }} ETB</td>
                    <td>
                        <a href="{{ route('ttcsd.project', $dp->project_id) }}" class="btn btn-outline-danger btn-sm fw-bold">
                            <i class="bi bi-search me-1"></i> Inspect &amp; Audit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No projects currently flagged as delayed past 12 months.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pending Governance Action Queues (Terminations, Amendments, Extensions) --}}
<div class="row g-4 mb-4">
    {{-- Terminations --}}
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-x-octagon text-danger me-2"></i>Pending Termination Requests ({{ $pendingTerminations->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($pendingTerminations as $term)
                    <li class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $term->project->title ?? 'Project' }}</h6>
                                <small class="text-muted">PI: {{ $term->project->pi->name ?? 'N/A' }} &bull; Reason: {{ $term->reason }}</small>
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                        <form action="{{ route('ttcsd.termination.decision', $term->id) }}" method="POST" class="d-flex gap-2 mt-2">
                            @csrf
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Add decision note...">
                            <button type="submit" name="decision" value="Approved" class="btn btn-danger btn-sm text-nowrap">Approve Termination</button>
                            <button type="submit" name="decision" value="Rejected" class="btn btn-outline-secondary btn-sm text-nowrap">Reject</button>
                        </form>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4">No pending project terminations.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Amendments --}}
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-cash-coin text-warning me-2"></i>Pending Budget Amendments ({{ $pendingAmendments->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($pendingAmendments as $amend)
                    <li class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $amend->project->title ?? 'Project' }}</h6>
                                <small class="text-muted">PI: {{ $amend->project->pi->name ?? 'N/A' }} &bull; Delta: <strong>+{{ number_format($amend->delta_amount, 2) }} ETB</strong></small>
                                <div class="small text-secondary">{{ $amend->justification }}</div>
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                        <form action="{{ route('ttcsd.amendment.decision', $amend->id) }}" method="POST" class="d-flex gap-2 mt-2">
                            @csrf
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Review note...">
                            <button type="submit" name="decision" value="Approved" class="btn btn-success btn-sm text-nowrap">Approve</button>
                            <button type="submit" name="decision" value="Rejected" class="btn btn-outline-secondary btn-sm text-nowrap">Reject</button>
                        </form>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4">No pending budget amendments.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
