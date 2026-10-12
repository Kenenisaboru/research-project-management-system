@extends('layouts.app')

@section('navbar-class', 'justify-content-center')

@section('content')
<div class="text-center mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-globe2 me-2 text-dark"></i>GMU Research Repository</h2>
    <p class="text-muted mb-0">Open catalogue of ongoing, completed, terminated and rejected research projects at Gambella University</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center h-100">
            <div class="fs-3 fw-bold text-success">{{ $stats['completed'] }}</div>
            <div class="small text-muted text-uppercase fw-bold">Completed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center h-100">
            <div class="fs-3 fw-bold text-primary">{{ $stats['ongoing'] }}</div>
            <div class="small text-muted text-uppercase fw-bold">Ongoing</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center h-100">
            <div class="fs-3 fw-bold text-warning">{{ $stats['terminated'] }}</div>
            <div class="small text-muted text-uppercase fw-bold">Terminated</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center h-100">
            <div class="fs-3 fw-bold text-danger">{{ $stats['rejected'] }}</div>
            <div class="small text-muted text-uppercase fw-bold">Rejected</div>
        </div>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-bold mb-1">Search</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Title or abstract..." value="{{ request('q') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-bold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach(['Active','Completed','Terminated','Rejected'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-bold mb-1">Thematic Area</label>
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
        <div class="col-6 col-md-auto">
            <button class="btn btn-dark btn-sm fw-bold w-100"><i class="bi bi-search me-1"></i> Search</button>
        </div>
    </form>
</div>

<div class="row row-cols-1 row-cols-lg-2 g-3">
    @forelse($projects as $project)
    <div class="col">
        <div class="card card-custom h-100 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge {{ $project->status === 'Completed' ? 'bg-success' : ($project->status === 'Rejected' ? 'bg-danger' : ($project->status === 'Terminated' ? 'bg-warning text-dark' : 'bg-primary')) }}">{{ $project->status }}</span>
                @if($project->thematicArea)
                    <span class="badge bg-light text-dark border">{{ \Illuminate\Support\Str::limit($project->thematicArea->title, 40) }}</span>
                @endif
            </div>
            <h6 class="fw-bold mb-1">{{ $project->title }}</h6>
            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($project->abstract_text, 220) }}</p>
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
@endsection