@extends('layouts.app')

@section('content')
<div class="landing-page-wrapper py-3 py-md-4">
    <div class="container" style="max-width: 1140px;">
        {{-- ── 1. Hero Section (Simple, Clean, Easy) ──────────────────────────────── --}}
        <section class="text-center py-4 py-md-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-white border shadow-xs">
                <i class="bi bi-mortarboard-fill text-success"></i>
                <span class="small fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Research Portal</span>
            </div>

            <h1 class="display-6 fw-bold text-dark mb-3 mx-auto" style="max-width: 820px; letter-spacing: -0.02em;">
                Manage Your Research Projects Easily
            </h1>

            <p class="lead text-muted mb-4 mx-auto" style="max-width: 680px; font-size: 1.05rem;">
                Submit proposals, review research, manage funding, and track progress in one place.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2 gap-sm-3 mb-3 mb-md-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-speedometer2"></i> Open My Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-success btn-lg px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Research Portal
                    </a>
                @endauth
                <a href="{{ route('research.catalog') }}" class="btn btn-outline-dark btn-lg px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-journal-text"></i> Browse Research
                </a>
            </div>

            {{-- Clean Metrics Ribbon --}}
            <div class="row g-2 g-sm-3 justify-content-center">
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="fs-3 fw-bold text-dark">{{ $stats['projects'] ?? 4 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Projects</small>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="fs-3 fw-bold text-dark">{{ $stats['thematics'] ?? 3 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Research Areas</small>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="fs-3 fw-bold text-dark">{{ $stats['colleges'] ?? 3 }}</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Participating Colleges</small>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-white border text-center shadow-xs h-100">
                        <div class="fs-3 fw-bold text-success">100%</div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Peer-Review Rate</small>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── 2. What RPMS Does (3 Clean Simple Cards) ───────────────────────── --}}
        <section class="py-3 py-md-4 border-top">
            <div class="text-center mb-3 mb-md-4">
                <h2 class="h4 fw-bold text-dark mb-1">Core Portal Features</h2>
                <p class="text-muted small mb-0">Everything you need to manage research in one place.</p>
            </div>

            <div class="row g-3 g-md-4">
                <div class="col-md-4">
                    <div class="card h-100 border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="rounded-3 bg-success-subtle text-success d-inline-flex align-items-center justify-content-center p-2 mb-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-file-earmark-plus-fill fs-5"></i>
                        </div>
                        <h3 class="h6 fw-bold text-dark mb-2">Submit a Proposal</h3>
                        <p class="text-muted small mb-0 lh-base">
                            Create and submit your research proposal online.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="rounded-3 bg-success-subtle text-success d-inline-flex align-items-center justify-content-center p-2 mb-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <h3 class="h6 fw-bold text-dark mb-2">Review Proposals</h3>
                        <p class="text-muted small mb-0 lh-base">
                            Evaluate proposals through a clear and fair review process.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="rounded-3 bg-success-subtle text-success d-inline-flex align-items-center justify-content-center p-2 mb-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-cash-stack fs-5"></i>
                        </div>
                        <h3 class="h6 fw-bold text-dark mb-2">Manage Funding</h3>
                        <p class="text-muted small mb-0 lh-base">
                            Track approvals, milestones, budgets, and payments.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── 3. Quick Navigation Links (Dedicated Pages) ─────────────────────── --}}
        <section class="py-4 border-top">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-diagram-3-fill text-success fs-5"></i>
                                <h3 class="h6 fw-bold text-dark mb-0">Research Lifecycle &amp; Rubric</h3>
                            </div>
                            <p class="text-muted small mb-3">
                                Learn how proposals are submitted, reviewed, approved, and funded.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('lifecycle') }}" class="btn btn-sm btn-outline-success fw-semibold">
                                See the Research Process <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-headset text-success fs-5"></i>
                                <h3 class="h6 fw-bold text-dark mb-0">Directorate Office &amp; Support</h3>
                            </div>
                            <p class="text-muted small mb-3">
                                Find answers, office information, and help with research grants.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('support') }}" class="btn btn-sm btn-outline-success fw-semibold">
                                Get Help <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── 4. Simple Call to Action Banner ─────────────────────────────────── --}}
        <section class="my-4 py-4 px-3 px-md-5 rounded-4 bg-light border text-center shadow-xs">
            <h3 class="h5 fw-bold text-dark mb-2">Ready to manage your research?</h3>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 540px;">
                Log in to submit proposals, review projects, or check your research progress.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-success fw-bold px-4">Go to My Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-success fw-bold px-4">Login to the Portal</a>
                    <a href="{{ route('research.catalog') }}" class="btn btn-outline-secondary fw-semibold px-3">Browse Research</a>
                @endauth
            </div>
        </section>
    </div>
</div>
@endsection
