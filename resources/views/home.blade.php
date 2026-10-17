@extends('layouts.app')

@section('content')
<div class="py-2 py-md-3">
    <div class="container" style="max-width: 880px;">
        {{-- Hero --}}
        <section class="text-center py-3">
            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.6px;">
                Research &amp; Technology Transfer
            </div>
            <h1 class="h4 fw-bold text-dark mb-1">
                Research Project Management System
            </h1>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 520px;">
                Manage proposals, peer reviews, approvals, and grant funding in one place.
            </p>

            <div class="d-flex justify-content-center gap-2 mb-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-dark btn-sm px-3 fw-semibold">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-dark btn-sm px-3 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </a>
                @endauth
                <a href="{{ route('research.catalog') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
                    <i class="bi bi-journal-text me-1"></i> Research Catalog
                </a>
            </div>

            {{-- Metrics Strip --}}
            <div class="row g-2 justify-content-center">
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-white border text-center shadow-xs">
                        <div class="fw-bold text-dark fs-5">{{ $stats['projects'] ?? 4 }}</div>
                        <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Proposals</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-white border text-center shadow-xs">
                        <div class="fw-bold text-dark fs-5">{{ $stats['thematics'] ?? 3 }}</div>
                        <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Research Areas</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-white border text-center shadow-xs">
                        <div class="fw-bold text-dark fs-5">{{ $stats['colleges'] ?? 3 }}</div>
                        <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Colleges</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-white border text-center shadow-xs">
                        <div class="fw-bold text-dark fs-5">100%</div>
                        <div class="text-muted text-uppercase" style="font-size: 0.68rem;">Reviewed</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Core Features --}}
        <section class="py-3 border-top">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="card h-100 border rounded p-3 bg-white shadow-xs">
                        <div class="fw-bold text-dark small mb-1">
                            <i class="bi bi-file-earmark-text text-dark me-1"></i> 1. Proposals
                        </div>
                        <p class="text-muted small mb-0 lh-sm">
                            Submit research proposals mapped to university priority areas.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border rounded p-3 bg-white shadow-xs">
                        <div class="fw-bold text-dark small mb-1">
                            <i class="bi bi-shield-check text-dark me-1"></i> 2. Peer Review
                        </div>
                        <p class="text-muted small mb-0 lh-sm">
                            Double-blind evaluation scored on a 100-point rubric.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border rounded p-3 bg-white shadow-xs">
                        <div class="fw-bold text-dark small mb-1">
                            <i class="bi bi-cash-stack text-dark me-1"></i> 3. Grants
                        </div>
                        <p class="text-muted small mb-0 lh-sm">
                            Tiered budget approvals, contracts, and milestone payments.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Quick Links --}}
        <section class="py-2 border-top">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="card border rounded p-2 px-3 bg-white shadow-xs h-100 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold text-dark small">Research Lifecycle &amp; Rubric</div>
                            <div class="text-muted" style="font-size: 0.72rem;">5-step process and evaluation guide</div>
                        </div>
                        <a href="{{ route('lifecycle') }}" class="btn btn-sm btn-outline-dark ms-2 py-1 px-2" style="font-size: 0.72rem;">
                            View &rarr;
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border rounded p-2 px-3 bg-white shadow-xs h-100 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold text-dark small">Directorate &amp; Support</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Office contacts, hours, and FAQ</div>
                        </div>
                        <a href="{{ route('support') }}" class="btn btn-sm btn-outline-dark ms-2 py-1 px-2" style="font-size: 0.72rem;">
                            View &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
