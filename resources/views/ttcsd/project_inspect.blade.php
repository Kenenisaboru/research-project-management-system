@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <div class="small text-muted mb-1"><a href="{{ route('ttcsd.dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i>Back to TTCSD Dashboard</a></div>
        <h3 class="fw-bold mb-1"><i class="bi bi-shield-check me-2 text-dark"></i>TTCSD Project Audit &bull; #{{ $project->project_id }}</h3>
        <p class="text-muted mb-0 small">{{ $project->title }}</p>
    </div>
    <div>
        <span class="badge bg-primary fs-6 px-3 py-2">{{ $project->status }}</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Project Overview --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Project Abstract &amp; Details</h5>
            <div class="p-3 bg-light rounded-3 text-secondary mb-3" style="line-height: 1.7;">
                {{ $project->abstract_text }}
            </div>
            <div class="row g-3 small">
                <div class="col-sm-6">
                    <strong>PI Name:</strong> {{ $project->pi->name ?? 'N/A' }} ({{ $project->pi->email ?? '' }})
                </div>
                <div class="col-sm-6">
                    <strong>Department:</strong> {{ $project->department->name ?? 'N/A' }}
                </div>
                <div class="col-sm-6">
                    <strong>Thematic Area:</strong> {{ $project->thematicArea->name ?? 'General' }}
                </div>
                <div class="col-sm-6">
                    <strong>Approved Budget:</strong> <span class="fw-bold text-success">{{ number_format($project->approved_budget ?: $project->requested_budget, 2) }} ETB</span>
                </div>
            </div>
        </div>

        {{-- Milestone Reports --}}
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-info me-2"></i>Milestone Progress Reports</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Phase</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Deliverables</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->milestoneReports as $report)
                        <tr>
                            <td><strong>Phase {{ $report->milestone_phase }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $report->status }}</span></td>
                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($report->deliverable_file)
                                <a href="{{ Storage::url($report->deliverable_file) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                                    <i class="bi bi-download me-1"></i> File
                                </a>
                                @else
                                <span class="text-muted small">None</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No progress reports filed yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Reported PI Challenges --}}
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Reported Bottlenecks &amp; Challenges</h5>
            <ul class="list-group list-group-flush">
                @forelse($challenges as $c)
                <li class="list-group-item px-0 py-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="badge {{ $c->challenge_type === 'Financial' ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $c->challenge_type }}</span>
                        <span class="small text-muted">{{ $c->created_at->format('M d, Y') }} &bull; {{ $c->status }}</span>
                    </div>
                    <p class="mb-1 text-dark small fw-semibold">{{ $c->description }}</p>
                    @if($c->proposed_solution)
                    <div class="small text-muted"><strong>Proposed solution:</strong> {{ $c->proposed_solution }}</div>
                    @endif
                    @if($c->coordinator_response)
                    <div class="p-2 bg-light rounded mt-2 small text-secondary">
                        <strong>Coordinator response:</strong> {{ $c->coordinator_response }}
                    </div>
                    @endif
                </li>
                @empty
                <li class="list-group-item text-center text-muted py-3">No challenges reported by PI.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Financial Summary --}}
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Financial &amp; Disbursement Audit</h6>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Total Budget:</span>
                <span class="fw-bold">{{ number_format($project->approved_budget ?: $project->requested_budget, 2) }} ETB</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Disbursed:</span>
                <span class="fw-bold text-success">{{ number_format($project->budgetRequests->where('status', 'Released')->sum('approved_amount'), 2) }} ETB</span>
            </div>
            <div class="d-flex justify-content-between small">
                <span class="text-muted">Remaining:</span>
                <span class="fw-bold text-secondary">
                    {{ number_format(max(0, ($project->approved_budget ?: $project->requested_budget) - $project->budgetRequests->where('status', 'Released')->sum('approved_amount')), 2) }} ETB
                </span>
            </div>
        </div>

        {{-- Research Team --}}
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Research Team</h6>
            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                <div class="rounded-circle bg-dark text-white p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">PI</div>
                <div>
                    <div class="fw-bold small">{{ $project->pi->name ?? 'N/A' }}</div>
                    <small class="text-muted">Principal Investigator</small>
                </div>
            </div>
            @foreach($project->members as $member)
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="rounded-circle bg-light text-dark p-2 d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px; font-size: 0.8rem;">TM</div>
                <div>
                    <div class="fw-semibold small">{{ $member->user->name ?? $member->name ?? 'Team Member' }}</div>
                    <small class="text-muted">{{ $member->role_in_project ?: 'Co-Investigator' }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
