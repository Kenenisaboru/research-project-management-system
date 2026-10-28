@extends('layouts.app')

@section('content')
<div class="py-2 py-md-3">
    <div class="container" style="max-width: 880px;">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Research Catalog</li>
            </ol>
        </nav>

        <header class="mb-3">
            <h1 class="h4 fw-bold text-dark mb-1">Research Catalog</h1>
            <p class="text-muted small mb-0">Browse university research proposals and ongoing projects.</p>
        </header>

        {{-- Metrics Strip --}}
        <div class="row g-2 mb-3">
            <div class="col-6 col-sm-3">
                <div class="p-2 rounded bg-white border text-center shadow-xs">
                    <div class="fw-bold text-dark fs-5">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Total Proposals</div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="p-2 rounded bg-white border text-center shadow-xs">
                    <div class="fw-bold text-dark fs-5">{{ $stats['draft'] ?? 0 }}</div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem;">In Intake</div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="p-2 rounded bg-white border text-center shadow-xs">
                    <div class="fw-bold text-dark fs-5">{{ $stats['ongoing'] ?? 0 }}</div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Active</div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="p-2 rounded bg-white border text-center shadow-xs">
                    <div class="fw-bold text-dark fs-5">{{ $stats['completed'] ?? 0 }}</div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Completed</div>
                </div>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="card border rounded p-2 bg-white shadow-xs mb-3">
            <form method="GET" action="{{ route('research.catalog') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Search proposals..." value="{{ request('q') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="thematic_id" class="form-select form-select-sm">
                        <option value="">All Research Areas</option>
                        @foreach($thematicAreas as $ta)
                            <option value="{{ $ta->id }}" {{ request('thematic_id') == $ta->id ? 'selected' : '' }}>{{ $ta->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach(['Draft','Active','Completed','Terminated','Rejected'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st === 'Draft' ? 'Draft / Intake' : $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100 fw-semibold">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Project List --}}
        <div class="row g-2">
            @forelse($projects as $project)
                <div class="col-md-6">
                    <div class="card h-100 border rounded p-3 bg-white shadow-xs d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-1 mb-2">
                                <span class="badge bg-dark fw-semibold" style="font-size: 0.68rem;">
                                    {{ $project->status === 'Draft' ? 'Draft / Intake' : $project->status }}
                                </span>
                                @if($project->thematicArea)
                                    <span class="badge border bg-light text-muted text-truncate" style="max-width: 180px; font-size: 0.68rem;">
                                        {{ $project->thematicArea->title }}
                                    </span>
                                @endif
                            </div>
                            <h2 class="h6 fw-bold text-dark mb-1 text-truncate" title="{{ $project->title }}">
                                {{ $project->title }}
                            </h2>
                            <p class="text-muted small mb-2 lh-sm" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $project->abstract_text ?: 'Proposal summary on file.' }}
                            </p>
                        </div>
                        <div class="pt-2 border-top text-muted d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                            <span class="text-truncate">
                                <i class="bi bi-building me-1"></i>{{ $project->department->name ?? 'University' }}
                            </span>
                            <span class="text-nowrap ms-2 font-monospace">
                                ETB {{ number_format($project->budget_total ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border rounded p-4 bg-white text-center text-muted shadow-xs">
                        <i class="bi bi-journal-x fs-3 d-block mb-1 text-muted"></i>
                        <div class="small fw-semibold">No proposals found matching criteria.</div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($projects->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $projects->links() }}
            </div>
        @endif

        {{-- Bottom Navigation --}}
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                &larr; Home
            </a>
            <a href="{{ route('support') }}" class="btn btn-sm btn-outline-dark fw-semibold">
                Support &rarr;
            </a>
        </div>
    </div>
</div>
@endsection