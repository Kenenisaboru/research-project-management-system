@extends('layouts.app')

@section('navbar-class', 'justify-content-center')

@section('styles')
<style>
    .catalog-page {
        max-width: 1180px;
        margin: 0 auto;
    }

    .catalog-heading {
        padding: 0.75rem 0 1.25rem;
    }

    .catalog-heading h1 {
        font-size: 1.75rem;
    }

    .catalog-filter {
        border: 1px solid var(--UNI-card-border);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .catalog-project-card {
        min-height: 190px;
    }

    .catalog-project-title {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .catalog-project-summary {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .catalog-area-badge {
        max-width: 44%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .catalog-heading {
            padding-top: 0;
        }

        .catalog-heading h1 {
            font-size: 1.45rem;
        }
    }
</style>
@endsection

@section('content')
<div class="catalog-page">
<div class="catalog-heading text-center">
    <h1 class="fw-bold mb-1"><i class="bi bi-globe2 me-2 text-dark"></i>Research Catalog</h1>
    <p class="text-muted small mb-0">Browse university research projects.</p>
</div>

<div class="card catalog-filter p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label small fw-bold mb-1">Search</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search projects..." value="{{ request('q') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach(['Draft','Active','Completed','Terminated','Rejected'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold mb-1">Research Area</label>
            <select name="thematic_id" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($thematicAreas as $ta)
                    <option value="{{ $ta->id }}" {{ request('thematic_id') == $ta->id ? 'selected' : '' }}>{{ $ta->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold mb-1">College</label>
            <select name="college_id" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($colleges as $c)
                    <option value="{{ $c->id }}" {{ request('college_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-1">
            <button class="btn btn-success btn-sm fw-bold w-100"><i class="bi bi-search me-1"></i> Go</button>
        </div>
    </form>
</div>

<div class="row row-cols-1 row-cols-lg-2 g-3">
    @forelse($projects as $project)
    <div class="col">
        <div class="card card-custom catalog-project-card h-100 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge {{ $project->status === 'Completed' ? 'bg-success' : ($project->status === 'Draft' ? 'bg-secondary' : ($project->status === 'Rejected' ? 'bg-danger' : ($project->status === 'Terminated' ? 'bg-warning text-dark' : 'bg-primary'))) }}">
                    {{ $project->status === 'Draft' ? 'Draft / Intake' : $project->status }}
                </span>
                @if($project->thematicArea)
                    <span class="badge catalog-area-badge bg-light text-dark border">{{ \Illuminate\Support\Str::limit($project->thematicArea->title, 40) }}</span>
                @endif
            </div>
            <h2 class="catalog-project-title h6 fw-bold mb-1">{{ $project->title }}</h2>
            <p class="catalog-project-summary text-muted small mb-2">{{ \Illuminate\Support\Str::limit($project->abstract_text, 150) }}</p>
            <div class="mt-auto pt-2 border-top small text-muted">
                <i class="bi bi-building me-1"></i>{{ $project->department->name ?? 'GMU' }}
                @if($project->department && $project->department->college)
                    &nbsp;·&nbsp;{{ $project->department->college->name }}
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-5">
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            No projects match your search.
        </div>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $projects->links() }}
</div>
</div>
@endsection