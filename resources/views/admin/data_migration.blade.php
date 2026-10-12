@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-box-arrow-in-down me-2 text-dark"></i>Legacy Data Migration</h3>
        <span class="text-muted small">Import existing paper-based project records from CSV (Excel-exported) files</span>
    </div>
    <div>
        <a href="{{ route('admin.data-migration.template') }}" class="btn btn-outline-dark fw-bold">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> Download CSV Template
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-upload me-2 text-dark"></i>Import Projects from CSV</h5>

            @if(session('import_errors'))
            <div class="alert alert-warning small">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Skipped rows:</div>
                <ul class="mb-0 ps-3">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.data-migration.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">CSV File <span class="text-danger">*</span></label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                    <div class="form-text small">Export your Excel sheet as CSV (UTF-8) and upload it here.</div>
                </div>
                <button type="button" class="btn btn-dark fw-bold w-100 confirm-btn"
                        data-confirm-title="Import Legacy Data"
                        data-confirm-message="Import projects from this CSV file into the system?"
                        data-confirm-icon="bi-box-arrow-in-down"
                        data-confirm-color="text-dark"
                        data-confirm-btn-text="Yes, Import"
                        data-confirm-btn-class="btn-dark">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Run Import
                </button>
            </form>

            <hr>
            <h6 class="fw-bold">Expected Columns</h6>
            <div class="small">
                <code>title</code>, <code>abstract_text</code>, <code>pi_email</code>, <code>requested_budget</code>, <code>status</code>, <code>department_code</code>, <code>thematic_title</code>
            </div>
            <p class="small text-muted mt-2 mb-0">
                The first row must be the header. Rows with a missing title or unmatched PI are skipped and reported.
                Unknown <code>department_code</code>/<code>thematic_title</code> fall back to the first available record; unmatched <code>pi_email</code> falls back to the first PI account.
            </p>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-custom p-4 mb-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-dark"></i>Migration Status</h5>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="text-muted">Projects currently in system</span>
                <span class="fw-bold">{{ $existing }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Last import</span>
                <span class="fw-bold">{{ $lastImport ? $lastImport->created_at->diffForHumans() : 'Never' }}</span>
            </div>
            @if($lastImport)
            <div class="small text-muted mt-2">{{ $lastImport->details }}</div>
            @endif
        </div>

        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-2"><i class="bi bi-shield-check me-2 text-success"></i>Integrity Notes</h5>
            <ul class="small text-muted mb-0 ps-3">
                <li>All imported records are tagged in the audit log under <code>LEGACY_IMPORT</code>.</li>
                <li>Duplicate titles are allowed; review imported records under Projects.</li>
                <li>Re-running an import will create additional records — clean the CSV first.</li>
            </ul>
        </div>
    </div>
</div>
@endsection