@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0"><i class="bi bi-journal-richtext me-2 text-dark"></i>Publication &amp; Award Registry</h3>
        <span class="text-muted small">Submit papers, evaluate journal-index quality, approve, award and record publications</span>
    </div>
    @if(in_array(auth()->user()->role, ['pi', 'tm', 'admin']))
    <div>
        <a href="{{ route('papers.create') }}" class="btn btn-dark fw-bold text-nowrap">
            <i class="bi bi-plus-circle me-1"></i> Submit Paper
        </a>
    </div>
    @endif
</div>

@if(in_array(auth()->user()->role, ['reviewer', 'coordinator', 'vparttcs', 'rcsc']) && $pendingEvaluationCount > 0)
<div class="alert alert-warning d-flex align-items-center shadow-sm mb-4">
    <i class="bi bi-clipboard-check fs-4 me-3"></i>
    <div class="flex-grow-1">
        <strong>{{ $pendingEvaluationCount }} paper(s)</strong> assigned to you await journal-index evaluation.
    </div>
    <a href="#evaluationQueue" class="btn btn-warning btn-sm fw-bold">Evaluate Now</a>
</div>
@endif

<div class="card card-custom p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label small fw-bold mb-1">Search</label>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search by paper title..." value="{{ request('q') }}">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label small fw-bold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['Submitted','UnderEvaluation','Approved','Rejected','Recorded'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label small fw-bold mb-1">Journal Index</label>
            <select name="journal_index" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Any Index</option>
                @foreach(['SCIE','SSCI','Scopus','ESCI','National','None'] as $idx)
                    <option value="{{ $idx }}" {{ request('journal_index') === $idx ? 'selected' : '' }}>{{ $idx }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-dark btn-sm fw-bold"><i class="bi bi-search me-1"></i> Filter</button>
        </div>
    </form>
</div>

{{-- Evaluation Queue --}}
@if(in_array(auth()->user()->role, ['reviewer', 'coordinator', 'vparttcs', 'rcsc']))
<div id="evaluationQueue" class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2 text-warning"></i>Awaiting My Evaluation</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Paper</th>
                    <th>Submitter</th>
                    <th>Journal / Index</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($papers->getCollection()->where('status', '!=', 'Recorded')->where('status', '!=', 'Rejected') as $paper)
                <tr>
                    <td class="fw-bold">{{ \Illuminate\Support\Str::limit($paper->title, 60) }}</td>
                    <td>{{ $paper->submitter->name ?? '—' }}</td>
                    <td class="small">{{ $paper->journal_name ?? '—' }} <span class="badge bg-secondary">{{ $paper->journal_index ?? '—' }}</span></td>
                    <td><span class="badge {{ $paper->status === 'Submitted' ? 'bg-info' : ($paper->status === 'UnderEvaluation' ? 'bg-warning text-dark' : 'bg-success') }}">{{ $paper->status }}</span></td>
                    <td>
                        <a href="{{ route('papers.show', $paper->id) }}" class="btn btn-sm btn-outline-dark">Open &amp; Evaluate</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-check2-circle fs-3 d-block mb-2"></i>
                        Nothing awaiting your evaluation.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Papers --}}
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-dark"></i>Paper Records</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Submitter</th>
                    <th>Journal / Index</th>
                    <th>Score</th>
                    <th>Award</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($papers as $paper)
                <tr>
                    <td>#{{ $paper->id }}</td>
                    <td class="fw-bold">{{ \Illuminate\Support\Str::limit($paper->title, 55) }}</td>
                    <td class="small">{{ $paper->submitter->name ?? '—' }}</td>
                    <td class="small">{{ $paper->journal_name ?? '—' }}<br><span class="badge bg-secondary">{{ $paper->journal_index ?? '—' }}</span></td>
                    <td class="small">{{ $paper->average_score !== null ? $paper->average_score . ' / 100' : '—' }}</td>
                    <td><span class="badge {{ $paper->award_status === 'Awarded' ? 'bg-success' : 'bg-light text-dark border' }}">{{ $paper->award_status }}</span></td>
                    <td><span class="badge {{ $paper->status === 'Approved' ? 'bg-success' : ($paper->status === 'Rejected' ? 'bg-danger' : ($paper->status === 'Recorded' ? 'bg-dark' : 'bg-warning text-dark')) }}">{{ $paper->status }}</span></td>
                    <td>
                        <a href="{{ route('papers.show', $paper->id) }}" class="btn btn-sm btn-outline-dark">
                            <i class="bi bi-box-arrow-up-right"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-journal-text fs-3 d-block mb-2"></i>
                        No papers registered yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $papers->links() }}
    </div>
</div>

{{-- Awards --}}
@if($awards->count())
<div class="card card-custom p-4 mt-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-award me-2 text-success"></i>Recorded Awards</h5>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Recipient</th>
                    <th>Award</th>
                    <th>Paper</th>
                    <th>Amount (ETB)</th>
                    <th>Date</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($awards as $award)
                <tr>
                    <td class="fw-bold">{{ $award->recipient->name ?? '—' }}</td>
                    <td>{{ $award->award_name }}</td>
                    <td class="small">{{ \Illuminate\Support\Str::limit($award->paper->title ?? '—', 50) }}</td>
                    <td>{{ number_format($award->award_amount, 0) }}</td>
                    <td class="small">{{ $award->awarded_at->format('d M Y') }}</td>
                    <td class="small">{{ $award->awardedBy->name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection