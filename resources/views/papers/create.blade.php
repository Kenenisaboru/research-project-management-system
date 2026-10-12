@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <a href="{{ route('papers.index') }}" class="text-decoration-none small text-muted mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i> Back to Paper Registry
        </a>
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-journal-plus fs-2 text-dark me-3"></i>
                <div>
                    <h3 class="fw-bold mb-0">Submit Publication Paper</h3>
                    <span class="text-muted small">Registers your paper for journal-index evaluation and eligibility for university awards</span>
                </div>
            </div>

            <form method="POST" action="{{ route('papers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Paper Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Full title of the published / submitted paper" value="{{ old('title') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Abstract <span class="text-danger">*</span></label>
                        <textarea name="abstract_text" class="form-control" rows="4" required placeholder="Paper abstract...">{{ old('abstract_text') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Author(s) <span class="text-danger">*</span></label>
                        <input type="text" name="authors" class="form-control" required placeholder="e.g. Dr. Abebe Bikila; Tigist Assefa" value="{{ old('authors') }}">
                        <div class="form-text small">Separate multiple authors with a semicolon (;).</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Related Research Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">-- Not linked to a project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->project_id }}" {{ old('project_id') == $project->project_id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($project->title, 70) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Journal Name</label>
                        <input type="text" name="journal_name" class="form-control" placeholder="e.g. Journal of Hydrology" value="{{ old('journal_name') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Journal Index</label>
                        <select name="journal_index" class="form-select">
                            <option value="">-- Not indexed --</option>
                            <option value="SCIE" {{ old('journal_index') === 'SCIE' ? 'selected' : '' }}>SCIE (Web of Science)</option>
                            <option value="SSCI" {{ old('journal_index') === 'SSCI' ? 'selected' : '' }}>SSCI (Web of Science)</option>
                            <option value="Scopus" {{ old('journal_index') === 'Scopus' ? 'selected' : '' }}>Scopus</option>
                            <option value="ESCI" {{ old('journal_index') === 'ESCI' ? 'selected' : '' }}>ESCI (Web of Science)</option>
                            <option value="National" {{ old('journal_index') === 'National' ? 'selected' : '' }}>National Journal</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Publication Date</label>
                        <input type="date" name="publication_date" class="form-control" value="{{ old('publication_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Paper File (PDF/DOC)</label>
                        <input type="file" name="paper_file" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('papers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="button" class="btn btn-dark fw-bold px-4 confirm-btn"
                            data-confirm-title="Submit Paper"
                            data-confirm-message="Submit this paper for journal-index evaluation?"
                            data-confirm-icon="bi-journal-plus"
                            data-confirm-color="text-dark"
                            data-confirm-btn-text="Yes, Submit"
                            data-confirm-btn-class="btn-dark">
                        <i class="bi bi-send me-1"></i> Submit Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection