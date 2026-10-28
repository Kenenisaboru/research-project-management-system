<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gambella University RPMS - Grant Expenditure & Financial Audit</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 9.5px; color: #1e293b; padding: 25px; }
    .header { border-bottom: 2px solid #0f3e2e; padding-bottom: 10px; margin-bottom: 12px; text-align: center; }
    .header h1 { color: #0f3e2e; font-size: 15px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
    .header h3 { color: #475569; font-size: 10.5px; font-weight: normal; margin-bottom: 3px; }
    .doc-title { font-size: 12px; font-weight: bold; color: #b45309; text-transform: uppercase; }

    .meta-box { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; margin-bottom: 12px; border-collapse: collapse; font-size: 8.5px; }
    .meta-box td { padding: 4px 6px; }

    .kpi-cards { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 12px; }
    .kpi-card { width: 33.33%; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px; text-align: center; }
    .kpi-label { font-size: 8px; text-transform: uppercase; color: #64748b; font-weight: bold; }
    .kpi-value { font-size: 13px; font-weight: bold; color: #0f3e2e; margin-top: 2px; }

    .section-title { font-size: 10px; font-weight: bold; color: #0f3e2e; background: #f1f5f9; padding: 4px 6px; margin: 10px 0 5px 0; border-left: 3px solid #0f3e2e; text-transform: uppercase; }

    table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.report-table th, table.report-table td { border: 1px solid #cbd5e1; padding: 4px 5px; font-size: 8px; text-align: left; }
    table.report-table th { background: #0f3e2e; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 7.5px; }
    table.report-table tr:nth-child(even) { background: #f8fafc; }

    .signatures { width: 100%; margin-top: 25px; border-collapse: collapse; }
    .signatures td { width: 50%; text-align: center; padding: 0 30px; vertical-align: top; }
    .sig-line { border-top: 1px solid #475569; padding-top: 3px; font-weight: bold; font-size: 8.5px; color: #1e293b; margin-top: 30px; }

    .footer { margin-top: 15px; border-top: 1px dashed #cbd5e1; padding-top: 4px; font-size: 7.5px; color: #64748b; text-align: center; }
</style>
</head>
<body>

<div class="header">
    <h1>Gambella University &bull; Directorate of Finance &amp; Budget Management</h1>
    <h3>Research Project Management System (RPMS) &bull; Grant Disbursement Directorate</h3>
    <div class="doc-title">Official Grant Expenditure &amp; Financial Governance Audit (FY {{ $year }})</div>
</div>

<table class="meta-box">
    <tr>
        <td style="width: 25%;"><strong>Ref:</strong> {{ $refNumber }}</td>
        <td style="width: 25%;"><strong>Audit Cycle:</strong> Fiscal Year {{ $year }}</td>
        <td style="width: 25%;"><strong>Disbursement Rows:</strong> {{ count($disbursements) }} vouchers</td>
        <td style="width: 25%; text-align: right;"><strong>Standard:</strong> Dual-Threshold Governance (&lt;500k / &ge;500k ETB)</td>
    </tr>
</table>

<table class="kpi-cards">
    <tr>
        <td class="kpi-card">
            <div class="kpi-label">Total Released Funds</div>
            <div class="kpi-value" style="color: #15803d;">ETB {{ number_format($totalDisbursed, 2) }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Pending Authorized Disbursements</div>
            <div class="kpi-value" style="color: #b45309;">ETB {{ number_format($totalPending, 2) }}</div>
        </td>
        <td class="kpi-card">
            <div class="kpi-label">Completed Vouchers</div>
            <div class="kpi-value">{{ count($disbursements) }} Vouchers</div>
        </td>
    </tr>
</table>

<div class="section-title">1. Released Disbursement Vouchers (Audit Log)</div>
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 10%;">Voucher ID</th>
            <th style="width: 12%;">Project Code</th>
            <th style="width: 28%;">Project Title</th>
            <th style="width: 18%;">Principal Investigator</th>
            <th style="width: 12%;">Milestone</th>
            <th style="width: 10%; text-align: right;">Amount (ETB)</th>
            <th style="width: 10%;">Disbursed Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($disbursements as $d)
        <tr>
            <td>REQ-{{ $d->request_id }}</td>
            <td>{{ $d->project->project_code ?? ('UNI-PRJ-' . $d->project_id) }}</td>
            <td><strong>{{ $d->project->title ?? 'N/A' }}</strong></td>
            <td>{{ $d->project->pi->name ?? 'N/A' }}</td>
            <td>{{ $d->milestone_phase }}</td>
            <td style="text-align: right; font-weight: bold; color: #15803d;">{{ number_format((float) $d->approved_amount, 2) }}</td>
            <td>{{ $d->disbursed_at ? $d->disbursed_at->format('Y-m-d') : 'N/A' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align: center; color: #64748b;">No released disbursements found for fiscal year {{ $year }}.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">2. Capital Procurement Audit Summary</div>
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 30%;">Item Name</th>
            <th style="width: 20%;">Category</th>
            <th style="width: 25%;">Project</th>
            <th style="width: 15%; text-align: right;">Cost (ETB)</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($procurements as $pr)
        <tr>
            <td><strong>{{ $pr->item_name }}</strong></td>
            <td>{{ $pr->category }}</td>
            <td>{{ $pr->project->title ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format((float) $pr->estimated_cost, 2) }}</td>
            <td>{{ $pr->status }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align: center; color: #64748b;">No procurement orders recorded for cycle {{ $year }}.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            <div class="sig-line">Director of Finance</div>
            <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">Gambella University</div>
        </td>
        <td>
            <div class="sig-line">Internal Audit Office</div>
            <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">Gambella University</div>
        </td>
    </tr>
</table>

<div class="footer">
    Gambella University RPMS &bull; Finance Audit Trail &bull; Generated by {{ $generatedBy }} on {{ $generatedAt }}
</div>

</body>
</html>
