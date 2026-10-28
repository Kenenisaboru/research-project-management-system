@extends('layouts.app')

@section('content')
<div class="py-4 py-lg-5">
    <div class="container">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Lifecycle &amp; Evaluation</li>
            </ol>
        </nav>

        <header class="mb-4">
            <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.85rem; letter-spacing: 1px;">
                Institutional Governance &amp; Quality Assurance
            </div>
            <h1 class="h2 fw-bold text-dark mb-2">Research Project Lifecycle &amp; Evaluation</h1>
            <p class="lead text-muted fs-6 mb-0" style="max-width: 720px;">
                A standardized 5-step workflow and 100-point double-blind scoring rubric governing grant calls from submission to completion.
            </p>
        </header>

        {{-- 1. The 5 Steps --}}
        <section class="card border rounded-3 p-4 bg-white shadow-xs mb-4">
            <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-diagram-3 me-2 text-dark"></i>5-Step Lifecycle Workflow
            </h2>
            <div class="d-flex flex-column gap-3">
                <div class="p-3 rounded bg-light border-start border-4 border-dark d-flex align-items-start gap-3">
                    <span class="badge bg-dark rounded-circle px-3 py-2 fs-6 flex-shrink-0">1</span>
                    <div>
                        <div class="fw-bold text-dark fs-6">1. Proposal Intake &amp; Submission</div>
                        <div class="text-muted" style="font-size: 0.92rem;">
                            <strong>Principal Investigator (PI)</strong> drafts and submits research proposal mapped to university thematic priority areas with itemized budget breakdowns.
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded bg-light border-start border-4 border-dark d-flex align-items-start gap-3">
                    <span class="badge bg-dark rounded-circle px-3 py-2 fs-6 flex-shrink-0">2</span>
                    <div>
                        <div class="fw-bold text-dark fs-6">2. Departmental Quality Screening</div>
                        <div class="text-muted" style="font-size: 0.92rem;">
                            <strong>Department Head (DH)</strong> evaluates submissions for departmental academic relevance, resource feasibility, and institutional compliance.
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded bg-light border-start border-4 border-dark d-flex align-items-start gap-3">
                    <span class="badge bg-dark rounded-circle px-3 py-2 fs-6 flex-shrink-0">3</span>
                    <div>
                        <div class="fw-bold text-dark fs-6">3. Double-Blind Peer Review</div>
                        <div class="text-muted" style="font-size: 0.92rem;">
                            <strong>Independent Academic Reviewers</strong> evaluate anonymized manuscripts using a 100-point rubric with identities strictly masked on both sides.
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded bg-light border-start border-4 border-dark d-flex align-items-start gap-3">
                    <span class="badge bg-dark rounded-circle px-3 py-2 fs-6 flex-shrink-0">4</span>
                    <div>
                        <div class="fw-bold text-dark fs-6">4. Governance Approval &amp; Bilateral Contract</div>
                        <div class="text-muted" style="font-size: 0.92rem;">
                            Dual-tier budget routing: proposals under ETB 500k are approved by the <strong>College Dean</strong>; proposals &ge; ETB 500k are defended before the <strong>RCSC Council</strong>. PI and Vice President execute a bilateral research contract.
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded bg-light border-start border-4 border-dark d-flex align-items-start gap-3">
                    <span class="badge bg-dark rounded-circle px-3 py-2 fs-6 flex-shrink-0">5</span>
                    <div>
                        <div class="fw-bold text-dark fs-6">5. Grant Funding &amp; Project Completion</div>
                        <div class="text-muted" style="font-size: 0.92rem;">
                            <strong>Finance Directorate</strong> disburses funds in 3 milestone tranches (30% advance, 40% mid-term, 30% final). Upon audit sign-off, an official PDF completion certificate is issued.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. 100-Point Scoring Rubric --}}
        <section class="card border rounded-3 p-4 bg-white shadow-xs mb-4">
            <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-card-checklist me-2 text-dark"></i>100-Point Standardized Evaluation Rubric
            </h2>

            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 py-2">Evaluation Criteria</th>
                            <th class="text-center py-2" style="width: 140px;">Max Score</th>
                            <th class="py-2">Assessment Scope &amp; Rigor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3 fw-semibold text-dark">1. Scientific &amp; Technical Merit</td>
                            <td class="text-center fw-bold text-dark fs-6">25 Pts</td>
                            <td class="text-muted" style="font-size: 0.92rem;">Originality, clarity of problem statement, theoretical depth, and literature grounding.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold text-dark">2. Methodology &amp; Experimental Design</td>
                            <td class="text-center fw-bold text-dark fs-6">25 Pts</td>
                            <td class="text-muted" style="font-size: 0.92rem;">Empirical soundness, data collection rigor, analytical instrumentation, and sampling validity.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold text-dark">3. Feasibility &amp; Work Plan Timeline</td>
                            <td class="text-center fw-bold text-dark fs-6">20 Pts</td>
                            <td class="text-muted" style="font-size: 0.92rem;">Milestone realism, risk mitigation strategies, and adequacy of research team expertise.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold text-dark">4. Community &amp; Socio-Economic Impact</td>
                            <td class="text-center fw-bold text-dark fs-6">20 Pts</td>
                            <td class="text-muted" style="font-size: 0.92rem;">Tangible socio-economic benefits, regional policy relevance, and technology transfer potential.</td>
                        </tr>
                        <tr>
                            <td class="ps-3 fw-semibold text-dark">5. Budget Realism &amp; Cost Justification</td>
                            <td class="text-center fw-bold text-dark fs-6">10 Pts</td>
                            <td class="text-muted" style="font-size: 0.92rem;">Itemized cost realism, market price compliance, and adherence to institutional finance policies.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Verdict Thresholds --}}
            <div class="row g-3 text-center pt-3 border-top">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <span class="badge bg-dark font-monospace fs-6 px-3 py-1 mb-2">80 – 100</span>
                        <div class="fw-bold text-dark">Accepted</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Unconditional funding</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <span class="badge bg-dark font-monospace fs-6 px-3 py-1 mb-2">70 – 79</span>
                        <div class="fw-bold text-dark">Minor Revisions</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Funding upon minor edits</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <span class="badge bg-dark font-monospace fs-6 px-3 py-1 mb-2">50 – 69</span>
                        <div class="fw-bold text-dark">Major Revisions</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Resubmission required</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <span class="badge bg-dark font-monospace fs-6 px-3 py-1 mb-2">&lt; 50</span>
                        <div class="fw-bold text-dark">Rejected</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Ineligible for current call</small>
                    </div>
                </div>
            </div>
        </section>

        {{-- Bottom Navigation --}}
        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary fw-semibold px-4 py-2">
                &larr; Back to Home
            </a>
            <a href="{{ route('support') }}" class="btn btn-dark fw-semibold px-4 py-2">
                Directorate Support &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
