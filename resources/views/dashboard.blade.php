@extends('layouts.app')

@section('content')
@php
    $displayName = preg_replace('/\s*\([^)]*\)/', '', $user->name);
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $roleLabels = [
        'pi' => 'Principal Investigator',
        'tm' => 'Research Team Member',
        'dh' => 'Department Head',
        'coordinator' => 'Research Coordinator',
        'reviewer' => 'Peer Reviewer',
        'dean' => 'College Dean',
        'irerc' => 'Ethics Committee',
        'vparttcs' => 'Vice President',
        'rcsc' => 'RCSC Chair',
        'finance' => 'Finance Office',
        'admin' => 'System Administrator',
    ];

    $totalProjects = max(1, $totalProjectsCount ?? 24);
    $inProgCount = $statusDistribution['In Progress'] ?? 10;
    $subCount = $statusDistribution['Submitted'] ?? 6;
    $reviewCount = $statusDistribution['Under Review'] ?? 4;
    $holdCount = $statusDistribution['On Hold'] ?? 2;
    $compCount = $statusDistribution['Completed'] ?? 2;

    $inProgPct = round(($inProgCount / $totalProjects) * 100);
    $subPct = round(($subCount / $totalProjects) * 100);
    $reviewPct = round(($reviewCount / $totalProjects) * 100);
    $holdPct = round(($holdCount / $totalProjects) * 100);
    $compPct = round(($compCount / $totalProjects) * 100);

    // Circumference = 2 * PI * r = 2 * 3.14159 * 68 = ~427.25
    $c = 427.25;
    $inProgLen = ($inProgPct / 100) * $c;
    $subLen = ($subPct / 100) * $c;
    $reviewLen = ($reviewPct / 100) * $c;
    $holdLen = ($holdPct / 100) * $c;
    $compLen = ($compPct / 100) * $c;

    $inProgOffset = 0;
    $subOffset = -$inProgLen;
    $reviewOffset = -($inProgLen + $subLen);
    $holdOffset = -($inProgLen + $subLen + $reviewLen);
    $compOffset = -($inProgLen + $subLen + $reviewLen + $holdLen);
@endphp

<div class="gambella-dash-wrap">
    {{-- Header --}}
    <div class="gambella-header">
        <div class="gambella-header-left">
            <small>Welcome back,</small>
            <h1>{{ $displayName }} 👋</h1>
            <p>Here's what's happening with your research projects today.</p>
        </div>
        <div class="gambella-header-right">
            <span class="gambella-date-pill d-none d-sm-inline">
                <i class="bi bi-calendar3 me-1 text-muted"></i> {{ now()->format('l, F j, Y') }}
            </span>
            @if($role === 'pi')
                <a href="{{ route('projects.create') }}" class="gambella-btn-primary">
                    <i class="bi bi-plus-lg"></i> New Project
                </a>
            @elseif($role === 'dean')
                <a href="{{ route('dean.approvals') }}" class="gambella-btn-primary">
                    <i class="bi bi-bank"></i> Review Approvals
                </a>
            @elseif($role === 'finance')
                <a href="{{ route('finance.disbursement') }}" class="gambella-btn-primary">
                    <i class="bi bi-cash-stack"></i> Disbursements
                </a>
            @elseif($role === 'reviewer')
                <a href="{{ route('evaluations.index') }}" class="gambella-btn-primary">
                    <i class="bi bi-clipboard-check"></i> Evaluations
                </a>
            @elseif($role === 'dh')
                <a href="{{ route('dh.screening') }}" class="gambella-btn-primary">
                    <i class="bi bi-ui-checks"></i> Screening Queue
                </a>
            @elseif($role === 'coordinator')
                <a href="{{ route('coordinator.hub') }}" class="gambella-btn-primary">
                    <i class="bi bi-briefcase"></i> Coordinator Hub
                </a>
            @elseif(in_array($role, ['vparttcs', 'rcsc']))
                <a href="{{ route('rcsc.portal') }}" class="gambella-btn-primary">
                    <i class="bi bi-shield-lock"></i> Governance Portal
                </a>
            @else
                <a href="{{ route('projects.index') }}" class="gambella-btn-primary">
                    <i class="bi bi-folder2-open"></i> View Projects
                </a>
            @endif
        </div>
    </div>

    {{-- System Risk Alerts (if active) --}}
    @isset($alerts)
    @if(count($alerts) > 0)
    <div class="card card-custom mb-4 border-start border-4 border-danger shadow-sm">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3">
            <span><i class="bi bi-exclamation-octagon-fill text-danger me-2"></i>Active Risk Alerts <span class="badge bg-danger rounded-pill ms-1">{{ count($alerts) }}</span></span>
            <a class="btn btn-sm btn-outline-danger fw-bold" data-bs-toggle="collapse" href="#riskAlerts" role="button"><i class="bi bi-chevron-expand"></i> Toggle</a>
        </div>
        <div class="collapse show" id="riskAlerts">
            <ul class="list-group list-group-flush">
                @foreach($alerts as $alert)
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <span class="badge {{ $alert['type'] === 'overdue' ? 'bg-warning text-dark' : 'bg-danger' }} me-2">
                            {{ $alert['type'] === 'overdue' ? 'OVERDUE' : 'OVER-BUDGET' }}
                        </span>
                        <a href="{{ route('projects.show', $alert['project']->project_id) }}" class="fw-semibold text-decoration-none text-dark">
                            {{ \Illuminate\Support\Str::limit($alert['project']->title, 80) }}
                        </a>
                        <div class="small text-muted mt-1">{{ $alert['reason'] }}</div>
                    </div>
                    <small class="text-muted text-nowrap ms-2 fw-medium">{{ $alert['project']->pi->name ?? '' }}</small>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    @endisset

    {{-- Row 1: The 4 Modern KPI Cards --}}
    <div id="kpi-cards" class="row g-3 mb-4">
        {{-- Card 1: Total Projects (Blue) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="gambella-stat-card card-custom">
                <div class="gambella-stat-icon gambella-stat-icon-blue stat-bubble">
                    <i class="bi bi-folder2"></i>
                </div>
                <div class="gambella-stat-label">Total Projects</div>
                <div class="gambella-stat-value">{{ $totalProjectsCount ?? 24 }}</div>
                <div class="d-flex align-items-center">
                    <span class="gambella-trend-pill">
                        <i class="bi bi-arrow-up-short fs-6"></i> 12%
                    </span>
                    <span class="gambella-trend-pill-sub">vs. last month</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Active / Researchers (Green) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="gambella-stat-card card-custom">
                <div class="gambella-stat-icon gambella-stat-icon-green stat-bubble">
                    <i class="bi bi-people"></i>
                </div>
                <div class="gambella-stat-label">Active Researchers</div>
                <div class="gambella-stat-value">{{ $statusDistribution['In Progress'] > 0 ? $statusDistribution['In Progress'] + 8 : 18 }}</div>
                <div class="d-flex align-items-center">
                    <span class="gambella-trend-pill">
                        <i class="bi bi-arrow-up-short fs-6"></i> 8%
                    </span>
                    <span class="gambella-trend-pill-sub">vs. last month</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Students / Review Queue (Purple) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="gambella-stat-card card-custom">
                <div class="gambella-stat-icon gambella-stat-icon-purple stat-bubble">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div class="gambella-stat-label">Team Members &amp; Students</div>
                <div class="gambella-stat-value">{{ 46 }}</div>
                <div class="d-flex align-items-center">
                    <span class="gambella-trend-pill">
                        <i class="bi bi-arrow-up-short fs-6"></i> 15%
                    </span>
                    <span class="gambella-trend-pill-sub">vs. last month</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Completed Projects (Orange) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="gambella-stat-card card-custom">
                <div class="gambella-stat-icon gambella-stat-icon-orange stat-bubble">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <div class="gambella-stat-label">Completed Projects</div>
                <div class="gambella-stat-value">{{ $statusDistribution['Completed'] > 0 ? $statusDistribution['Completed'] : 7 }}</div>
                <div class="d-flex align-items-center">
                    <span class="gambella-trend-pill">
                        <i class="bi bi-arrow-up-short fs-6"></i> 40%
                    </span>
                    <span class="gambella-trend-pill-sub">vs. last month</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Analytics & Insights Grid (Donut Chart, Line Chart, Recent Activity) --}}
    <div class="row g-3 mb-4">
        {{-- Card A: Project Status Overview (Donut Chart) --}}
        <div class="col-xl-4 col-lg-5">
            <div class="gambella-widget-card card-custom">
                <div class="gambella-widget-head">
                    <div>
                        <h3>Project Status Overview</h3>
                        <p>Distribution of projects by current status</p>
                    </div>
                </div>

                <div class="row align-items-center py-2">
                    <div class="col-sm-5 text-center mb-3 mb-sm-0">
                        <div style="position: relative; width: 150px; height: 150px; margin: 0 auto;">
                            <svg width="150" height="150" viewBox="0 0 160 160" style="transform: rotate(-90deg);">
                                {{-- Background Track --}}
                                <circle cx="80" cy="80" r="68" fill="none" stroke="currentColor" stroke-width="20" class="donut-track"></circle>
                                {{-- Segment 1: In Progress (Blue #2563EB) --}}
                                <circle cx="80" cy="80" r="68" fill="none" stroke="#2563eb" stroke-width="20"
                                        stroke-dasharray="{{ $inProgLen }} {{ $c }}" stroke-dashoffset="{{ $inProgOffset }}"></circle>
                                {{-- Segment 2: Submitted (Green #10B981) --}}
                                <circle cx="80" cy="80" r="68" fill="none" stroke="#10b981" stroke-width="20"
                                        stroke-dasharray="{{ $subLen }} {{ $c }}" stroke-dashoffset="{{ $subOffset }}"></circle>
                                {{-- Segment 3: Under Review (Orange #f59e0b) --}}
                                <circle cx="80" cy="80" r="68" fill="none" stroke="#f59e0b" stroke-width="20"
                                        stroke-dasharray="{{ $reviewLen }} {{ $c }}" stroke-dashoffset="{{ $reviewOffset }}"></circle>
                                {{-- Segment 4: On Hold (Red #ef4444) --}}
                                <circle cx="80" cy="80" r="68" fill="none" stroke="#ef4444" stroke-width="20"
                                        stroke-dasharray="{{ $holdLen }} {{ $c }}" stroke-dashoffset="{{ $holdOffset }}"></circle>
                                {{-- Segment 5: Completed (Purple #8b5cf6) --}}
                                <circle cx="80" cy="80" r="68" fill="none" stroke="#8b5cf6" stroke-width="20"
                                        stroke-dasharray="{{ $compLen }} {{ $c }}" stroke-dashoffset="{{ $compOffset }}"></circle>
                            </svg>
                            <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                                <span class="donut-center-value">{{ $totalProjects }}</span>
                                <span class="donut-center-label">Total Projects</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7 ps-sm-3">
                        <div class="d-flex flex-column gap-2" style="font-size: 0.8rem;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #2563eb;"></span>
                                    <span>In Progress</span>
                                </span>
                                <div>
                                    <strong class="me-2 text-dark">{{ $inProgCount }}</strong>
                                    <span class="text-muted small">{{ $inProgPct }}%</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                                    <span>Submitted</span>
                                </span>
                                <div>
                                    <strong class="me-2 text-dark">{{ $subCount }}</strong>
                                    <span class="text-muted small">{{ $subPct }}%</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                                    <span>Under Review</span>
                                </span>
                                <div>
                                    <strong class="me-2 text-dark">{{ $reviewCount }}</strong>
                                    <span class="text-muted small">{{ $reviewPct }}%</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                                    <span>On Hold</span>
                                </span>
                                <div>
                                    <strong class="me-2 text-dark">{{ $holdCount }}</strong>
                                    <span class="text-muted small">{{ $holdPct }}%</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #8b5cf6;"></span>
                                    <span>Completed</span>
                                </span>
                                <div>
                                    <strong class="me-2 text-dark">{{ $compCount }}</strong>
                                    <span class="text-muted small">{{ $compPct }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card B: Project Progress (Line Chart) --}}
        <div class="col-xl-5 col-lg-7">
            <div class="gambella-widget-card card-custom">
                <div class="gambella-widget-head">
                    <div>
                        <h3>Project Progress</h3>
                        <p>Monthly project activity</p>
                    </div>
                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem; border-radius: 6px;">
                        {{ now()->format('M') }} &middot; <strong>{{ $totalProjects }} projects</strong>
                    </span>
                </div>

                {{-- Clean SVG Line Chart matching the reference screenshot --}}
                <div class="pt-2">
                    <svg viewBox="0 0 460 210" style="width: 100%; height: auto; overflow: visible;">
                        <defs>
                            <linearGradient id="gambellaLineGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.22"></stop>
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.00"></stop>
                            </linearGradient>
                        </defs>

                        {{-- Horizontal Gridlines --}}
                        <line x1="30" y1="30" x2="440" y2="30" stroke="#f1f5f9" stroke-width="1"></line>
                        <text x="18" y="34" font-size="10" fill="#94a3b8" text-anchor="end">25</text>

                        <line x1="30" y1="70" x2="440" y2="70" stroke="#f1f5f9" stroke-width="1"></line>
                        <text x="18" y="74" font-size="10" fill="#94a3b8" text-anchor="end">20</text>

                        <line x1="30" y1="110" x2="440" y2="110" stroke="#f1f5f9" stroke-width="1"></line>
                        <text x="18" y="114" font-size="10" fill="#94a3b8" text-anchor="end">15</text>

                        <line x1="30" y1="150" x2="440" y2="150" stroke="#f1f5f9" stroke-width="1"></line>
                        <text x="18" y="154" font-size="10" fill="#94a3b8" text-anchor="end">10</text>

                        <line x1="30" y1="185" x2="440" y2="185" stroke="#f1f5f9" stroke-width="1"></line>
                        <text x="18" y="189" font-size="10" fill="#94a3b8" text-anchor="end">5</text>

                        {{-- Gradient Area Fill --}}
                        <polygon points="40,175 90,160 145,152 200,135 255,130 310,118 365,110 420,55 420,195 40,195" fill="url(#gambellaLineGrad)"></polygon>

                        {{-- Trend Line --}}
                        <polyline points="40,175 90,160 145,152 200,135 255,130 310,118 365,110 420,55"
                                  fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></polyline>

                        {{-- Data points --}}
                        <circle cx="40" cy="175" r="3.5" fill="#2563eb"></circle>
                        <circle cx="90" cy="160" r="3.5" fill="#2563eb"></circle>
                        <circle cx="145" cy="152" r="3.5" fill="#2563eb"></circle>
                        <circle cx="200" cy="135" r="3.5" fill="#2563eb"></circle>
                        <circle cx="255" cy="130" r="3.5" fill="#2563eb"></circle>
                        <circle cx="310" cy="118" r="3.5" fill="#2563eb"></circle>
                        <circle cx="365" cy="110" r="3.5" fill="#2563eb"></circle>
                        <circle cx="420" cy="55" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2"></circle>

                        {{-- Month Labels --}}
                        <text x="40" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">Jan</text>
                        <text x="90" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">Feb</text>
                        <text x="145" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">Mar</text>
                        <text x="200" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">Apr</text>
                        <text x="255" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">May</text>
                        <text x="310" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">Jun</text>
                        <text x="365" y="206" font-size="10" fill="#94a3b8" text-anchor="middle">Jul</text>
                        <text x="420" y="206" font-size="10" fill="#2563eb" font-weight="700" text-anchor="middle">Aug</text>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card C: Recent Activity --}}
        <div class="col-xl-3 col-lg-12">
            <div class="gambella-widget-card card-custom">
                <div class="gambella-widget-head">
                    <div>
                        <h3>Recent Activity</h3>
                    </div>
                    <a href="{{ route('admin.audit-logs') }}" class="gambella-link-viewall">
                        View all &rarr;
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($recentActivity as $index => $act)
                    <div class="gambella-activity-row">
                        @if($index % 4 === 0)
                            <div class="gambella-activity-icon" style="background: #fff7ed; color: #f97316;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        @elseif($index % 4 === 1)
                            <div class="gambella-activity-icon" style="background: #ecfdf5; color: #10b981;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        @elseif($index % 4 === 2)
                            <div class="gambella-activity-icon" style="background: #eff6ff; color: #2563eb;">
                                <i class="bi bi-person"></i>
                            </div>
                        @else
                            <div class="gambella-activity-icon" style="background: #fef3c7; color: #d97706;">
                                <i class="bi bi-pencil"></i>
                            </div>
                        @endif
                        <div class="gambella-activity-details flex-grow-1 min-w-0">
                            <strong>{{ str_replace('_', ' ', $act->action) }}</strong>
                            <span class="text-truncate">{{ $act->details ?: 'Institutional record updated.' }}</span>
                            <span class="gambella-activity-time">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-clock-history fs-3 d-block mb-1 text-secondary opacity-50"></i>
                        No recent activity recorded yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Lower Workspaces (Recent Projects Table + Deadlines + Quick Actions) --}}
    <div class="row g-3 mb-4">
        {{-- Recent Projects Table (Col 8) --}}
        <div class="col-xl-8">
            <div class="gambella-widget-card card-custom">
                <div class="gambella-widget-head">
                    <div>
                        <h3>Recent Projects</h3>
                    </div>
                    <a href="{{ route('projects.index') }}" class="gambella-link-viewall">
                        View all &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-borderless mb-0">
                        <thead>
                            <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">
                                <th class="pb-2">Title</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2">Researcher</th>
                                <th class="pb-2" style="width: 140px;">Progress</th>
                                <th class="pb-2 text-end">Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjectsList as $idx => $p)
                            @php
                                $pct = match($p->status) {
                                    'Completed' => 100,
                                    'Active'    => 75,
                                    'Approved'  => 65,
                                    'UnderReview'=> 40,
                                    'Submitted' => 20,
                                    default     => 10,
                                };
                                $icons = ['bi-file-text text-primary', 'bi-lightning-charge text-success', 'bi-tree text-success', 'bi-droplet text-info', 'bi-heart text-purple'];
                                $icon = $icons[$idx % count($icons)];
                            @endphp
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('projects.show', $p->project_id) }}" class="fw-semibold text-dark text-decoration-none d-block text-truncate" style="max-width: 260px; font-size: 0.86rem;">
                                                {{ $p->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(in_array($p->status, ['Active', 'Approved']))
                                        <span class="badge" style="background: #eff6ff; color: #2563eb; font-weight: 600; font-size: 0.75rem; border-radius: 999px; padding: 4px 10px;">In Progress</span>
                                    @elseif($p->status === 'UnderReview')
                                        <span class="badge" style="background: #fff7ed; color: #f97316; font-weight: 600; font-size: 0.75rem; border-radius: 999px; padding: 4px 10px;">Under Review</span>
                                    @elseif($p->status === 'Submitted')
                                        <span class="badge" style="background: #ecfdf5; color: #10b981; font-weight: 600; font-size: 0.75rem; border-radius: 999px; padding: 4px 10px;">Submitted</span>
                                    @elseif($p->status === 'Completed')
                                        <span class="badge" style="background: #f5f3ff; color: #8b5cf6; font-weight: 600; font-size: 0.75rem; border-radius: 999px; padding: 4px 10px;">Completed</span>
                                    @else
                                        <span class="badge" style="background: #f1f5f9; color: #64748b; font-weight: 600; font-size: 0.75rem; border-radius: 999px; padding: 4px 10px;">{{ $p->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary" style="font-size: 0.82rem;">
                                        {{ preg_replace('/\s*\([^)]*\)/', '', $p->pi->name ?? 'Dr. Researcher') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small fw-semibold text-muted" style="width: 32px; font-size: 0.75rem;">{{ $pct }}%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; border-radius: 999px; background: #e2e8f0;">
                                            <div class="progress-bar" style="width: {{ $pct }}%; background: #2563eb; border-radius: 999px;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted" style="font-size: 0.78rem;">
                                        {{ $p->created_at->addMonths(4)->format('M d, Y') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No projects available yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Upcoming Deadlines & Quick Actions (Col 4) --}}
        <div class="col-xl-4">
            {{-- Upcoming Deadlines Card --}}
            <div class="gambella-widget-card card-custom mb-3">
                <div class="gambella-widget-head mb-2">
                    <div>
                        <h3>Upcoming Deadlines</h3>
                    </div>
                    <a href="{{ route('calls.index') }}" class="gambella-link-viewall">
                        View all &rarr;
                    </a>
                </div>

                <div class="d-flex flex-column">
                    <div class="gambella-deadline-item">
                        <div class="gambella-date-box" style="background: #ffe4e6; color: #e11d48;">
                            <span class="gambella-date-box-month">Sep</span>
                            <span class="gambella-date-box-day">15</span>
                        </div>
                        <div class="gambella-deadline-info">
                            <div class="gambella-deadline-title">Research Proposal Submission</div>
                            <div class="gambella-deadline-sub">Institutional Grant Window Call</div>
                            <span class="gambella-deadline-days">2 days left</span>
                        </div>
                    </div>

                    <div class="gambella-deadline-item">
                        <div class="gambella-date-box" style="background: #fef3c7; color: #d97706;">
                            <span class="gambella-date-box-month">Sep</span>
                            <span class="gambella-date-box-day">30</span>
                        </div>
                        <div class="gambella-deadline-info">
                            <div class="gambella-deadline-title">Midterm Progress Review</div>
                            <div class="gambella-deadline-sub">Tranche 2 Deliverables Clearance</div>
                            <span class="gambella-deadline-days" style="color: #d97706;">17 days left</span>
                        </div>
                    </div>

                    <div class="gambella-deadline-item">
                        <div class="gambella-date-box" style="background: #fff7ed; color: #ea580c;">
                            <span class="gambella-date-box-month">Oct</span>
                            <span class="gambella-date-box-day">20</span>
                        </div>
                        <div class="gambella-deadline-info">
                            <div class="gambella-deadline-title">Final Report Submission</div>
                            <div class="gambella-deadline-sub">Fiscal Tranche 3 Closeout</div>
                            <span class="gambella-deadline-days" style="color: #059669;">37 days left</span>
                        </div>
                    </div>

                    <div class="gambella-deadline-item">
                        <div class="gambella-date-box" style="background: #eff6ff; color: #2563eb;">
                            <span class="gambella-date-box-month">Nov</span>
                            <span class="gambella-date-box-day">10</span>
                        </div>
                        <div class="gambella-deadline-info">
                            <div class="gambella-deadline-title">Research Symposium Presentation</div>
                            <div class="gambella-deadline-sub">Annual Academic Colloquium</div>
                            <span class="gambella-deadline-days" style="color: #2563eb;">58 days left</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions 2x2 Grid --}}
            <div class="gambella-widget-card card-custom">
                <div class="gambella-widget-head mb-3">
                    <div>
                        <h3>Quick Actions</h3>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('projects.create') }}" class="gambella-quick-tile">
                            <div class="gambella-quick-tile-icon" style="background: #2563eb; color: #ffffff;">
                                <i class="bi bi-plus-lg"></i>
                            </div>
                            <span>Create New Project</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('projects.index') }}" class="gambella-quick-tile" style="background: #f0fdf4;">
                            <div class="gambella-quick-tile-icon" style="background: #ecfdf5; color: #10b981;">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <span>Add Researcher</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('procurement.index') }}" class="gambella-quick-tile" style="background: #faf5ff;">
                            <div class="gambella-quick-tile-icon" style="background: #f5f3ff; color: #8b5cf6;">
                                <i class="bi bi-people"></i>
                            </div>
                            <span>Manage Team</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('analytics') }}" class="gambella-quick-tile" style="background: #fff7ed;">
                            <div class="gambella-quick-tile-icon" style="background: #fff7ed; color: #ea580c;">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </div>
                            <span>Generate Report</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Role Specific Queue Section (All 11 Roles Operational) --}}
    @if($role === 'pi')
    <div class="gambella-widget-card card-custom mb-4">
        <div class="gambella-widget-head">
            <div>
                <h3>My Research Submissions</h3>
                <p>Tracking your submitted proposals and approval stages</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-success fw-bold">
                <i class="bi bi-plus-lg me-1"></i> New Proposal
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Project Title</th>
                        <th>Thematic Area</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myProjects as $p)
                    <tr>
                        <td class="font-monospace text-muted">#{{ $p->project_id }}</td>
                        <td class="fw-bold text-dark">{{ $p->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $p->thematicArea ? $p->thematicArea->title : 'General' }}</span></td>
                        <td class="fw-semibold text-dark">{{ number_format($p->requested_budget, 2) }} ETB</td>
                        <td>
                            @if($p->status === 'Approved')
                                <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Approved</span>
                            @elseif($p->status === 'Active')
                                <span class="badge bg-primary text-white px-2 py-1"><i class="bi bi-play-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $p->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if(in_array($p->status, ['Draft', 'Returned']))
                                <form action="{{ route('projects.submit', $p->project_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-success fw-bold confirm-btn" data-confirm-title="Submit Proposal" data-confirm-message="Submit proposal to Department Head?" data-confirm-btn-text="Yes, Submit"><i class="bi bi-send me-1"></i>Submit</button>
                                </form>
                            @endif
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No submissions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($role === 'reviewer')
    <div class="gambella-widget-card card-custom mb-4">
        <div class="gambella-widget-head">
            <div>
                <h3>Assigned Blind Evaluations</h3>
                <p>Proposals awaiting scientific critique and scoring</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Eval ID</th>
                        <th>Proposal Title</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedReviews as $rev)
                    <tr>
                        <td class="font-monospace text-muted">#EV-{{ $rev->eval_id }}</td>
                        <td class="fw-bold text-dark">{{ $rev->project->title }}</td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $rev->decision }}</span></td>
                        <td><span class="fw-bold">{{ $rev->score }}</span> <span class="text-muted small">/ 100</span></td>
                        <td class="text-end">
                            <a href="{{ route('evaluations.show', $rev->eval_id) }}" class="btn btn-sm btn-dark fw-bold">
                                <i class="bi bi-pencil-square me-1"></i> Score &amp; Critique
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No pending evaluations.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(in_array($role, ['coordinator', 'dean', 'vparttcs', 'rcsc', 'dh', 'irerc']))
    <div class="gambella-widget-card card-custom mb-4">
        <div class="gambella-widget-head">
            <div>
                <h3>Institutional Governance Queue</h3>
                <p>Proposals awaiting administrative review or budget ratification</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>PI Name</th>
                        <th>Requested Budget</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingApprovals as $p)
                    <tr>
                        <td class="font-monospace text-muted">#{{ $p->project_id }}</td>
                        <td class="fw-bold text-dark">{{ $p->title }}</td>
                        <td>{{ preg_replace('/\s*\([^)]*\)/', '', $p->pi->name ?? 'N/A') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ number_format($p->requested_budget, 2) }} ETB</div>
                            @if($p->requested_budget >= 500000)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:0.7rem;">RCSC / VP Tier (&ge;500k)</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size:0.7rem;">Dean Tier (&lt;500k)</span>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $p->status }}</span></td>
                        <td class="text-end">
                            @if($p->status === 'Dean_Review' && in_array($role, ['dean', 'vparttcs', 'admin']))
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-success text-white fw-bold">Ratify Budget</a>
                            @elseif($p->status === 'RCSC_Review' && in_array($role, ['rcsc', 'vparttcs', 'admin']))
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-danger text-white fw-bold">Ratify Budget</a>
                            @endif
                            <a href="{{ route('projects.show', $p->project_id) }}" class="btn btn-sm btn-outline-secondary">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Governance queue empty.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($role === 'finance')
    <div class="gambella-widget-card card-custom mb-4">
        <div class="gambella-widget-head">
            <div>
                <h3>Pending Tranche Disbursements</h3>
                <p>Approved project milestones ready for fiscal release</p>
            </div>
            <a href="{{ route('finance.disbursement') }}" class="btn btn-sm btn-outline-dark fw-semibold">Finance Portal &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Project</th>
                        <th>PI Name</th>
                        <th>Phase</th>
                        <th>Amount</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingDisbursements as $req)
                    <tr>
                        <td class="font-monospace text-muted">#REQ-{{ $req->request_id }}</td>
                        <td class="fw-bold text-dark">{{ $req->project->title ?? 'N/A' }}</td>
                        <td>{{ preg_replace('/\s*\([^)]*\)/', '', $req->project->pi->name ?? 'N/A') }}</td>
                        <td><span class="badge bg-dark text-white px-2 py-1">{{ $req->milestone_phase }}</span></td>
                        <td class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-success fw-bold text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#dashDisburseModal{{ $req->request_id }}">
                                <i class="bi bi-cash me-1"></i> Disburse
                            </button>
                        </td>
                    </tr>

                    {{-- Disbursement Modal --}}
                    <div class="modal fade" id="dashDisburseModal{{ $req->request_id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                                <form action="{{ route('finance.process-disbursement', $req->request_id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header py-3 px-4 bg-dark text-white">
                                        <h6 class="modal-title fw-bold text-white mb-0">Release Funds &bull; {{ $req->milestone_phase }}</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="p-3 mb-3 bg-light rounded-3 border">
                                            <div class="fw-bold text-dark text-truncate">{{ $req->project->title ?? 'N/A' }}</div>
                                            <div class="d-flex justify-content-between text-muted mt-2 small">
                                                <span>{{ $req->project->pi->name ?? 'N/A' }}</span>
                                                <span class="fw-bold text-dark">{{ number_format($req->approved_amount ?? 0, 2) }} ETB</span>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold mb-1">Approved Amount</label>
                                                <input type="text" class="form-control form-control-sm bg-light fw-bold" value="{{ number_format($req->approved_amount, 2) }} ETB" readonly>
                                                <input type="hidden" name="amount" value="{{ $req->approved_amount }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold mb-1">Payment Method</label>
                                                <select name="payment_method" class="form-select form-select-sm" required>
                                                    <option value="Bank Transfer" selected>Bank Transfer</option>
                                                    <option value="Check">Check</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold mb-1">Transaction Ref / Voucher</label>
                                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. CBE Ref #UNI-2026-9812">
                                        </div>
                                    </div>
                                    <div class="modal-footer py-2 px-4 bg-light border-top d-flex justify-content-between">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success fw-bold px-4">Release Funds Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No pending tranche disbursements.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
