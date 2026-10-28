@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <div class="small text-muted mb-1"><a href="{{ route('projects.show', $project->project_id) }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i>Back to Project #{{ $project->project_id }}</a></div>
        <h3 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Project Challenges &amp; Bottlenecks</h3>
        <p class="text-muted mb-0 small">Report technical or financial hindrances to the Coordinator and Directorate.</p>
    </div>
    @if(Auth::user()->role === 'pi' && (int)$project->pi_id === (int)Auth::id())
    <button type="button" class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#newChallengeModal">
        <i class="bi bi-plus-circle me-1"></i> Submit New Challenge
    </button>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 15%;">Type &amp; Date</th>
                    <th style="width: 35%;">Challenge Description</th>
                    <th style="width: 25%;">Proposed Solution</th>
                    <th style="width: 15%;">Status &amp; Response</th>
                    <th style="width: 10%;" class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($challenges as $c)
                <tr>
                    <td>
                        <span class="badge {{ $c->challenge_type === 'Financial' ? 'bg-danger' : 'bg-warning text-dark' }} mb-1">{{ $c->challenge_type }}</span>
                        <div class="small text-muted">{{ $c->created_at->format('M d, Y') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $c->description }}</div>
                    </td>
                    <td>
                        <div class="small text-secondary">{{ $c->proposed_solution ?: 'No solution proposed' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $c->status === 'Resolved' ? 'bg-success' : ($c->status === 'Acknowledged' ? 'bg-info text-dark' : 'bg-secondary') }}">
                            {{ $c->status }}
                        </span>
                        @if($c->coordinator_response)
                        <div class="small text-muted mt-1 fst-italic">{{ Str::limit($c->coordinator_response, 45) }}</div>
                        @endif
                    </td>
                    <td class="text-end">
                        @if(in_array(Auth::user()->role, ['coordinator', 'admin']) && $c->status !== 'Resolved')
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#ackModal{{ $c->id }}">
                            Respond
                        </button>

                        <div class="modal fade text-start" id="ackModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('challenges.acknowledge', $c->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title">Coordinator Challenge Response</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Update Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Acknowledged">Acknowledged (Under Review)</option>
                                                    <option value="Resolved">Resolved</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Official Response / Guidance <span class="text-danger">*</span></label>
                                                <textarea name="coordinator_response" rows="3" class="form-control" required placeholder="State actionable assistance, budget adjustment recommendation, or technical support provided..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-dark btn-sm fw-bold">Submit Response</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted small">&mdash;</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                        No challenges or hindrances reported for this project.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Submit Challenge Modal --}}
@if(Auth::user()->role === 'pi')
<div class="modal fade" id="newChallengeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('challenges.store', $project->project_id) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Submit Project Challenge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Challenge Category <span class="text-danger">*</span></label>
                        <select name="challenge_type" class="form-select" required>
                            <option value="Technical">Technical (Equipment failure, sample delay, laboratory access, software)</option>
                            <option value="Financial">Financial (Procurement price inflation, disbursement delay, budget reallocation needed)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Detailed Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4" class="form-control" required placeholder="Describe the hindrance and its impact on the project schedule (minimum 20 characters)..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Proposed PI Solution / Mitigating Action</label>
                        <textarea name="proposed_solution" rows="2" class="form-control" placeholder="What solution or administrative assistance are you requesting?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark btn-sm fw-bold">
                        <i class="bi bi-send-check me-1"></i> Submit Challenge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
