@extends('layouts.app')

@section('content')
<div class="py-2 py-md-3">
    <div class="container" style="max-width: 880px;">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Support</li>
            </ol>
        </nav>

        <header class="mb-3">
            <h1 class="h4 fw-bold text-dark mb-1">Research Support &amp; Helpdesk</h1>
            <p class="text-muted small mb-0">Directorate contacts and guidance for research grants and inquiries.</p>
        </header>

        <div class="row g-2 mb-3">
            {{-- Left Column: Office & Contacts --}}
            <div class="col-md-5">
                <div class="card border rounded p-3 bg-white shadow-xs mb-2">
                    <h2 class="small fw-bold text-dark text-uppercase mb-2 pb-1 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-geo-alt me-1 text-dark"></i> Office Information
                    </h2>
                    <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-building text-dark mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark">Office:</strong> Research Directorate
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-pin-map text-dark mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark">Location:</strong> Admin Bldg, Room 204
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-clock text-dark mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark">Hours:</strong> Mon – Fri, 8:30 AM – 5:30 PM
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card border rounded p-3 bg-white shadow-xs">
                    <h2 class="small fw-bold text-dark text-uppercase mb-2 pb-1 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-envelope me-1 text-dark"></i> Contact Points
                    </h2>
                    <div class="d-flex flex-column gap-2 small">
                        <div>
                            <div class="fw-semibold text-dark">Research Office</div>
                            <a href="mailto:research-office@rpms.local" class="text-muted text-decoration-none">research-office@rpms.local</a>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Technical Support</div>
                            <a href="mailto:support@rpms.local" class="text-muted text-decoration-none">support@rpms.local</a>
                        </div>
                        <div class="pt-2 border-top">
                            <div class="fw-semibold text-dark">Phone Extension</div>
                            <span class="text-muted font-monospace">+251 (0) 47 111 0022</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Compact FAQs --}}
            <div class="col-md-7">
                <div class="card border rounded p-3 bg-white shadow-xs h-100">
                    <h2 class="small fw-bold text-dark text-uppercase mb-2 pb-1 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-question-circle me-1 text-dark"></i> Frequently Asked Questions
                    </h2>
                    <div class="accordion accordion-flush" id="supportFaq">
                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq1">
                                <button class="accordion-button collapsed px-0 py-2 fw-semibold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#c1">
                                    Who can submit research proposals?
                                </button>
                            </h3>
                            <div id="c1" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted small lh-sm">
                                    Active university faculty, researchers, and technical staff can submit proposals mapped to priority areas.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed px-0 py-2 fw-semibold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#c2">
                                    When is Dean vs. RCSC approval required?
                                </button>
                            </h3>
                            <div id="c2" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted small lh-sm">
                                    Budgets under ETB 500,000 are approved by the College Dean. Budgets of ETB 500,000 or more require RCSC Council approval.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed px-0 py-2 fw-semibold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#c3">
                                    How are peer reviews scored?
                                </button>
                            </h3>
                            <div id="c3" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted small lh-sm">
                                    Double-blind scoring on a 100-point rubric. Scores of 80+ are accepted; 70-79 require minor revisions.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed px-0 py-2 fw-semibold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#c4">
                                    How are grant tranches disbursed?
                                </button>
                            </h3>
                            <div id="c4" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted small lh-sm">
                                    Funding is released in 3 tranches: 30% advance on contract, 40% on mid-term report, and 30% on final completion.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed px-0 py-2 fw-semibold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#c5">
                                    How do I obtain account access or reset passwords?
                                </button>
                            </h3>
                            <div id="c5" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted small lh-sm">
                                    Accounts are provisioned with staff credentials. Contact ICT at <code>support@rpms.local</code> for credentials or resets.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Navigation --}}
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                &larr; Home
            </a>
            <a href="{{ route('lifecycle') }}" class="btn btn-sm btn-outline-dark fw-semibold">
                Lifecycle &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
