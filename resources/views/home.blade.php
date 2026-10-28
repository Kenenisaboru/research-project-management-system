@extends('layouts.app')

@section('content')
<div class="home-pro-wrapper" x-data="homePipelineSimulator()">
    {{-- Ambient Background Glows --}}
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="ambient-glow glow-3"></div>

    {{-- HERO SECTION --}}
    <section class="hero-pro-section">
        <div class="container hero-container">
            <div class="row align-items-center g-5">
                {{-- Left Column: Hero Typography & CTA --}}
                <div class="col-lg-6 hero-text-col">
                    <div class="hero-badge mb-3">
                        <span class="live-pulse-dot"></span>
                        <span class="badge-text"><i class="bi bi-shield-check me-1 text-warning"></i> Enterprise Research Lifecycle Platform</span>
                    </div>

                    <h1 class="hero-title">
                        Transforming <span class="gradient-text">Scientific Discovery</span> from Proposal to Global Impact.
                    </h1>

                    <p class="hero-lead">
                        The unified digital command center for university research proposals, double-blind peer reviews, IRERC ethics compliance, dual-threshold budget governance, and automated tranche disbursements.
                    </p>

                    <div class="hero-cta-group">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-pro-primary btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i> Open Command Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-pro-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Access Research Portal
                            </a>
                        @endauth
                        <a href="#pipeline-interactive" class="btn btn-pro-glass btn-lg">
                            <i class="bi bi-play-circle me-2"></i> Interactive Workflow Demo
                        </a>
                    </div>

                    {{-- Live Micro-Metrics Pills --}}
                    <div class="hero-metrics-strip mt-4">
                        <div class="metric-pill">
                            <div class="metric-pill-value">ETB 48.5M+</div>
                            <div class="metric-pill-label">Research Grants Managed</div>
                        </div>
                        <div class="metric-pill">
                            <div class="metric-pill-value">100%</div>
                            <div class="metric-pill-label">Double-Blind Integrity</div>
                        </div>
                        <div class="metric-pill">
                            <div class="metric-pill-value">11 Roles</div>
                            <div class="metric-pill-label">End-to-End Governance</div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Interactive Research Pipeline Cockpit --}}
                <div class="col-lg-6 hero-cockpit-col">
                    <div class="cockpit-card">
                        <div class="cockpit-header">
                            <div class="cockpit-dots">
                                <span class="dot-c dot-red"></span>
                                <span class="dot-c dot-amber"></span>
                                <span class="dot-c dot-green"></span>
                            </div>
                            <div class="cockpit-title">
                                <i class="bi bi-cpu-fill text-success me-1"></i> RPMS Governance Engine &bull; Interactive Simulator
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small rounded-pill">
                                <i class="bi bi-broadcast me-1"></i>Live Pipeline
                            </span>
                        </div>

                        <div class="cockpit-body">
                            {{-- Stage Selector Pills --}}
                            <div class="stage-pills-nav mb-3">
                                <template x-for="(st, idx) in stages" :key="idx">
                                    <button type="button"
                                        @click="activeStage = idx"
                                        :class="activeStage === idx ? 'stage-pill-active' : ''"
                                        class="stage-pill-btn">
                                        <span x-text="(idx + 1) + '. ' + st.shortTitle"></span>
                                    </button>
                                </template>
                            </div>

                            {{-- Active Stage Dynamic Display Card --}}
                            <div class="active-stage-display p-3 rounded-3 mb-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="stage-avatar-icon" :class="stages[activeStage].badgeColor">
                                            <i :class="'bi ' + stages[activeStage].icon"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-white" x-text="stages[activeStage].title"></h6>
                                            <small class="text-muted" x-text="'Acting Authority: ' + stages[activeStage].actor"></small>
                                        </div>
                                    </div>
                                    <span class="badge rounded-pill px-3 py-1"
                                          :class="stages[activeStage].statusBadgeClass"
                                          x-text="stages[activeStage].status"></span>
                                </div>
                                <p class="small text-muted mb-2" x-text="stages[activeStage].description"></p>

                                {{-- Live Parameters Grid --}}
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <div class="sub-metric-box">
                                            <div class="sub-label">Governance Tier</div>
                                            <div class="sub-value" x-text="stages[activeStage].tier"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="sub-metric-box">
                                            <div class="sub-label">Financial Action</div>
                                            <div class="sub-value text-success" x-text="stages[activeStage].financial"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Real-Time Tranche Milestone Visualizer --}}
                            <div class="tranche-visualizer p-3 rounded-3 bg-dark-subtle border">
                                <div class="d-flex justify-content-between text-muted small fw-bold mb-2">
                                    <span>Tranche 1 (Advance 30%)</span>
                                    <span>Tranche 2 (Mid-term 40%)</span>
                                    <span>Tranche 3 (Final 30%)</span>
                                </div>
                                <div class="progress" style="height: 10px; background: rgba(255,255,255,0.08);">
                                    <div class="progress-bar bg-success"
                                         :style="'width: ' + stages[activeStage].progressPercent + '%; transition: width 0.4s ease;'"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mt-2">
                                    <span class="text-white" x-text="'Project Stage: ' + stages[activeStage].status"></span>
                                    <span class="fw-bold text-success" x-text="stages[activeStage].progressPercent + '% Lifecycle Completed'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- BENTO GRID: ENTERPRISE CAPABILITIES --}}
    <section id="features" class="bento-section py-5">
        <div class="container">
            <div class="section-title-wrap text-center mb-5">
                <span class="section-eyebrow"><i class="bi bi-grid-1x2-fill me-1"></i> Core Architectural Pillars</span>
                <h2 class="section-heading-lg">Engineered for Rigorous University Research Operations</h2>
                <p class="section-sub-lead mx-auto">
                    Built to institutional specifications supporting complete academic workflows from open grant calls to published scientific impact.
                </p>
            </div>

            <div class="row g-4 bento-grid">
                {{-- Feature 1: Double-Blind Peer Review --}}
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card h-100">
                        <div class="bento-icon-box bg-indigo-subtle text-indigo">
                            <i class="bi bi-eye-slash-fill"></i>
                        </div>
                        <h4 class="bento-title">Double-Blind Peer Review</h4>
                        <p class="bento-text">
                            Researcher identities and institutional affiliations are cryptographically masked. Independent examiners grade methodology, literature, and impact on a 100-point rubric with zero bias.
                        </p>
                        <ul class="bento-checklist">
                            <li><i class="bi bi-check-circle-fill text-success"></i> Automated anonymization of proposal PDFs</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Dual-reviewer consensus scoring</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Coordinator revision assignment engine</li>
                        </ul>
                    </div>
                </div>

                {{-- Feature 2: Dual-Threshold Financial Governance --}}
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card h-100">
                        <div class="bento-icon-box bg-emerald-subtle text-emerald">
                            <i class="bi bi-bank2"></i>
                        </div>
                        <h4 class="bento-title">Dual-Threshold Financial Governance</h4>
                        <p class="bento-text">
                            Strict institutional financial routing automatically detects project budgets and assigns the appropriate ratification tier to eliminate unauthorized expenditure.
                        </p>
                        <ul class="bento-checklist">
                            <li><i class="bi bi-check-circle-fill text-success"></i> &lt; 500,000 ETB: College Dean Level</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> &ge; 500,000 ETB: RCSC &amp; Vice President Tier</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Formal budget ratification decree logs</li>
                        </ul>
                    </div>
                </div>

                {{-- Feature 3: Automated 30/40/30 Tranche Disbursement --}}
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card h-100">
                        <div class="bento-icon-box bg-amber-subtle text-amber">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h4 class="bento-title">30-40-30 Tranche Disbursement</h4>
                        <p class="bento-text">
                            Eliminates lump-sum risk. Funds are released strictly against verified milestones with cryptographic digital vouchers and downloadable bank authorization PDFs.
                        </p>
                        <ul class="bento-checklist">
                            <li><i class="bi bi-check-circle-fill text-success"></i> 30% Advance on bilateral contract signing</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> 40% Mid-term upon coordinator approval</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> 30% Final release with certificate issuance</li>
                        </ul>
                    </div>
                </div>

                {{-- Feature 4: IRERC Ethics Review & Clearance --}}
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card h-100">
                        <div class="bento-icon-box bg-teal-subtle text-teal">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="bento-title">IRERC Ethics Review &amp; Clearance</h4>
                        <p class="bento-text">
                            Integrated Institutional Review Board / Ethics Committee panel. Protocols covering human subjects, animal testing, and biosafety undergo full statutory evaluation before contract activation.
                        </p>
                    </div>
                </div>

                {{-- Feature 5: Dynamic Itemized Budget Calculator --}}
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card h-100">
                        <div class="bento-icon-box bg-cyan-subtle text-cyan">
                            <i class="bi bi-calculator-fill"></i>
                        </div>
                        <h4 class="bento-title">Interactive Budget Builder</h4>
                        <p class="bento-text">
                            Principal Investigators build line-item budgets across personnel, field consumables, lab equipment, and dissemination with live instant subtotal calculations and threshold alerts.
                        </p>
                    </div>
                </div>

                {{-- Feature 6: Research Publication & Paper Awards --}}
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card h-100">
                        <div class="bento-icon-box bg-purple-subtle text-purple">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h4 class="bento-title">Publication &amp; Journal Incentives</h4>
                        <p class="bento-text">
                            Tracks journal indexations (Scopus, Web of Science, PubMed), evaluates impact factors, prevents predatory publishing, and registers official university excellence awards.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- INTERACTIVE WORKFLOW STEPPER --}}
    <section id="pipeline-interactive" class="workflow-stepper-section py-5">
        <div class="container">
            <div class="section-title-wrap text-center mb-5">
                <span class="section-eyebrow"><i class="bi bi-bezier2 me-1"></i> End-to-End Governance</span>
                <h2 class="section-heading-lg">The Complete 6-Stage Research Journey</h2>
                <p class="section-sub-lead mx-auto">
                    Every proposal progresses through an immutable, audit-logged lifecycle with distinct checks and balances.
                </p>
            </div>

            <div class="journey-stepper-wrap">
                <div class="journey-step-item">
                    <div class="step-num">01</div>
                    <div class="step-icon-circle"><i class="bi bi-file-earmark-plus"></i></div>
                    <h5>Proposal &amp; Budget</h5>
                    <p>PI defines objectives, adds co-investigators, and builds the line-item budget.</p>
                </div>

                <div class="journey-step-item">
                    <div class="step-num">02</div>
                    <div class="step-icon-circle"><i class="bi bi-funnel"></i></div>
                    <h5>DH Screening</h5>
                    <p>Department Head verifies academic priority and college relevance.</p>
                </div>

                <div class="journey-step-item">
                    <div class="step-num">03</div>
                    <div class="step-icon-circle"><i class="bi bi-eye-slash"></i></div>
                    <h5>Blind Peer Review</h5>
                    <p>Coordinator assigns 2 examiners who evaluate methodology blindly.</p>
                </div>

                <div class="journey-step-item">
                    <div class="step-num">04</div>
                    <div class="step-icon-circle"><i class="bi bi-shield-lock"></i></div>
                    <h5>Ethics Clearance</h5>
                    <p>IRERC panel reviews ethical compliance and issues clearance.</p>
                </div>

                <div class="journey-step-item">
                    <div class="step-num">05</div>
                    <div class="step-icon-circle"><i class="bi bi-bank"></i></div>
                    <h5>Budget Ratification</h5>
                    <p>Dean (&lt;500k) or RCSC &amp; VP (&ge;500k) formally ratify grant funding.</p>
                </div>

                <div class="journey-step-item">
                    <div class="step-num">06</div>
                    <div class="step-icon-circle"><i class="bi bi-award"></i></div>
                    <h5>Tranche Execution</h5>
                    <p>Bilateral contract signed, funds disbursed, and completion certificate granted.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- INSTITUTIONAL ROLES MATRIX --}}
    <section id="research" class="roles-matrix-section py-5">
        <div class="container">
            <div class="section-title-wrap text-center mb-5">
                <span class="section-eyebrow"><i class="bi bi-people-fill me-1"></i> Role-Based Access Control</span>
                <h2 class="section-heading-lg">Tailored Dashboards for 11 Stakeholder Roles</h2>
                <p class="section-sub-lead mx-auto">
                    Every participant operates within a dedicated workspace customized to their specific authority.
                </p>
            </div>

            <div class="row g-3">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-person-workspace text-success"></i>
                        <h6>Principal Investigator</h6>
                        <span>Proposal authoring &amp; milestones</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-people text-info"></i>
                        <h6>Research Team Member</h6>
                        <span>Co-investigator collaboration</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-building-check text-primary"></i>
                        <h6>Department Head</h6>
                        <span>First-line proposal screening</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-diagram-3-fill text-warning"></i>
                        <h6>Research Coordinator</h6>
                        <span>Reviewer assignment &amp; hub</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-eye-slash-fill text-danger"></i>
                        <h6>Blind Peer Reviewer</h6>
                        <span>100-point rubric evaluation</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-shield-check text-success"></i>
                        <h6>IRERC Ethics Panel</h6>
                        <span>Ethical clearance protocols</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-bank text-info"></i>
                        <h6>College Dean</h6>
                        <span>Tier-1 budget ratification</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-award-fill text-warning"></i>
                        <h6>Vice President (VP)</h6>
                        <span>Institutional contracts &amp; RCSC</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-cash-coin text-success"></i>
                        <h6>Finance Officer</h6>
                        <span>Tranche vouchers &amp; ledger</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-bank2 text-primary"></i>
                        <h6>RCSC Chair</h6>
                        <span>High-tier council ratification</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card">
                        <i class="bi bi-gear-fill text-secondary"></i>
                        <h6>System Administrator</h6>
                        <span>Audit logs, RBAC &amp; backups</span>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="role-badge-card highlight-card">
                        <i class="bi bi-box-arrow-in-right text-success"></i>
                        <h6>Demo Credentials</h6>
                        <span>All password: <code>UNI@Demo1</code></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- INSTITUTIONAL CTA CALLOUT --}}
    <section id="contact" class="pro-cta-section py-5">
        <div class="container">
            <div class="pro-cta-banner p-4 p-md-5 rounded-4 text-center">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-3">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Ready to Advance Research?
                </span>
                <h2 class="display-6 fw-bold text-white mb-3">Accelerate Your Academic Research Today</h2>
                <p class="text-white-50 max-w-xl mx-auto mb-4" style="max-width: 600px;">
                    Experience seamless proposal submissions, transparent peer reviews, and automated financial disbursements on the RPMS platform.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg fw-bold px-4">
                            <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg fw-bold px-4 text-dark">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Log In to System
                        </a>
                    @endauth
                    <a href="{{ route('research.catalog') }}" class="btn btn-outline-light btn-lg fw-bold px-4">
                        <i class="bi bi-journal-text me-1"></i> View Research Catalog
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
/* =====================================================================
   PRO LANDING PAGE STYLES (Ultra-Modern Academic Theme)
   ===================================================================== */
.home-pro-wrapper {
    position: relative;
    overflow-x: hidden;
    background: radial-gradient(circle at 50% 10%, #0d281f 0%, #071712 50%, #040b08 100%);
    color: #e5e7eb;
    min-height: 100vh;
}

/* Ambient Radial Glows */
.ambient-glow {
    position: absolute;
    border-radius: 50%;
    filter: blur(140px);
    pointer-events: none;
    z-index: 0;
    opacity: 0.45;
}
.glow-1 {
    width: 600px;
    height: 600px;
    top: -100px;
    left: -150px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, transparent 70%);
}
.glow-2 {
    width: 500px;
    height: 500px;
    top: 20%;
    right: -100px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.25) 0%, transparent 70%);
}
.glow-3 {
    width: 700px;
    height: 700px;
    bottom: 10%;
    left: 20%;
    background: radial-gradient(circle, rgba(20, 184, 166, 0.2) 0%, transparent 70%);
}

/* Hero Section */
.hero-pro-section {
    position: relative;
    z-index: 1;
    padding: 4.5rem 0 3.5rem;
}
.hero-container {
    max-width: 1320px;
}
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    border-radius: 999px;
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.28);
    font-size: 0.82rem;
    font-weight: 600;
    color: #34d399;
}
.live-pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 10px #10b981, 0 0 20px #10b981;
    animation: pulseGlow 1.8s infinite;
}
@keyframes pulseGlow {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.4); opacity: 0.6; }
}
.hero-title {
    font-size: clamp(2.4rem, 4.2vw, 3.8rem);
    font-weight: 800;
    line-height: 1.12;
    letter-spacing: -0.035em;
    color: #ffffff;
    margin-bottom: 1.25rem;
}
.gradient-text {
    background: linear-gradient(135deg, #34d399 0%, #f4c95d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.hero-lead {
    font-size: 1.12rem;
    line-height: 1.65;
    color: #94a3b8;
    max-width: 540px;
    margin-bottom: 2rem;
}
.hero-cta-group {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}
.btn-pro-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff !important;
    font-weight: 700;
    padding: 0.8rem 1.6rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-pro-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.45);
    background: linear-gradient(135deg, #14b8a6 0%, #059669 100%);
}
.btn-pro-glass {
    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: #ffffff !important;
    font-weight: 600;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    transition: all 0.25s ease;
}
.btn-pro-glass:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.28);
    transform: translateY(-2px);
}

/* Micro Metrics Strip */
.hero-metrics-strip {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.metric-pill {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 10px 16px;
    flex: 1 1 140px;
}
.metric-pill-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.02em;
}
.metric-pill-label {
    font-size: 0.72rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-top: 2px;
}

/* Cockpit Interactive Card */
.cockpit-card {
    background: rgba(13, 27, 22, 0.88);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(52, 211, 153, 0.22);
    border-radius: 20px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55), 0 0 35px rgba(16, 185, 129, 0.12);
    overflow: hidden;
}
.cockpit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    background: rgba(255, 255, 255, 0.03);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.cockpit-dots {
    display: flex;
    gap: 6px;
}
.dot-c {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.dot-red { background: #ef4444; }
.dot-amber { background: #f59e0b; }
.dot-green { background: #10b981; }
.cockpit-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.cockpit-body {
    padding: 20px;
}
.stage-pills-nav {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.stage-pill-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #94a3b8;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.stage-pill-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}
.stage-pill-active {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
}
.active-stage-display {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.stage-avatar-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}
.sub-metric-box {
    background: rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 8px 12px;
}
.sub-label {
    font-size: 0.68rem;
    color: #94a3b8;
    text-transform: uppercase;
}
.sub-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: #e2e8f0;
}

/* Bento Grid */
.bento-section {
    position: relative;
    z-index: 1;
}
.section-eyebrow {
    display: inline-flex;
    align-items: center;
    font-size: 0.82rem;
    font-weight: 700;
    color: #34d399;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.6rem;
}
.section-heading-lg {
    font-size: clamp(2rem, 3.2vw, 2.75rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #ffffff;
}
.section-sub-lead {
    font-size: 1.05rem;
    color: #94a3b8;
    max-width: 640px;
}
.bento-card {
    background: rgba(13, 27, 22, 0.65);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 24px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.bento-card:hover {
    transform: translateY(-4px);
    border-color: rgba(52, 211, 153, 0.35);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4), 0 0 20px rgba(16, 185, 129, 0.1);
}
.bento-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    margin-bottom: 16px;
}
.bento-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 10px;
}
.bento-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #94a3b8;
    margin-bottom: 14px;
}
.bento-checklist {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 0.82rem;
    color: #cbd5e1;
    display: grid;
    gap: 6px;
}

/* Colors */
.bg-indigo-subtle { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
.bg-emerald-subtle { background: rgba(16, 185, 129, 0.15); color: #34d399; }
.bg-amber-subtle { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
.bg-teal-subtle { background: rgba(20, 184, 166, 0.15); color: #2dd4bf; }
.bg-cyan-subtle { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
.bg-purple-subtle { background: rgba(168, 85, 247, 0.15); color: #c084fc; }

/* Journey Stepper */
.journey-stepper-wrap {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 16px;
}
@media (max-width: 991px) {
    .journey-stepper-wrap { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 576px) {
    .journey-stepper-wrap { grid-template-columns: 1fr; }
}
.journey-step-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 18px 14px;
    text-align: center;
    position: relative;
    transition: all 0.25s ease;
}
.journey-step-item:hover {
    border-color: #34d399;
    transform: translateY(-3px);
}
.step-num {
    font-size: 0.72rem;
    font-weight: 800;
    color: #10b981;
    margin-bottom: 8px;
    letter-spacing: 0.05em;
}
.step-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    margin: 0 auto 10px;
    background: rgba(16, 185, 129, 0.12);
    color: #34d399;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.journey-step-item h5 {
    font-size: 0.92rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 6px;
}
.journey-step-item p {
    font-size: 0.76rem;
    line-height: 1.45;
    color: #94a3b8;
    margin: 0;
}

/* Roles Matrix */
.role-badge-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: all 0.2s ease;
}
.role-badge-card:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.18);
    transform: translateY(-2px);
}
.role-badge-card i {
    font-size: 1.3rem;
    margin-bottom: 4px;
}
.role-badge-card h6 {
    font-size: 0.88rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}
.role-badge-card span {
    font-size: 0.74rem;
    color: #94a3b8;
}
.highlight-card {
    background: rgba(16, 185, 129, 0.1) !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}

/* Callout Banner */
.pro-cta-banner {
    background: linear-gradient(135deg, #093424 0%, #062217 50%, #03130d 100%);
    border: 1px solid rgba(52, 211, 153, 0.3);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
}
</style>
@endsection

@section('scripts')
<script>
function homePipelineSimulator() {
    return {
        activeStage: 0,
        stages: [
            {
                shortTitle: 'Proposal',
                title: '1. Proposal Authoring & Budgeting',
                actor: 'Principal Investigator (PI)',
                status: 'Draft / Submitted',
                statusBadgeClass: 'bg-secondary text-white',
                icon: 'bi-file-earmark-plus',
                badgeColor: 'bg-secondary-subtle text-secondary',
                description: 'The PI defines research objectives, assembles co-investigators, and generates an itemized budget across personnel, lab equipment, and fieldwork.',
                tier: 'Automated Tier Assessment',
                financial: 'Budget Model Configured',
                progressPercent: 15
            },
            {
                shortTitle: 'Screening',
                title: '2. Department Head Screening',
                actor: 'Department Head (DH)',
                status: 'DH_Screened',
                statusBadgeClass: 'bg-primary text-white',
                icon: 'bi-funnel',
                badgeColor: 'bg-primary-subtle text-primary',
                description: 'Verifies departmental alignment, faculty workload, lab resource feasibility, and institutional priority before routing forward.',
                tier: 'College Department Level',
                financial: 'Screening Passed',
                progressPercent: 30
            },
            {
                shortTitle: 'Peer Review',
                title: '3. Double-Blind Peer Review',
                actor: '2 Independent Reviewers',
                status: 'UnderReview',
                statusBadgeClass: 'bg-warning text-dark',
                icon: 'bi-eye-slash',
                badgeColor: 'bg-warning-subtle text-warning',
                description: 'Reviewers evaluate scientific rigor, methodology, and significance on a 100-point rubric with investigator identities masked.',
                tier: 'Research Coordinator Hub',
                financial: 'Scientific Scoring &gt; 70%',
                progressPercent: 50
            },
            {
                shortTitle: 'Ethics',
                title: '4. IRERC Ethics Clearance',
                actor: 'IRERC Review Panel',
                status: 'Ethics Cleared',
                statusBadgeClass: 'bg-info text-dark',
                icon: 'bi-shield-check',
                badgeColor: 'bg-info-subtle text-info',
                description: 'Statutory compliance check for human subjects, animal welfare, environmental safety, and data sovereignty protocols.',
                tier: 'Institutional Ethics Board',
                financial: 'Clearance Certificate Issued',
                progressPercent: 65
            },
            {
                shortTitle: 'Ratification',
                title: '5. Dual-Threshold Ratification',
                actor: 'Dean (<500k) or RCSC & VP (≥500k)',
                status: 'Approved',
                statusBadgeClass: 'bg-success text-white',
                icon: 'bi-bank',
                badgeColor: 'bg-success-subtle text-success',
                description: 'Executive committee ratifies approved grant allocations and establishes binding grant agreement milestones.',
                tier: 'Dean vs RCSC/VP Dual Threshold',
                financial: 'Formal Ratification Decree',
                progressPercent: 80
            },
            {
                shortTitle: 'Disbursement',
                title: '6. Active Contract & Disbursement',
                actor: 'Finance Office & PI',
                status: 'Active (Tranches 1-3)',
                statusBadgeClass: 'bg-success text-white',
                icon: 'bi-cash-coin',
                badgeColor: 'bg-success-subtle text-success',
                description: 'Tranche 1 (30% advance) released upon bilateral signing. Tranche 2 (40%) and Tranche 3 (30%) unlocked upon milestone approvals.',
                tier: 'Finance Disbursement Ledger',
                financial: 'Tranche 1 (30%) Released',
                progressPercent: 100
            }
        ]
    };
}
</script>
@endsection
