@extends('layouts.app')

@section('content')
<div class="page-heading mb-4">
    <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-pdf me-2 text-dark"></i>Institutional Reports Generator</h3>
<<<<<<< HEAD
    <p class="text-muted small mb-0">Generate and download official Gambella University governance, audit, and output PDF documents.</p>
=======
    <p class="text-muted small mb-0">Generate and download official university governance, audit, and output PDF documents.</p>
>>>>>>> upstream/main
</div>

<div class="row g-4">
    {{-- Report 1: Annual Output --}}
    <div class="col-md-6 col-lg-6">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success">
                        <i class="bi bi-journal-text fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Annual Research Output</h5>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">GU-RPMS-AOR</span>
                    </div>
                </div>
                <p class="text-muted small mb-3">
                    Comprehensive annual audit of all funded research grants, certified completion milestones, faculty participation, and recorded peer-reviewed journal publications.
                </p>
            </div>
            <form method="GET" action="{{ route('reports.annual-output') }}" class="d-flex gap-2 align-items-center pt-3 border-top">
                <select name="year" class="form-select form-select-sm" style="max-width: 120px;">
                    @for($y = (int)date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-dark btn-sm fw-bold flex-grow-1">
                    <i class="bi bi-download me-1"></i> Generate PDF
                </button>
            </form>
        </div>
    </div>

    {{-- Report 2: Financial Audit --}}
    <div class="col-md-6 col-lg-6">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-dark">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Grant Expenditure &amp; Financial Audit</h5>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">GU-RPMS-FIN</span>
                    </div>
                </div>
                <p class="text-muted small mb-3">
                    Detailed financial audit with dual-threshold governance classifications (Dean &lt;500k vs RCSC/VP &ge;500k), tranches disbursed vs committed, and procurement capital orders.
                </p>
            </div>
            <form method="GET" action="{{ route('reports.financial-audit') }}" class="d-flex gap-2 align-items-center pt-3 border-top">
                <select name="year" class="form-select form-select-sm" style="max-width: 120px;">
                    @for($y = (int)date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-dark btn-sm fw-bold flex-grow-1">
                    <i class="bi bi-download me-1"></i> Generate PDF
                </button>
            </form>
        </div>
    </div>

    {{-- Report 3: Thematic Area Impact --}}
    <div class="col-md-6 col-lg-6">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info-emphasis">
                        <i class="bi bi-pie-chart-fill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Thematic Area Impact Analysis</h5>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">GU-RPMS-THM</span>
                    </div>
                </div>
                <p class="text-muted small mb-3">
                    Portfolio breakdown by university priority research thematic areas, grant allocation percentages, completed vs active field projects, and funding distribution.
                </p>
            </div>
            <div class="pt-3 border-top">
                <a href="{{ route('reports.thematic-impact') }}" class="btn btn-dark btn-sm fw-bold w-100">
                    <i class="bi bi-download me-1"></i> Generate Impact PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Report 4: Researcher Performance --}}
    <div class="col-md-6 col-lg-6">
        <div class="card card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                        <i class="bi bi-award-fill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Researcher Performance Summary</h5>
                        <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">GU-RPMS-RPR</span>
                    </div>
                </div>
                <p class="text-muted small mb-3">
                    Institutional evaluation metrics of Principal Investigators and Co-Researchers: proposal approval rate, active fieldwork management, and successful project completions.
                </p>
            </div>
            <div class="pt-3 border-top">
                <a href="{{ route('reports.researcher-performance') }}" class="btn btn-dark btn-sm fw-bold w-100">
                    <i class="bi bi-download me-1"></i> Generate Performance PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
