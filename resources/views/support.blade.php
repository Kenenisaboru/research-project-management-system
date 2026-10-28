@extends('layouts.app')

@section('content')
<div class="py-4 py-lg-5">
    <div class="container">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Support &amp; Helpdesk</li>
            </ol>
        </nav>

        <header class="mb-4">
            <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.85rem; letter-spacing: 1px;">
                Research &amp; Community Service Support
            </div>
            <h1 class="h2 fw-bold text-dark mb-2">Directorate Support &amp; Helpdesk</h1>
            <p class="lead text-muted fs-6 mb-0" style="max-width: 720px;">
                Direct contacts, office hours, and answers for research grants, proposal authoring, and system access.
            </p>
        </header>

        <div class="row g-4 mb-4">
            {{-- Left Column: Office & Contacts --}}
            <div class="col-lg-5">
                <div class="card border rounded-3 p-4 bg-white shadow-xs mb-3">
                    <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-geo-alt me-2 text-dark"></i>Office Information
                    </h2>
                    <ul class="list-unstyled text-muted mb-0 d-flex flex-column gap-3" style="font-size: 0.95rem;">
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-building fs-5 text-dark mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark">Office:</strong>
                                <div>Research &amp; Technology Transfer Directorate</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-pin-map fs-5 text-dark mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark">Location:</strong>
                                <div>Administration Building, Level 2, Room 204</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-clock fs-5 text-dark mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="text-dark">Working Hours:</strong>
                                <div>Monday &ndash; Friday: 8:30 AM &ndash; 5:30 PM<br><small class="text-muted">(Lunch break: 12:30 PM &ndash; 1:30 PM)</small></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card border rounded-3 p-4 bg-white shadow-xs">
                    <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-envelope me-2 text-dark"></i>Direct Contact Points
                    </h2>
                    <div class="d-flex flex-column gap-3" style="font-size: 0.95rem;">
                        <div>
                            <div class="fw-bold text-dark">Research Directorate Inquiries</div>
                            <a href="mailto:research-office@rpms.local" class="text-dark fw-semibold text-decoration-none">research-office@rpms.local</a>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">System Access &amp; Technical Support</div>
                            <a href="mailto:support@rpms.local" class="text-dark fw-semibold text-decoration-none">support@rpms.local</a>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Institutional Ethics Review Committee (IRERC)</div>
                            <a href="mailto:irerc@rpms.local" class="text-dark fw-semibold text-decoration-none">irerc@rpms.local</a>
                        </div>
                        <div class="pt-2 border-top">
                            <div class="fw-bold text-dark">Telephone Extension</div>
                            <span class="text-muted font-monospace fs-6">+251 (0) 47 111 0022</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Collapsible FAQs --}}
            <div class="col-lg-7">
                <div class="card border rounded-3 p-4 bg-white shadow-xs h-100">
                    <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-question-circle me-2 text-dark"></i>Frequently Asked Questions
                    </h2>
                    <div class="accordion accordion-flush" id="supportFaq">
                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq1">
                                <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#c1">
                                    Who is eligible to submit research proposals?
                                </button>
                            </h3>
                            <div id="c1" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted" style="font-size: 0.93rem; line-height: 1.6;">
                                    Active university faculty members, research fellows, and technical academic staff with recognized institutional appointments are eligible to submit proposals mapped to designated thematic areas.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#c2">
                                    When is Dean approval vs. RCSC approval required?
                                </button>
                            </h3>
                            <div id="c2" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted" style="font-size: 0.93rem; line-height: 1.6;">
                                    Proposals with requested budgets <strong>under ETB 500,000</strong> are approved directly by the respective College Dean. Proposals with requested budgets <strong>of ETB 500,000 or greater</strong> require formal defense and institutional approval through the Research and Community Service Council (RCSC).
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#c3">
                                    How does the double-blind peer review process work?
                                </button>
                            </h3>
                            <div id="c3" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted" style="font-size: 0.93rem; line-height: 1.6;">
                                    The system dynamically strips all author and departmental affiliations from manuscripts before they reach assigned reviewers. Reviewers evaluate submissions on a standardized 100-point rubric. Scores of 80+ are accepted for funding, 70–79 require minor revisions, and 50–69 require major revisions.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#c4">
                                    How are grant tranches disbursed by the Finance Directorate?
                                </button>
                            </h3>
                            <div id="c4" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted" style="font-size: 0.93rem; line-height: 1.6;">
                                    Grant funding is mobilized across three structured milestone tranches:
                                    <ul class="mt-2 mb-0 ps-3">
                                        <li><strong>Tranche 1 (30% Advance):</strong> Queued upon bilateral contract signing between the PI and Vice President.</li>
                                        <li><strong>Tranche 2 (40% Mid-Term):</strong> Released upon verification and approval of the mid-term progress report.</li>
                                        <li><strong>Tranche 3 (30% Final):</strong> Released upon project completion, final audit, and publication submission.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#c5">
                                    How do I request account access or reset credentials?
                                </button>
                            </h3>
                            <div id="c5" class="accordion-collapse collapse" data-bs-parent="#supportFaq">
                                <div class="accordion-body px-0 py-2 text-muted" style="font-size: 0.93rem; line-height: 1.6;">
                                    User accounts and institutional roles are provisioned by the ICT Directorate mapped to your staff ID. For new accounts, password resets, or role adjustments, email <code class="text-dark">support@rpms.local</code> or visit the ICT Helpdesk.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Navigation --}}
        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary fw-semibold px-4 py-2">
                &larr; Back to Home
            </a>
            <a href="{{ route('lifecycle') }}" class="btn btn-dark fw-semibold px-4 py-2">
                5-Step Lifecycle &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
