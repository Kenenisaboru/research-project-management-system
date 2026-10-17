@extends('layouts.app')

@section('content')
<div class="py-3 py-md-4">
    <div class="container" style="max-width: 1140px;">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Research Lifecycle</li>
            </ol>
        </nav>

        <header class="mb-4 mb-md-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 rounded-pill bg-white border shadow-xs">
                <i class="bi bi-diagram-3-fill text-success"></i>
                <span class="small fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Research Process</span>
            </div>
            <h1 class="h2 fw-bold text-dark mb-2">Research Project Process</h1>
            <p class="text-muted mb-0" style="max-width: 760px;">
                Follow each step from proposal submission to final certification.
            </p>
        </header>

        {{-- ── 1. The 5 Phases (Vertical Step Progression) ─────────────────────── --}}
        <section class="mb-5">
            <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-signpost-split me-2 text-success"></i>Five Simple Steps
            </h2>

            <div class="row g-3 g-md-4">
                {{-- Phase 1 --}}
                <div class="col-12">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                1
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <h3 class="h6 fw-bold text-dark mb-0">1. Submit Proposal</h3>
                                    <span class="badge bg-light text-dark border">Lead Actor: Principal Investigator (PI)</span>
                                </div>
                                <p class="text-muted small mb-2">
                                    Submit a proposal that matches an approved research area.
                                </p>
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Required:</strong> Title, abstract, method, budget, and proposal file.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phase 2 --}}
                <div class="col-12">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                2
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <h3 class="h6 fw-bold text-dark mb-0">2. Department Screening</h3>
                                    <span class="badge bg-light text-dark border">Lead Actor: Department Head (DH)</span>
                                </div>
                                <p class="text-muted small mb-2">
                                    The Department Head checks relevance, feasibility, and compliance.
                                </p>
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Outcome:</strong> Approved proposals move forward; corrections are returned to the researcher.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phase 3 --}}
                <div class="col-12">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                3
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <h3 class="h6 fw-bold text-dark mb-0">3. Peer Review</h3>
                                    <span class="badge bg-light text-dark border">Lead Actors: Research Coordinator &amp; Peer Examiners</span>
                                </div>
                                <p class="text-muted small mb-2">
                                    Qualified reviewers evaluate proposals without seeing researcher identities.
                                </p>
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Evaluation:</strong> Proposals are scored out of 100.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phase 4 --}}
                <div class="col-12">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                4
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <h3 class="h6 fw-bold text-dark mb-0">4. Approval and Contract</h3>
                                    <span class="badge bg-light text-dark border">Lead Actors: Dean / RCSC Council &amp; Vice President</span>
                                </div>
                                <p class="text-muted small mb-2">
                                    Proposals are approved by the Dean or RCSC based on the requested budget.
                                </p>
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Contracting:</strong> Once approved, the PI and Vice President (ARTTCS) execute a binding bilateral research contract (`Active` status).
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phase 5 --}}
                <div class="col-12">
                    <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                5
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <h3 class="h6 fw-bold text-dark mb-0">5. Funding and Completion</h3>
                                    <span class="badge bg-light text-dark border">Lead Actors: Finance Office &amp; Research Coordinator</span>
                                </div>
                                <p class="text-muted small mb-2">
                                    Funding is released in stages after each milestone is verified.
                                </p>
                                <div class="bg-light p-2 rounded small text-muted">
                                    <strong>Completion:</strong> A certificate is issued after final review and publication submission.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── 2. Standardized 100-Point Scoring Rubric ───────────────────────── --}}
        <section class="mb-5">
            <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-card-checklist me-2 text-success"></i>Evaluation Rubric
            </h2>

            <div class="table-responsive bg-white rounded-3 border shadow-xs mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 35%;">Criteria</th>
                            <th style="width: 15%;" class="text-center">Weight</th>
                            <th>What Reviewers Check</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <i class="bi bi-lightbulb text-success me-2"></i>Research Merit
                            </td>
                            <td class="text-center fw-bold text-success">25 Points</td>
                            <td class="text-muted">Originality of research questions, intellectual depth, literature grounding, and theoretical foundation.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <i class="bi bi-gear-wide-connected text-success me-2"></i>Method
                            </td>
                            <td class="text-center fw-bold text-success">25 Points</td>
                            <td class="text-muted">Rigor of empirical design, sampling adequacy, analytical instruments, and experimental reproducibility.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <i class="bi bi-calendar2-check text-success me-2"></i>Feasibility
                            </td>
                            <td class="text-center fw-bold text-success">20 Points</td>
                            <td class="text-muted">Work-package sequencing, milestone realism, institutional facilities, and investigator capabilities.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <i class="bi bi-globe-americas text-success me-2"></i>Community Impact
                            </td>
                            <td class="text-center fw-bold text-success">20 Points</td>
                            <td class="text-muted">Tangible benefits to local communities, industry linkage, policy relevance, and sustainability outcomes.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <i class="bi bi-calculator text-success me-2"></i>Budget
                            </td>
                            <td class="text-center fw-bold text-success">10 Points</td>
                            <td class="text-muted">Itemized cost justification, compliance with institutional finance regulations, and value for funding.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Verdict Thresholds --}}
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <div class="p-2 p-md-3 rounded bg-white border text-center">
                        <span class="badge bg-success mb-1">80 – 100 Points</span>
                        <div class="fw-bold small text-dark">Accepted</div>
                        <small class="text-muted" style="font-size:0.7rem;">Recommended for grant approval</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 p-md-3 rounded bg-white border text-center">
                        <span class="badge bg-secondary mb-1">70 – 79 Points</span>
                        <div class="fw-bold small text-dark">Minor Modifications</div>
                        <small class="text-muted" style="font-size:0.7rem;">Approval pending revisions</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 p-md-3 rounded bg-white border text-center">
                        <span class="badge bg-secondary mb-1">50 – 69 Points</span>
                        <div class="fw-bold small text-dark">Major Modifications</div>
                        <small class="text-muted" style="font-size:0.7rem;">Returned for substantial revision</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 p-md-3 rounded bg-white border text-center">
                        <span class="badge bg-secondary mb-1">&lt; 50 Points</span>
                        <div class="fw-bold small text-dark">Rejected</div>
                        <small class="text-muted" style="font-size:0.7rem;">Does not meet minimum criteria</small>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── 3. Call to Action Banner ────────────────────────────────────────── --}}
        <section class="p-4 rounded-4 bg-light border text-center shadow-xs">
            <h3 class="h6 fw-bold text-dark mb-2">Ready to get started?</h3>
            <p class="text-muted small mb-3">Log in to submit, review, or track research projects.</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success fw-bold px-4">Open Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-success fw-bold px-4">Login to RPMS</a>
                    <a href="{{ route('support') }}" class="btn btn-sm btn-outline-secondary fw-semibold px-3">Get Help</a>
                @endauth
            </div>
        </section>
    </div>
</div>
@endsection
