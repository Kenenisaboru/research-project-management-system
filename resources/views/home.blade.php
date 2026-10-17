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
                    <i class="bi bi-diagram-3 me-2"></i> Lifecycle &amp; Rubric
                </a>
                <a href="{{ route('support') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                    <i class="bi bi-headset me-2"></i> Helpdesk &amp; FAQ
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

        {{-- Unique Section 1: Priority Research Thematic Areas --}}
        <section class="py-4 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1">Priority Research Thematic Areas</h2>
                    <p class="text-muted small mb-0">Current institutional grant call priorities for academic faculty proposals:</p>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2 mt-2 mt-md-0 align-self-start align-self-md-auto" style="font-size: 0.8rem;">
                    <i class="bi bi-check-circle-fill text-dark me-1"></i> Active Funding Cycle
                </span>
            </div>

            <div class="row g-3">
                @forelse($thematicAreas as $ta)
                    <div class="col-12 col-md-4">
                        <div class="card h-100 border rounded-3 p-4 bg-white shadow-xs d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-2">
                                    <span class="badge bg-dark fw-semibold" style="font-size: 0.72rem;">
                                        {{ $ta->category ?? 'Research Priority' }}
                                    </span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-2" style="font-size: 1rem; line-height: 1.4;">
                                    {{ $ta->title }}
                                </h3>
                                <p class="text-muted mb-3" style="font-size: 0.88rem; line-height: 1.5;">
                                    {{ $ta->description }}
                                </p>
                            </div>
                            <div class="pt-2 border-top text-muted small d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-tag me-1"></i> Thematic Grant</span>
                                <span class="fw-bold text-dark">Open for Intake</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="p-3 rounded bg-light border text-center text-muted small">
                            Institutional priority areas currently open for faculty submissions.
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Unique Section 2: Core Institutional Standards --}}
        <section class="pt-4 border-top">
            <h2 class="h5 fw-bold text-dark mb-3">Institutional Research Governance Principles</h2>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs">
                        <div class="fw-bold text-dark mb-1 fs-6">
                            <i class="bi bi-shield-lock text-dark me-2"></i>Double-Blind Integrity
                        </div>
                        <p class="text-muted small mb-0 lh-sm">
                            Author identities and affiliations are masked dynamically throughout academic peer review to guarantee impartial, merit-first evaluation.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs">
                        <div class="fw-bold text-dark mb-1 fs-6">
                            <i class="bi bi-sliders text-dark me-2"></i>Dual-Tier Governance
                        </div>
                        <p class="text-muted small mb-0 lh-sm">
                            Proposals under ETB 500k are expedited through College Deans, while major institutional grants (&ge;500k) receive formal RCSC Council review.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 border rounded-3 p-3 bg-white shadow-xs">
                        <div class="fw-bold text-dark mb-1 fs-6">
                            <i class="bi bi-cash-stack text-dark me-2"></i>Tranche Disbursements
                        </div>
                        <p class="text-muted small mb-0 lh-sm">
                            Grant funds are disbursed in 3 structured tranches (30% Advance, 40% Mid-Term, 30% Final) strictly tied to verifiable progress milestones.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
