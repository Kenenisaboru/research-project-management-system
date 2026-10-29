@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-dark"></i>Coordinator Operational Report</h3>
        <p class="text-muted mb-0 small">Operational &amp; portfolio performance summary for {{ ucfirst($period) }} interval.</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('reports.coordinator-operational', ['period' => 'quarterly']) }}" class="btn btn-outline-dark btn-sm {{ $period === 'quarterly' ? 'active' : '' }}">
            <i class="bi bi-calendar3 me-1"></i> Quarterly PDF
        </a>
        <a href="{{ route('reports.coordinator-operational', ['period' => 'biannual']) }}" class="btn btn-outline-dark btn-sm {{ $period === 'biannual' ? 'active' : '' }}">
            <i class="bi bi-calendar-range me-1"></i> Biannual PDF
        </a>
        <a href="{{ route('reports.coordinator-operational', ['period' => 'annual']) }}" class="btn btn-outline-dark btn-sm {{ $period === 'annual' ? 'active' : '' }}">
            <i class="bi bi-calendar2-check me-1"></i> Annual PDF
        </a>
    </div>
</div>

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <span class="text-muted small text-uppercase fw-bold">Active Projects</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ $activeCount }}</h3>
            <small class="text-muted">{{ number_format($activeBudget, 2) }} ETB</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <span class="text-muted small text-uppercase fw-bold">Completed</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ $completedCount }}</h3>
            <small class="text-muted">{{ number_format($completedBudget, 2) }} ETB</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <span class="text-muted small text-uppercase fw-bold">Milestone Reports</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ $milestonesCount }}</h3>
            <small class="text-muted">In reporting cycle</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <span class="text-muted small text-uppercase fw-bold">Disbursed (ETB)</span>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalDisbursed, 0) }}</h3>
            <small class="text-muted">Finance releases</small>
        </div>
    </div>
</div>

{{-- Projects Summary Table --}}
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-folder2-open me-2"></i>Research Projects Under Scope ({{ $projects->count() }})</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>PI &amp; Department</th>
                    <th>Budget (ETB)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                <tr>
                    <td>#{{ $p->project_id }}</td>
                    <td class="fw-bold">{{ $p->title }}</td>
                    <td>{{ $p->pi->name ?? 'N/A' }} <small class="text-muted d-block">{{ $p->department->name ?? '' }}</small></td>
                    <td>{{ number_format($p->approved_budget ?: $p->requested_budget, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ $p->status }}</span></td>
                    <td>
                        <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-outline-dark btn-sm">Inspect</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No projects under coordinator scope.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Challenges summary --}}
@if($challenges->count() > 0)
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Recent PI Challenges Reported</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Project</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($challenges as $c)
                <tr>
                    <td>#{{ $c->project_id }}</td>
                    <td><span class="badge {{ $c->challenge_type === 'Financial' ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $c->challenge_type }}</span></td>
                    <td>{{ $c->description }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $c->status }}</span></td>
                    <td>{{ $c->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
