@extends('layouts.app')

@section('content')
<div class="py-3 py-md-4">
    <div class="container" style="max-width: 1140px;">
        {{-- Breadcrumb & Header --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Support</li>
            </ol>
        </nav>

        <header class="mb-4 mb-md-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 rounded-pill bg-white border shadow-xs">
                <i class="bi bi-headset text-success"></i>
                <span class="small fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">Helpdesk</span>
            </div>
            <h1 class="h2 fw-bold text-dark mb-2">Research Support</h1>
            <p class="text-muted mb-0" style="max-width: 760px;">
                Find contacts and answers for research grants, system access, and funding.
            </p>
        </header>

        <div class="row g-4">
            {{-- Left Column: Directorate Office & Key Contacts --}}
            <div class="col-lg-5">
                <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs mb-3">
                    <h2 class="h6 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-geo-alt-fill me-2 text-success"></i>Visit the Office
                    </h2>
                    <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-building text-dark mt-1"></i>
                            <div>
                                <strong class="text-dark">Office:</strong> Research &amp; Technology Transfer Directorate
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-pin-map text-dark mt-1"></i>
                            <div>
                                <strong class="text-dark">Location:</strong> Main Campus, Administration Building, Level 2, Room 204
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-clock text-dark mt-1"></i>
                            <div>
                                <strong class="text-dark">Working Hours:</strong> Mon – Fri, 8:30 AM – 5:30 PM (Lunch: 12:30 – 1:30 PM)
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs mb-3">
                    <h2 class="h6 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-envelope-at-fill me-2 text-success"></i>Contact Us
                    </h2>
                    <div class="d-flex flex-column gap-3 small">
                        <div>
                            <div class="fw-bold text-dark">Directorate General Inquiries</div>
                            <a href="mailto:research-office@rpms.local" class="text-success text-decoration-none">research-office@rpms.local</a>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Portal Technical Support (IT)</div>
                            <a href="mailto:support@rpms.local" class="text-success text-decoration-none">support@rpms.local</a>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Institutional Ethics Review Board (IRERC)</div>
                            <a href="mailto:irerc@rpms.local" class="text-success text-decoration-none">irerc@rpms.local</a>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Finance &amp; Disbursement Office</div>
                            <a href="mailto:finance@rpms.local" class="text-success text-decoration-none">finance@rpms.local</a>
                        </div>
                        <div class="pt-2 border-top">
                            <div class="fw-bold text-dark">Telephone Extension</div>
                            <span class="text-muted font-monospace">+251 (0) 47 111 0022</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Frequently Asked Questions (FAQ) --}}
            <div class="col-lg-7">
                <div class="card border rounded-3 p-3 p-md-4 bg-white shadow-xs">
                    <h2 class="h6 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-question-circle-fill me-2 text-success"></i>Common Questions
                    </h2>

                    <div class="accordion accordion-flush" id="rpmsFaqAccordion">
                        {{-- FAQ 1 --}}
                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faqHeadingOne">
                                <button class="accordion-button collapsed px-0 fw-bold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="false" aria-controls="faqCollapseOne">
                                    Who can submit a proposal?
                                </button>
                            </h3>
                            <div id="faqCollapseOne" class="accordion-collapse collapse" aria-labelledby="faqHeadingOne" data-bs-parent="#rpmsFaqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    Active faculty, researchers, and technical staff with a university appointment can submit proposals.
                                </div>
                            </div>
                        </div>

                        {{-- FAQ 2 --}}
                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faqHeadingTwo">
                                <button class="accordion-button collapsed px-0 fw-bold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                                    When is Dean or RCSC approval needed?
                                </button>
                            </h3>
                            <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#rpmsFaqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    Proposals with requested budgets under ETB 500,000 are approved directly by the respective College Dean. Proposals with requested budgets of ETB 500,000 or greater require formal institutional defense and council approval through the Research and Community Service Council (RCSC).
                                </div>
                            </div>
                        </div>

                        {{-- FAQ 3 --}}
                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faqHeadingThree">
                                <button class="accordion-button collapsed px-0 fw-bold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                                    How does peer review work?
                                </button>
                            </h3>
                            <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#rpmsFaqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    Under double-blind protocols, the identity and departmental affiliation of the Principal Investigator are anonymized before proposals reach reviewers. Likewise, the identity and remarks of the reviewer remain confidential from the PI.
                                </div>
                            </div>
                        </div>

                        {{-- FAQ 4 --}}
                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header" id="faqHeadingFour">
                                <button class="accordion-button collapsed px-0 fw-bold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                                    When is funding released?
                                </button>
                            </h3>
                            <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour" data-bs-parent="#rpmsFaqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    Grant funds are mobilized in three structured tranches:
                                    <ul class="mb-0 mt-2 ps-3">
                                        <li><strong>Tranche 1 (30% Advance):</strong> Auto-queued upon bilateral contract execution by PI and VP.</li>
                                        <li><strong>Tranche 2 (40% Mid-Term):</strong> Released upon verification of the mid-term progress report.</li>
                                        <li><strong>Tranche 3 (30% Final):</strong> Released upon full project completion and publication submission.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- FAQ 5 --}}
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingFive">
                                <button class="accordion-button collapsed px-0 fw-bold text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFive" aria-expanded="false" aria-controls="faqCollapseFive">
                                    How do I reset my password?
                                </button>
                            </h3>
                            <div id="faqCollapseFive" class="accordion-collapse collapse" aria-labelledby="faqHeadingFive" data-bs-parent="#rpmsFaqAccordion">
                                <div class="accordion-body px-0 text-muted small">
                                    RPMS user accounts are provisioned by the ICT Directorate and mapped to your institutional staff ID. If you forgot your password or need an account created, contact <code>support@rpms.local</code> or visit the ICT office.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
