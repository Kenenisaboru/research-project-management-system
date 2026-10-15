@extends('layouts.app')

@section('content')
<section class="home-landing">
    <div class="hero-shell">
        <div class="hero-copy">
            <span class="eyebrow"><i class="bi bi-stars me-2"></i>Research, simplified</span>
            <h1>From proposal to impact, every step stays connected.</h1>
            <p>
                RPMS helps universities manage research projects, funding, reviews, compliance,
                collaboration, and reporting from one secure workspace.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-4">Open dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-success btn-lg px-4">Login to RPMS</a>
                @endauth
                <a href="#features" class="btn btn-outline-secondary btn-lg px-4">Explore features</a>
            </div>

            <div class="mini-stat-grid">
                <div class="mini-stat">
                    <strong>1 platform</strong>
                    <span>for planning and reporting</span>
                </div>
                <div class="mini-stat">
                    <strong>4 roles</strong>
                    <span>PI, coordinator, reviewer, admin</span>
                </div>
                <div class="mini-stat">
                    <strong>24/7</strong>
                    <span>access to project visibility</span>
                </div>
            </div>
        </div>

        <div class="hero-panel">
            <div class="panel-card">
                <div class="panel-head">
                    <span class="dot dot-green"></span>
                    <span class="dot dot-gold"></span>
                    <span class="dot dot-dark"></span>
                </div>
                <div class="panel-body">
                    <div class="metric-row">
                        <div>
                            <small>Active projects</small>
                            <h3>184</h3>
                        </div>
                        <span class="badge badge-success">+12%</span>
                    </div>

                    <div class="progress-block">
                        <div class="progress-meta">
                            <span>Funding pipeline</span>
                            <strong>76%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 76%"></div>
                        </div>
                    </div>

                    <ul class="feature-list">
                        <li><i class="bi bi-check2-circle"></i> Proposal tracking</li>
                        <li><i class="bi bi-check2-circle"></i> Ethics review workflow</li>
                        <li><i class="bi bi-check2-circle"></i> Progress and finance reporting</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="content-section">
    <div class="section-heading">
        <span class="eyebrow subtle"><i class="bi bi-diagram-3 me-2"></i>Core features</span>
        <h2>Built for research operations</h2>
    </div>

    <div class="feature-grid">
        <article class="feature-card">
            <div class="icon-wrap"><i class="bi bi-folder2-open"></i></div>
            <h3>Project lifecycle</h3>
            <p>Track proposals, amendments, progress, approvals, and completion from one timeline.</p>
        </article>
        <article class="feature-card">
            <div class="icon-wrap"><i class="bi bi-clipboard-check"></i></div>
            <h3>Review management</h3>
            <p>Coordinate reviewer assignments, scoring, and ethics governance without the paperwork chaos.</p>
        </article>
        <article class="feature-card">
            <div class="icon-wrap"><i class="bi bi-cash-stack"></i></div>
            <h3>Finance visibility</h3>
            <p>Monitor disbursements, requests, and reporting with better accountability and control.</p>
        </article>
        <article class="feature-card">
            <div class="icon-wrap"><i class="bi bi-people"></i></div>
            <h3>Team collaboration</h3>
            <p>Give each role access to only the information and actions they need for smooth execution.</p>
        </article>
    </div>
</section>

<section id="research" class="content-section alt">
    <div class="section-heading">
        <span class="eyebrow subtle"><i class="bi bi-globe2 me-2"></i>Research ecosystem</span>
        <h2>Operational clarity for academic teams</h2>
    </div>

    <div class="research-grid">
        <div class="research-card">
            <h3>Portfolio overview</h3>
            <p>See activity, deadlines, and key milestones for every research project in a single dashboard.</p>
        </div>
        <div class="research-card">
            <h3>Governance and compliance</h3>
            <p>Support IRERC, dean approvals, and review-panel decisions with consistent workflow controls.</p>
        </div>
        <div class="research-card">
            <h3>Institutional reporting</h3>
            <p>Generate internal reports and evidence-based summaries to keep leadership informed.</p>
        </div>
    </div>
</section>

<section id="contact" class="content-section contact-wrap">
    <div class="contact-card">
        <div>
            <span class="eyebrow subtle"><i class="bi bi-envelope me-2"></i>Need support?</span>
            <h2>Keep your research operations moving.</h2>
        </div>
        <a href="mailto:research@institution.edu" class="btn btn-dark btn-lg">Contact the team</a>
    </div>
</section>
@endsection

@section('styles')
<style>
    .home-landing {
        padding: 2rem 0 0;
    }
    .hero-shell {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 2rem;
        align-items: center;
        width: min(1200px, calc(100% - 2rem));
        margin: 0 auto;
        padding: 2rem 0 1rem;
    }
    .hero-copy {
        padding: 1rem 0;
    }
    .eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.9rem;
        border-radius: 999px;
        background: rgba(15, 62, 46, 0.08);
        color: var(--UNI-primary);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .eyebrow.subtle {
        background: rgba(15, 62, 46, 0.04);
    }
    .hero-copy h1 {
        margin-top: 1rem;
        font-size: clamp(2.5rem, 4vw, 4.3rem);
        line-height: 1.05;
        letter-spacing: -0.05em;
        color: #102d28;
    }
    .hero-copy p {
        max-width: 35rem;
        margin-top: 1rem;
        font-size: 1.08rem;
        color: #475569;
    }
    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.9rem;
        margin-top: 1.5rem;
    }
    .mini-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 2rem;
        max-width: 42rem;
    }
    .mini-stat {
        padding: 1rem 1rem 0.85rem;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        background: rgba(255,255,255,0.8);
        box-shadow: 0 10px 25px rgba(15, 62, 46, 0.04);
    }
    .mini-stat strong {
        display: block;
        font-size: 1.05rem;
        color: #102d28;
    }
    .mini-stat span {
        display: block;
        margin-top: 0.25rem;
        color: #64748b;
        font-size: 0.78rem;
    }
    .hero-panel {
        display: flex;
        justify-content: center;
    }
    .panel-card {
        width: min(100%, 500px);
        background: linear-gradient(180deg, #ffffff 0%, #f8fafb 100%);
        border: 1px solid #e5e7eb;
        border-radius: 1.5rem;
        box-shadow: 0 24px 60px rgba(15, 62, 46, 0.1);
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        gap: 0.5rem;
        padding: 1rem 1.1rem;
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
    }
    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-green { background: #22c55e; }
    .dot-gold { background: #f4c95d; }
    .dot-dark { background: #0f172a; }
    .panel-body {
        padding: 1.25rem;
    }
    .metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .metric-row small {
        display: block;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .metric-row h3 {
        margin: 0.2rem 0 0;
        font-size: 2.2rem;
        color: #102d28;
    }
    .badge-success {
        background: rgba(34, 197, 94, 0.12);
        color: #166534;
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 999px;
        padding: 0.4rem 0.7rem;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .progress-block {
        margin-top: 1rem;
    }
    .progress-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        color: #334155;
        font-size: 0.9rem;
    }
    .progress {
        height: 0.75rem;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-bar {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #0f3e2e 0%, #2e8b57 100%);
    }
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 1.4rem 0 0;
        display: grid;
        gap: 0.75rem;
        color: #334155;
    }
    .feature-list li {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 0.8rem;
        padding: 0.72rem 0.8rem;
    }
    .feature-list i {
        color: #0f3e2e;
    }
    .content-section {
        width: min(1200px, calc(100% - 2rem));
        margin: 4.5rem auto 0;
    }
    .section-heading {
        margin-bottom: 1.5rem;
    }
    .section-heading h2 {
        margin-top: 0.75rem;
        font-size: clamp(2rem, 2.8vw, 2.7rem);
        line-height: 1.1;
        letter-spacing: -0.04em;
        color: #102d28;
    }
    .feature-grid, .research-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.25rem;
    }
    .feature-card, .research-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1.2rem;
        box-shadow: 0 14px 28px rgba(15, 62, 46, 0.04);
        padding: 1.4rem;
    }
    .feature-card h3, .research-card h3 {
        margin: 1rem 0 0.6rem;
        color: #102d28;
        font-size: 1.2rem;
    }
    .feature-card p, .research-card p {
        color: #475569;
        margin: 0;
    }
    .icon-wrap {
        display: inline-flex;
        width: 52px;
        height: 52px;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: rgba(15, 62, 46, 0.08);
        color: var(--UNI-primary);
        font-size: 1.4rem;
    }
    .content-section.alt {
        padding: 1rem 0 0;
    }
    .research-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .contact-wrap {
        padding-bottom: 4rem;
    }
    .contact-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem 1.6rem;
        border-radius: 1.4rem;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f8fafb 0%, #eefcf4 100%);
    }
    .contact-card h2 {
        margin: 0.4rem 0 0;
        color: #102d28;
        font-size: clamp(1.6rem, 2.1vw, 2.2rem);
    }
    @media (max-width: 991.98px) {
        .hero-shell,
        .feature-grid,
        .research-grid {
            grid-template-columns: 1fr;
        }
        .contact-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    @media (max-width: 575.98px) {
        .mini-stat-grid {
            grid-template-columns: 1fr;
        }
        .hero-copy h1 {
            font-size: 2.5rem;
        }
    }
</style>
@endsection
