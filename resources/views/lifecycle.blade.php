@extends('layouts.app')

@section('content')
<div class="py-2 py-md-3">
    <div class="container" style="max-width: 880px;">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Lifecycle</li>
            </ol>
        </nav>

        <header class="mb-3">
            <h1 class="h4 fw-bold text-dark mb-1">Research Project Lifecycle</h1>
            <p class="text-muted small mb-0">5-step review workflow and 100-point evaluation rubric.</p>
        </header>

        {{-- 1. The 5 Steps --}}
        <section class="card border rounded p-3 bg-white shadow-xs mb-3">
            <h2 class="small fw-bold text-dark text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                5-Step Workflow
            </h2>
            <div class="d-flex flex-column gap-2 small">
                <div class="p-2 rounded bg-light border-start border-3 border-dark d-flex align-items-start gap-2">
                    <span class="badge bg-dark rounded-circle px-2 py-1 flex-shrink-0" style="font-size: 0.7rem;">1</span>
                    <div>
                        <strong class="text-dark">Proposal Submission</strong>
                        <span class="text-muted">(PI) &mdash; Author and submit proposal with budget breakdown.</span>
                    </div>
                </div>

                <div class="p-2 rounded bg-light border-start border-3 border-dark d-flex align-items-start gap-2">
                    <span class="badge bg-dark rounded-circle px-2 py-1 flex-shrink-0" style="font-size: 0.7rem;">2</span>
                    <div>
                        <strong class="text-dark">Department Screening</strong>
                        <span class="text-muted">(DH) &mdash; Verify departmental relevance, feasibility, and eligibility.</span>
                    </div>
                </div>

                <div class="p-2 rounded bg-light border-start border-3 border-dark d-flex align-items-start gap-2">
                    <span class="badge bg-dark rounded-circle px-2 py-1 flex-shrink-0" style="font-size: 0.7rem;">3</span>
                    <div>
                        <strong class="text-dark">Blind Peer Review</strong>
                        <span class="text-muted">(Examiner) &mdash; Double-blind 100-point rubric scoring by academic reviewers.</span>
                    </div>
                </div>

                <div class="p-2 rounded bg-light border-start border-3 border-dark d-flex align-items-start gap-2">
                    <span class="badge bg-dark rounded-circle px-2 py-1 flex-shrink-0" style="font-size: 0.7rem;">4</span>
                    <div>
                        <strong class="text-dark">Governance Approval</strong>
                        <span class="text-muted">(Dean / RCSC &amp; VP) &mdash; Budget approval (&lt;500k Dean; &ge;500k RCSC) and bilateral contract.</span>
                    </div>
                </div>

                <div class="p-2 rounded bg-light border-start border-3 border-dark d-flex align-items-start gap-2">
                    <span class="badge bg-dark rounded-circle px-2 py-1 flex-shrink-0" style="font-size: 0.7rem;">5</span>
                    <div>
                        <strong class="text-dark">Funding &amp; Completion</strong>
                        <span class="text-muted">(Finance &amp; Coordinator) &mdash; Milestone payments (30%/40%/30%) and certificate.</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. 100-Point Scoring Rubric --}}
        <section class="card border rounded p-3 bg-white shadow-xs mb-3">
            <h2 class="small fw-bold text-dark text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                100-Point Evaluation Rubric
            </h2>

            <div class="table-responsive mb-2">
                <table class="table table-sm table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-2">Criteria</th>
                            <th class="text-center" style="width: 80px;">Weight</th>
                            <th>Scope</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-2 fw-semibold text-dark">Scientific &amp; Technical Merit</td>
                            <td class="text-center fw-bold text-dark">25 Pts</td>
                            <td class="text-muted">Originality, depth, and literature grounding.</td>
                        </tr>
                        <tr>
                            <td class="ps-2 fw-semibold text-dark">Methodology &amp; Design</td>
                            <td class="text-center fw-bold text-dark">25 Pts</td>
                            <td class="text-muted">Empirical rigor and analytical design.</td>
                        </tr>
                        <tr>
                            <td class="ps-2 fw-semibold text-dark">Feasibility &amp; Timeline</td>
                            <td class="text-center fw-bold text-dark">20 Pts</td>
                            <td class="text-muted">Milestone realism and work plan sequencing.</td>
                        </tr>
                        <tr>
                            <td class="ps-2 fw-semibold text-dark">Community Impact</td>
                            <td class="text-center fw-bold text-dark">20 Pts</td>
                            <td class="text-muted">Tangible socio-economic benefits and relevance.</td>
                        </tr>
                        <tr>
                            <td class="ps-2 fw-semibold text-dark">Budget &amp; Cost Realism</td>
                            <td class="text-center fw-bold text-dark">10 Pts</td>
                            <td class="text-muted">Itemized justification and cost compliance.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Verdict Thresholds --}}
            <div class="row g-1 text-center pt-2 border-top">
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-light border small">
                        <span class="badge bg-dark font-monospace mb-1">80–100</span>
                        <div class="fw-semibold text-dark" style="font-size: 0.72rem;">Accepted</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-light border small">
                        <span class="badge bg-dark font-monospace mb-1">70–79</span>
                        <div class="fw-semibold text-dark" style="font-size: 0.72rem;">Minor Mods</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-light border small">
                        <span class="badge bg-dark font-monospace mb-1">50–69</span>
                        <div class="fw-semibold text-dark" style="font-size: 0.72rem;">Major Mods</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-2 rounded bg-light border small">
                        <span class="badge bg-dark font-monospace mb-1">&lt;50</span>
                        <div class="fw-semibold text-dark" style="font-size: 0.72rem;">Rejected</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Bottom Navigation --}}
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                &larr; Home
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-dark fw-semibold">
                    Dashboard &rarr;
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-dark fw-semibold">
                    Sign In &rarr;
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
