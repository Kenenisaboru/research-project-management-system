@extends('layouts.app')

@section('content')
<div class="page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h1 class="h3 fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-dark"></i>Executive Analytics &amp; Reporting</h1>
        <p class="text-muted small mb-0">Institutional research portfolio intelligence, financial audit &amp; publication metrics.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('analytics.export.csv', request()->query()) }}" class="btn btn-outline-dark fw-bold shadow-sm flex-grow-1 flex-md-grow-0">
            <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> Export CSV
        </a>
        <a href="{{ route('analytics.export.pdf', request()->query()) }}" class="btn btn-dark fw-bold shadow-sm flex-grow-1 flex-md-grow-0">
            <i class="bi bi-file-earmark-pdf me-1 text-danger"></i> Export PDF
        </a>
    </div>
</div>

{{-- Filter Accordion Card --}}
<div class="card card-custom p-3 mb-4 border-0 shadow-sm">
    <form method="GET" action="{{ route('analytics') }}" class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
            <label for="date_from" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Date From</label>
            <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
        </div>
        <div class="col-6 col-md-3">
            <label for="date_to" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Date To</label>
            <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
        </div>
        <div class="col-6 col-md-3">
            <label for="thematic_id" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Thematic Area</label>
            <select class="form-select form-select-sm" id="thematic_id" name="thematic_id">
                <option value="">All Thematic Areas</option>
                @foreach($thematicAreas as $ta)
                    <option value="{{ $ta->id }}" {{ ($filters['thematic_id'] ?? '') == $ta->id ? 'selected' : '' }}>
                        {{ Str::limit($ta->title, 40) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="dept_id" class="form-label small fw-bold mb-1" style="font-size: 0.75rem;">Department</label>
            <select class="form-select form-select-sm" id="dept_id" name="dept_id">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ ($filters['dept_id'] ?? '') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('analytics') }}" class="btn btn-outline-secondary btn-sm fw-semibold">Reset Filters</a>
            <button type="submit" class="btn btn-dark btn-sm fw-bold"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
        </div>
    </form>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center border-0 shadow-sm">
            <div class="text-muted small text-uppercase fw-bold">Total Grants</div>
            <div class="fs-2 fw-bold text-dark">{{ $totalProjects }}</div>
            <div class="small text-muted">Submitted Proposals</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center border-0 shadow-sm">
            <div class="text-muted small text-uppercase fw-bold">Active Research</div>
            <div class="fs-2 fw-bold text-success">{{ $activeProjects }}</div>
            <div class="small text-muted">In Execution</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center border-0 shadow-sm">
            <div class="text-muted small text-uppercase fw-bold">Completed</div>
            <div class="fs-2 fw-bold text-primary">{{ $completedProjects }}</div>
            <div class="small text-muted">Certified Finished</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 text-center border-0 shadow-sm">
            <div class="text-muted small text-uppercase fw-bold">Under Review</div>
            <div class="fs-2 fw-bold text-warning">{{ $underReviewProjects }}</div>
            <div class="small text-muted">Screening / Review</div>
        </div>
    </div>
</div>

{{-- Financial Health Overview --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm bg-light">
            <span class="text-muted small fw-bold text-uppercase">Requested Budget</span>
            <h4 class="fw-bold mb-0 text-dark">ETB {{ number_format($totalRequested, 2) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm bg-light">
            <span class="text-muted small fw-bold text-uppercase">Approved Budget</span>
            <h4 class="fw-bold mb-0 text-dark">ETB {{ number_format($totalApproved, 2) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm bg-light">
            <span class="text-muted small fw-bold text-uppercase">Disbursed Funds</span>
            <h4 class="fw-bold mb-0 text-success">ETB {{ number_format($totalDisbursed, 2) }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm bg-light">
            <span class="text-muted small fw-bold text-uppercase">Budget Utilization</span>
            <h4 class="fw-bold mb-0 {{ $utilizationRate >= 80 ? 'text-success' : 'text-primary' }}">{{ $utilizationRate }}%</h4>
            <div class="progress mt-1" style="height: 5px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $utilizationRate) }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 1: Status Distribution & Budget by Thematic Area --}}
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2 text-dark"></i>Project Status Distribution</h6>
            <div class="d-flex align-items-center justify-content-center" style="height: 280px;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill me-2 text-dark"></i>Requested vs Approved by Thematic Area (ETB)</h6>
            <div style="height: 280px;">
                <canvas id="budgetBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 2: Monthly Submissions & Publication Indexing --}}
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-dark"></i>Project Submissions Trend (Last 6 Months)</h6>
            <div style="height: 250px;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-journal-bookmark me-2 text-dark"></i>Publication Output Indexation</h6>
            <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
                <canvas id="publicationIndexChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Budget Summary Table --}}
<div class="card card-custom p-4 mb-4 border-0 shadow-sm">
    <h6 class="fw-bold mb-3"><i class="bi bi-table me-2 text-dark"></i>Financial &amp; Governance Breakdown by Thematic Area</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Thematic Area</th>
                    <th>Projects</th>
                    <th>Requested Budget (ETB)</th>
                    <th>Approved Budget (ETB)</th>
                    <th>Governance Tier</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgetByThematic as $row)
                <tr>
                    <td class="fw-bold">{{ $row->thematic_title }}</td>
                    <td><span class="badge bg-dark">{{ $row->project_count }}</span></td>
                    <td class="text-dark fw-bold">{{ number_format($row->total_requested, 2) }}</td>
                    <td class="text-dark fw-bold">{{ number_format($row->total_approved ?? 0, 2) }}</td>
                    <td>
                        @if($row->total_requested >= 500000)
                            <span class="badge bg-dark">RCSC / VP Tier</span>
                        @else
                            <span class="badge bg-secondary">College Dean Tier</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No submitted research proposals match the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Chart.js Scripts --}}
<script src="{{ asset('vendor/chart.js/chart.umd.min.js') }}"></script>
<script>
    // 1. Status Doughnut Chart
    const statusLabels = @json($statusLabels);
    const statusData   = @json($statusData);
    const statusColors = ['#0f3e2e', '#047857', '#10b981', '#6ee7b7', '#3b82f6', '#f59e0b', '#ef4444', '#64748b'];

    new Chart(document.getElementById('statusPieChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors.slice(0, statusLabels.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 10 } } }
            }
        }
    });

    // 2. Budget Bar Chart (Requested vs Approved)
    const thematicLabels   = @json($thematicLabels);
    const thematicBudgets  = @json($thematicBudgets);
    const thematicApproved = @json($thematicApproved);

    new Chart(document.getElementById('budgetBarChart'), {
        type: 'bar',
        data: {
            labels: thematicLabels,
            datasets: [
                {
                    label: 'Requested (ETB)',
                    data: thematicBudgets,
                    backgroundColor: 'rgba(15, 62, 46, 0.4)',
                    borderColor: '#0f3e2e',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Approved (ETB)',
                    data: thematicApproved,
                    backgroundColor: 'rgba(4, 120, 87, 0.85)',
                    borderColor: '#047857',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 } } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: val => 'ETB ' + Number(val).toLocaleString() }
                },
                x: { ticks: { font: { size: 9 } } }
            }
        }
    });

    // 3. Monthly Submissions Trend Line Chart
    const monthlyLabels = @json($monthlyLabels);
    const monthlyData   = @json($monthlySubmissions);

    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'New Proposals',
                data: monthlyData,
                borderColor: '#047857',
                backgroundColor: 'rgba(4, 120, 87, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#0f3e2e'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { font: { size: 10 } } }
            }
        }
    });

    // 4. Publication Indexing Polar/Pie Chart
    const pubIndexObj = @json($publicationsByIndex);
    const pubLabels = Object.keys(pubIndexObj);
    const pubCounts = Object.values(pubIndexObj);

    new Chart(document.getElementById('publicationIndexChart'), {
        type: 'pie',
        data: {
            labels: pubLabels.length > 0 ? pubLabels : ['Unindexed / Pending'],
            datasets: [{
                data: pubCounts.length > 0 ? pubCounts : [0],
                backgroundColor: ['#0f3e2e', '#047857', '#3b82f6', '#f59e0b', '#8b5cf6'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 10 } } }
            }
        }
    });
</script>
@endsection
