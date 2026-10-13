@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-hdd-stack me-2 text-dark"></i>Backup &amp; Recovery</h3>
        <span class="text-muted small">Create, download and restore database backups for business continuity</span>
    </div>
    <div>
        <form method="POST" action="{{ route('admin.backups.create') }}" class="d-inline">
            @csrf
            <button type="button" class="btn btn-dark fw-bold confirm-btn"
                    data-confirm-title="Create Backup"
                    data-confirm-message="Create a new database backup now?"
                    data-confirm-icon="bi-hdd-stack"
                    data-confirm-color="text-dark"
                    data-confirm-btn-text="Yes, Create Backup"
                    data-confirm-btn-class="btn-dark">
                <i class="bi bi-plus-circle me-1"></i> Create Backup Now
            </button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <div class="text-muted small text-uppercase fw-bold">Database Driver</div>
            <div class="fs-4 fw-bold text-dark">{{ strtoupper($driver) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <div class="text-muted small text-uppercase fw-bold">Database Size</div>
            <div class="fs-4 fw-bold text-dark">{{ $dbSize ? number_format($dbSize / 1024, 1) . ' KB' : '—' }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 h-100">
            <div class="text-muted small text-uppercase fw-bold">Stored Backups</div>
            <div class="fs-4 fw-bold text-dark">{{ $files->count() }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-dark"></i>Backup History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>File</th><th>Size</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($files as $f)
                        <tr>
                            <td class="small fw-bold text-break">{{ $f['name'] }}</td>
                            <td class="small">{{ number_format($f['size'] / 1024, 1) }} KB</td>
                            <td class="small">{{ \Carbon\Carbon::createFromTimestamp($f['modified'])->format('d M Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.backups.download', $f['name']) }}" class="btn btn-outline-dark"><i class="bi bi-download"></i></a>
                                    <form method="POST" action="{{ route('admin.backups.destroy', $f['name']) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger confirm-btn" data-confirm-title="Delete Backup" data-confirm-message="Permanently delete this backup file?" data-confirm-icon="bi-trash" data-confirm-color="text-danger" data-confirm-btn-text="Yes, Delete" data-confirm-btn-class="btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-hdd fs-3 d-block mb-2"></i>No backups yet. Click "Create Backup Now".</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-arrow-counterclockwise me-2 text-danger"></i>Restore from Backup</h5>
            <div class="alert alert-warning small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Restoring replaces current data. A pre-restore backup is created first; verify backups regularly and store a separate copy off this server.
            </div>
            <form method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Upload Backup File (.json / .sqlite)</label>
                    <input type="file" name="backup_file" class="form-control" accept=".json,.sqlite" required>
                </div>
                <button type="button" class="btn btn-danger fw-bold w-100 confirm-btn"
                        data-confirm-title="Restore Database"
                        data-confirm-message="This will overwrite current data with the uploaded backup. Continue?"
                        data-confirm-icon="bi-arrow-counterclockwise"
                        data-confirm-color="text-danger"
                        data-confirm-btn-text="Yes, Restore"
                        data-confirm-btn-class="btn-danger">
                    <i class="bi bi-upload me-1"></i> Restore Database
                </button>
            </form>
            <hr>
            <div class="small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Scheduled backups can be enabled by running <code>php artisan backups:create</code> daily via the scheduler, or via the provided Docker entrypoint cron.
            </div>
        </div>
    </div>
</div>
@endsection