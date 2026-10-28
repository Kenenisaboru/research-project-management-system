@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-calendar-event me-2 text-dark"></i>Defence &amp; Presentation Timetable</h3>
        <p class="text-muted mb-0 small">Official schedule for initial defences, RCSC budget reviews, progress presentations, and final defences.</p>
    </div>
    @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
    <button type="button" class="btn btn-dark fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
        <i class="bi bi-calendar-plus me-1"></i> Arrange New Schedule
    </button>
    @endif
</div>

{{-- Notification Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Quick Filter Pills --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <span class="text-muted small fw-bold text-uppercase">Initial Defences</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ $upcoming->where('type', 'InitialDefence')->count() }}</h4>
            <small class="text-muted">Proposals</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <span class="text-muted small fw-bold text-uppercase">RCSC Budget</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ $upcoming->where('type', 'BudgetApproval')->count() }}</h4>
            <small class="text-muted">&ge; 500k ETB</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <span class="text-muted small fw-bold text-uppercase">Progress Reviews</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ $upcoming->where('type', 'ProgressReport')->count() }}</h4>
            <small class="text-muted">Milestones</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <span class="text-muted small fw-bold text-uppercase">Final Defences</span>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ $upcoming->where('type', 'FinalDefence')->count() }}</h4>
            <small class="text-muted">Examiners Panel</small>
        </div>
    </div>
</div>

{{-- Upcoming Presentation Schedules --}}
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Upcoming Scheduled Presentations ({{ $upcoming->count() }})</h5>
        <span class="badge bg-primary rounded-pill">{{ $upcoming->count() }} Upcoming</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 18%;">Date &amp; Time</th>
                    <th style="width: 20%;">Type</th>
                    <th style="width: 32%;">Project Title &amp; PI</th>
                    <th style="width: 18%;">Venue / Platform</th>
                    <th style="width: 12%;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcoming as $item)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->scheduled_at->format('M d, Y') }}</div>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $item->scheduled_at->format('h:i A') }}</small>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($item->type) {
                                'InitialDefence' => 'bg-primary',
                                'BudgetApproval' => 'bg-warning text-dark',
                                'ProgressReport' => 'bg-info text-dark',
                                'FinalDefence'   => 'bg-success',
                                default          => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1">
                            {{ $item->type_display }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('projects.show', $item->project_id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                            {{ $item->project->title ?? 'N/A' }}
                        </a>
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>PI: {{ $item->project->pi->name ?? 'N/A' }}
                            @if($item->project && $item->project->requested_budget >= 500000)
                            <span class="badge bg-light text-dark border ms-1">&ge; 500k ETB</span>
                            @endif
                        </small>
                        @if($item->notes)
                        <div class="small text-secondary mt-1 fst-italic"><i class="bi bi-info-circle me-1"></i>{{ $item->notes }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="fw-semibold text-secondary">
                            <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $item->venue ?: 'Main Campus Hall / TBA' }}
                        </span>
                        <div class="small text-muted">Arranged by {{ $item->arranger->name ?? 'Coordinator' }}</div>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('projects.show', $item->project_id) }}" class="btn btn-outline-dark btn-sm" title="View Project">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $item->id }}" title="Edit Schedule">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('schedules.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-outline-danger btn-sm confirm-btn" data-confirm-title="Cancel Schedule" data-confirm-message="Are you sure you want to cancel this presentation schedule?" data-confirm-icon="bi-x-circle" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Cancel" data-confirm-btn-class="btn-danger" title="Cancel Schedule">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Edit Modal for each upcoming item (Coordinator/Admin) --}}
                @if(in_array(Auth::user()->role, ['coordinator', 'admin']))
                <div class="modal fade" id="editScheduleModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('schedules.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Update Schedule</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="small text-muted mb-3">Project: <strong>{{ $item->project->title ?? 'N/A' }}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Date &amp; Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ $item->scheduled_at->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Venue / Platform</label>
                                        <input type="text" name="venue" class="form-control" value="{{ $item->venue }}" placeholder="Hall A, Zoom, College Room...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Schedule Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="Scheduled" {{ $item->status === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="Completed" {{ $item->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled" {{ $item->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Notes / Agenda</label>
                                        <textarea name="notes" rows="3" class="form-control" placeholder="Specific guidelines, stakeholders invited...">{{ $item->notes }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-dark btn-sm fw-bold">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted"></i>
                        No upcoming presentation schedules found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Past & Concluded Schedules --}}
@if($past->count() > 0)
<div class="card card-custom">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-archive me-2"></i>Past / Concluded Presentations ({{ $past->count() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 text-muted">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Project</th>
                    <th>PI</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($past as $pItem)
                <tr>
                    <td>{{ $pItem->scheduled_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $pItem->type_display }}</span></td>
                    <td>{{ Str::limit($pItem->project->title ?? 'N/A', 50) }}</td>
                    <td>{{ $pItem->project->pi->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $pItem->status === 'Completed' ? 'bg-success' : ($pItem->status === 'Cancelled' ? 'bg-danger' : 'bg-secondary') }}">
                            {{ $pItem->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- New Schedule Modal (Coordinator / Admin Only) --}}
@if(in_array(Auth::user()->role, ['coordinator', 'admin']))
<div class="modal fade" id="newScheduleModal" tabindex="-1" aria-labelledby="newScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('schedules.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="newScheduleModalLabel">
                        <i class="bi bi-calendar-plus me-2"></i>Arrange Presentation Schedule
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select" required>
                            <option value="">-- Choose Project --</option>
                            @foreach($projects as $proj)
                            <option value="{{ $proj->project_id }}">
                                #{{ $proj->project_id }} - {{ Str::limit($proj->title, 45) }} ({{ $proj->pi->name ?? 'PI' }} - {{ number_format($proj->requested_budget) }} ETB)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Presentation / Defence Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="InitialDefence">Initial Project Defence</option>
                            <option value="BudgetApproval">Budget Approval RCSC Meeting (Budget &ge; 500k ETB)</option>
                            <option value="ProgressReport">Project Progress Report Presentation</option>
                            <option value="FinalDefence">Final Project Defence with Stakeholders &amp; Examiners</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Scheduled Date &amp; Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Venue / Location / Link</label>
                        <input type="text" name="venue" class="form-control" placeholder="e.g. Conference Hall B, Zoom Link, College Meeting Room">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Agenda &amp; Instructions</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Specify presentation time limit (e.g. 20 min presentation, 15 min Q&A), examiners invited, required deliverables..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark btn-sm fw-bold px-3">
                        <i class="bi bi-check2-circle me-1"></i> Save &amp; Publish Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
