<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gambella University RPMS - Annual Research Output Report</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #1e293b; padding: 25px; }
    .header { border-bottom: 2px solid #0f3e2e; padding-bottom: 10px; margin-bottom: 12px; text-align: center; }
    .header h1 { color: #0f3e2e; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
    .header h3 { color: #475569; font-size: 11px; font-weight: normal; margin-bottom: 4px; }
    .doc-title { font-size: 13px; font-weight: bold; color: #047857; text-transform: uppercase; letter-spacing: 0.5px; }

    .meta-box { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; margin-bottom: 15px; border-collapse: collapse; font-size: 9px; }
    .meta-box td { padding: 5px 8px; }

    .kpi-cards { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 15px; }
    .kpi-card { width: 25%; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px; text-align: center; }
    .kpi-label { font-size: 8px; text-transform: uppercase; color: #64748b; font-weight: bold; }
    .kpi-value { font-size: 14px; font-weight: bold; color: #0f3e2e; margin-top: 2px; }

    .section-title { font-size: 11px; font-weight: bold; color: #0f3e2e; background: #f1f5f9; padding: 5px 8px; margin: 12px 0 6px 0; border-left: 3px solid #0f3e2e; text-transform: uppercase; }

    table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.report-table th, table.report-table td { border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 8.5px; text-align: left; }
    table.report-table th { background: #0f3e2e; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 8px; }
    table.report-table tr:nth-child(even) { background: #f8fafc; }

    .signatures { width: 100%; margin-top: 35px; border-collapse: collapse; }
    .signatures td { width: 50%; text-align: center; padding: 0 20px; vertical-align: top; }
    .sig-line { border-top: 1px solid #475569; padding-top: 4px; font-weight: bold; font-size: 9px; color: #1e293b; margin-top: 40px; }

    .footer { margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 6px; font-size: 8px; color: #64748b; text-align: center; }
</style>
</head>
<body>

<div class="header">
    <h1>Gambella University &bull; Directorate of Research &amp; Community Service</h1>
    <h3>Research Project Management System (RPMS) &bull; Institutional Output Archive</h3>
    <div class="doc-title">Annual Research Output &amp; Governance Performance Report ({{ $year }})</div>
</div>

<table class="meta-box">
    <tr>
        <td style="width: 25%;"><strong>Reference:</strong> {{ $refNumber }}</td>
        <td style="width: 25%;"><strong>Reporting Cycle:</strong> Academic Year {{ $year }}</td>
        <td style="width: 25%;"><strong>Generated:</strong> {{ $generatedAt }}</td>
        <td style="width: 25%; text-align: right;"><strong>Authority:</strong> Office of VP (ARTTCS)</td>
    </tr>
</table>

<table class="kpi-cards">
    <tr>
        <td class="kpi-card">
            <div class="kpi-label">Funded Grants</div>
            <div class="kpi-value">{{ count($projects) }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Completed Projects</div>
            <div class="kpi-value">{{ $completedCount }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Total Approved Budget</div>
            <div class="kpi-value">ETB {{ number_format($approvedGrantsValue, 0) }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Publications Produced</div>
            <div class="kpi-value">{{ count($papers) }}</div>
        </td>
    </tr>
</table>

<div class="section-title">1. Research Grants &amp; Projects Overview</div>
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 15%;">Project Code</th>
            <th style="width: 35%;">Project Title</th>
            <th style="width: 20%;">Principal Investigator</th>
            <th style="width: 15%;">Thematic Area</th>
            <th style="width: 15%; text-align: right;">Approved (ETB)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projects as $p)
        <tr>
            <td>{{ $p->project_code ?? ('UNI-PRJ-' . $p->project_id) }}</td>
            <td><strong>{{ $p->title }}</strong></td>
            <td>{{ $p->pi->name ?? 'N/A' }}</td>
            <td>{{ $p->thematicArea->title ?? 'General' }}</td>
            <td style="text-align: right;">{{ number_format((float) ($p->approved_budget ?? $p->requested_budget), 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align: center; color: #64748b;">No projects registered for cycle {{ $year }}.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">2. Academic Papers &amp; Publication Registry</div>
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 40%;">Paper Title</th>
            <th style="width: 25%;">First Author</th>
            <th style="width: 20%;">Journal / Index</th>
            <th style="width: 15%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($papers as $paper)
        <tr>
            <td><strong>{{ $paper->title }}</strong></td>
            <td>{{ $paper->author->name ?? ($paper->authors ?? 'N/A') }}</td>
            <td>{{ $paper->journal_name }} ({{ $paper->journal_index ?? 'Unindexed' }})</td>
            <td>{{ $paper->status }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align: center; color: #64748b;">No papers recorded in publication registry for cycle {{ $year }}.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            <div class="sig-line">Director, Research &amp; Community Service</div>
            <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Gambella University</div>
        </td>
        <td>
            <div class="sig-line">Vice President, ARTTCS</div>
            <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Gambella University</div>
        </td>
    </tr>
</table>

<div class="footer">
    Gambella University RPMS &bull; Official Institutional Record &bull; Generated by {{ $generatedBy }}
</div>

</body>
</html>
