@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bell-fill me-2 text-dark"></i>Notification Centre</h4>
        <p class="text-muted small mb-0">System alerts, workflow progression updates, and institutional reminders.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        @if(($unreadCounts['total'] ?? 0) > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn btn-outline-dark btn-sm fw-bold">
                <i class="bi bi-check2-all me-1"></i> Mark All as Read
            </button>
        </form>
        @endif
    </div>
</div>

{{-- Category Filter Navigation --}}
<div class="card card-custom p-3 mb-4 border-0 shadow-sm">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ route('notifications.index', array_merge($filters, ['category' => 'all'])) }}"
           class="btn btn-sm {{ ($filters['category'] ?? 'all') === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
            All Notifications
            <span class="badge bg-secondary ms-1">{{ $unreadCounts['total'] ?? 0 }}</span>
        </a>
        <a href="{{ route('notifications.index', array_merge($filters, ['category' => 'workflow'])) }}"
           class="btn btn-sm {{ ($filters['category'] ?? '') === 'workflow' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
            <i class="bi bi-diagram-3 me-1"></i> Workflow
            @if(($unreadCounts['workflow'] ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCounts['workflow'] }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.index', array_merge($filters, ['category' => 'budget'])) }}"
           class="btn btn-sm {{ ($filters['category'] ?? '') === 'budget' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
            <i class="bi bi-cash-coin me-1"></i> Financial &amp; Budget
            @if(($unreadCounts['budget'] ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCounts['budget'] }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.index', array_merge($filters, ['category' => 'review'])) }}"
           class="btn btn-sm {{ ($filters['category'] ?? '') === 'review' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
            <i class="bi bi-pencil-square me-1"></i> Peer Reviews
            @if(($unreadCounts['review'] ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCounts['review'] }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.index', array_merge($filters, ['category' => 'compliance'])) }}"
           class="btn btn-sm {{ ($filters['category'] ?? '') === 'compliance' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
            <i class="bi bi-shield-check me-1"></i> Ethics &amp; Compliance
            @if(($unreadCounts['compliance'] ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCounts['compliance'] }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.index', array_merge($filters, ['category' => 'system'])) }}"
           class="btn btn-sm {{ ($filters['category'] ?? '') === 'system' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
            <i class="bi bi-gear me-1"></i> System
            @if(($unreadCounts['system'] ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCounts['system'] }}</span>
            @endif
        </a>

        <div class="ms-md-auto d-flex gap-2">
            <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href = this.value;">
                <option value="{{ route('notifications.index', array_merge($filters, ['status' => ''])) }}" {{ empty($filters['status']) ? 'selected' : '' }}>All Status</option>
                <option value="{{ route('notifications.index', array_merge($filters, ['status' => 'unread'])) }}" {{ ($filters['status'] ?? '') === 'unread' ? 'selected' : '' }}>Unread Only</option>
                <option value="{{ route('notifications.index', array_merge($filters, ['status' => 'read'])) }}" {{ ($filters['status'] ?? '') === 'read' ? 'selected' : '' }}>Read</option>
            </select>
        </div>
    </div>
</div>

{{-- Notifications List --}}
<div class="card card-custom p-0 border-0 shadow-sm overflow-hidden">
    <div class="list-group list-group-flush">
        @forelse($notifications as $n)
        <div class="list-group-item p-3 p-md-4 {{ $n->read_at ? 'bg-white' : 'bg-light border-start border-4 border-success' }}">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 {{ $n->read_at ? 'bg-light text-muted' : 'bg-success-subtle text-success' }}" style="width: 42px; height: 42px;">
                    <i class="bi {{ $n->icon ?: 'bi-bell' }} fs-5"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark-subtle text-dark border px-2 py-1 font-monospace" style="font-size: 0.7rem;">
                                {{ strtoupper($n->category ?? 'WORKFLOW') }}
                            </span>
                            @if(($n->priority ?? 'normal') === 'urgent')
                                <span class="badge bg-danger text-white px-2 py-1" style="font-size: 0.7rem;">URGENT</span>
                            @elseif(($n->priority ?? 'normal') === 'high')
                                <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.7rem;">HIGH</span>
                            @endif
                            <small class="text-muted">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</small>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if(!$n->read_at)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none text-success p-0 fw-semibold" title="Mark as Read">
                                    <i class="bi bi-check-circle me-1"></i>Mark Read
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" class="d-inline" onsubmit="return confirm('Remove this notification?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted p-0" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="text-dark {{ $n->read_at ? '' : 'fw-semibold' }} mb-2" style="font-size: 0.92rem;">
                        {{ $n->message }}
                    </div>

                    @if($n->action_url)
                    <div class="mt-2">
                        <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-dark fw-semibold" style="font-size: 0.8rem;">
                                View Details &rarr;
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-3 text-secondary"></i>
            <h6 class="fw-bold mb-1">No notifications found</h6>
            <p class="small mb-0">You're completely caught up! New alerts and workflow events will appear here.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="p-3 border-top">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
