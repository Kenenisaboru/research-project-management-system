@extends('layouts.app')

@section('content')
<div class="py-4 py-lg-5">
    <div class="container">
        {{-- Hero Section --}}
        <section class="text-center pb-4 mb-3">
            <div class="text-uppercase fw-bold text-muted mb-2" style="font-size: 0.85rem; letter-spacing: 1.2px;">
                Research &amp; Technology Transfer Directorate
            </div>
            <h1 class="display-6 fw-bold text-dark mb-3">
                Research Project Management System
            </h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 680px; font-size: 1.15rem; line-height: 1.6;">
                A centralized institutional portal for authoring research proposals, double-blind peer review, dual-threshold governance, and milestone grant funding.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-dark px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-speedometer2 me-2"></i> Open Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-dark px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Portal
                    </a>
                @endauth
                <a href="{{ route('lifecycle') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold">
                    <i class="bi bi-diagram-3 me-2"></i> 5-Stage Lifecycle Guide
                </a>
                <a href="{{ route('support') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                    <i class="bi bi-headset me-2"></i> Directorate Support
                </a>
            </div>

            {{-- 3 Proportional Key Metrics --}}
            <div class="row g-3 justify-content-center pt-2">
                <div class="col-12 col-md-4">
                    <div class="p-3 p-md-4 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-1">{{ $stats['projects'] ?? 4 }}</div>
                        <div class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Active Proposals in Intake</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 p-md-4 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-1">{{ $stats['thematics'] ?? 3 }}</div>
                        <div class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Priority Research Areas</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 p-md-4 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="display-6 fw-bold text-dark mb-1">{{ $stats['colleges'] ?? 3 }}</div>
                        <div class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Colleges &amp; Academic Faculties</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2 Balanced Institutional Cards --}}
        <section class="pt-3 border-top">
            <div class="row g-4">
                {{-- Card 1: 5-Stage Lifecycle --}}
                <div class="col-lg-6">
                    <div class="card h-100 border rounded-3 p-4 bg-white shadow-xs d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-dark rounded-circle p-2 fs-6"><i class="bi bi-diagram-3"></i></span>
                                <h2 class="h5 fw-bold text-dark mb-0">5-Stage Research Lifecycle</h2>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                                Standardized institutional workflow governing proposal intake, peer review evaluation, and grant completion:
                            </p>
                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="p-2 px-3 rounded bg-light border-start border-3 border-dark d-flex align-items-center gap-3">
                                    <span class="badge bg-dark px-2 py-1">1 &ndash; 2</span>
                                    <span class="text-dark fw-medium" style="font-size: 0.92rem;">Proposal Intake &amp; Departmental Quality Screening</span>
                                </div>
                                <div class="p-2 px-3 rounded bg-light border-start border-3 border-dark d-flex align-items-center gap-3">
                                    <span class="badge bg-dark px-2 py-1">3</span>
                                    <span class="text-dark fw-medium" style="font-size: 0.92rem;">Double-Blind Peer Review on 100-Point Rubric</span>
                                </div>
                                <div class="p-2 px-3 rounded bg-light border-start border-3 border-dark d-flex align-items-center gap-3">
                                    <span class="badge bg-dark px-2 py-1">4 &ndash; 5</span>
                                    <span class="text-dark fw-medium" style="font-size: 0.92rem;">Dean / RCSC Approval &amp; 3-Tranche Grant Funding</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('lifecycle') }}" class="btn btn-outline-dark fw-semibold py-2">
                            Explore 5-Step Lifecycle &amp; Rubric &rarr;
                        </a>
                    </div>
                </div>

                {{-- Card 2: Directorate & Support --}}
                <div class="col-lg-6">
                    <div class="card h-100 border rounded-3 p-4 bg-white shadow-xs d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-dark rounded-circle p-2 fs-6"><i class="bi bi-headset"></i></span>
                                <h2 class="h5 fw-bold text-dark mb-0">Directorate Office &amp; Helpdesk</h2>
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                                Direct assistance for research faculty, proposal formatting, staff credentials, and institutional ethics:
                            </p>
                            <div class="d-flex flex-column gap-3 mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-building fs-5 text-dark mt-1 flex-shrink-0"></i>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Directorate Office Location</div>
                                        <div class="text-muted" style="font-size: 0.9rem;">Administration Building, Level 2, Room 204</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-clock fs-5 text-dark mt-1 flex-shrink-0"></i>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Working Hours</div>
                                        <div class="text-muted" style="font-size: 0.9rem;">Monday &ndash; Friday, 8:30 AM &ndash; 5:30 PM (Lunch: 12:30 &ndash; 1:30 PM)</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-envelope fs-5 text-dark mt-1 flex-shrink-0"></i>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Technical Support &amp; Accounts</div>
                                        <div class="text-muted" style="font-size: 0.9rem;"><a href="mailto:support@rpms.local" class="text-decoration-none text-dark fw-semibold">support@rpms.local</a> &middot; Telephone Ext: +251 (0) 47 111 0022</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('support') }}" class="btn btn-outline-dark fw-semibold py-2">
                            View Support &amp; FAQ &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
