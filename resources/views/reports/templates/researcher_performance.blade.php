<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<<<<<<< HEAD
<title>Gambella University RPMS - Researcher Performance Summary</title>
=======
<title>RPMS - Researcher Performance Summary</title>
>>>>>>> upstream/main
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #1e293b; padding: 25px; }
    .header { border-bottom: 2px solid #0f3e2e; padding-bottom: 10px; margin-bottom: 12px; text-align: center; }
    .header h1 { color: #0f3e2e; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
    .header h3 { color: #475569; font-size: 11px; font-weight: normal; margin-bottom: 4px; }
    .doc-title { font-size: 13px; font-weight: bold; color: #047857; text-transform: uppercase; }

    .meta-box { width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; margin-bottom: 15px; border-collapse: collapse; font-size: 9px; }
    .meta-box td { padding: 5px 8px; }

    table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.report-table th, table.report-table td { border: 1px solid #cbd5e1; padding: 6px 7px; font-size: 8.5px; text-align: left; }
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
<<<<<<< HEAD
    <h1>Gambella University &bull; Directorate of Research &amp; Community Service</h1>
=======
    <h1>Directorate of Research &amp; Community Service</h1>
>>>>>>> upstream/main
    <h3>Research Project Management System (RPMS) &bull; Academic Staff Performance</h3>
    <div class="doc-title">Principal Investigator &amp; Researcher Performance Summary</div>
</div>

<table class="meta-box">
    <tr>
        <td style="width: 25%;"><strong>Ref:</strong> {{ $refNumber }}</td>
        <td style="width: 25%;"><strong>Researchers Ranked:</strong> {{ count($researchers) }} Investigators</td>
        <td style="width: 25%;"><strong>Classification:</strong> ACADEMIC EVALUATION</td>
        <td style="width: 25%; text-align: right;"><strong>Date:</strong> {{ $generatedAt }}</td>
    </tr>
</table>

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 30%;">Researcher Name &amp; Staff ID</th>
            <th style="width: 25%;">Department</th>
            <th style="width: 15%; text-align: center;">Total Proposals</th>
            <th style="width: 12%; text-align: center;">Active Projects</th>
            <th style="width: 13%; text-align: center;">Completed Grants</th>
        </tr>
    </thead>
    <tbody>
        @forelse($researchers as $index => $r)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $r->name }}</strong><br>
                <span style="color: #64748b; font-size: 7.5px;">{{ $r->staff_id ?? 'N/A' }} &bull; {{ $r->email }}</span>
            </td>
            <td>{{ $r->department->name ?? 'General Faculty' }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $r->total_proposals }}</td>
            <td style="text-align: center; color: #15803d; font-weight: bold;">{{ $r->active_proposals }}</td>
            <td style="text-align: center; color: #0284c7; font-weight: bold;">{{ $r->completed_proposals }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align: center; color: #64748b;">No researcher performance records available.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="signatures">
    <tr>
        <td>
            <div class="sig-line">Director of Academic Affairs</div>
<<<<<<< HEAD
            <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Gambella University</div>
        </td>
        <td>
            <div class="sig-line">Vice President, ARTTCS</div>
            <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Gambella University</div>
=======
            <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Academic Directorate</div>
        </td>
        <td>
            <div class="sig-line">Vice President, Academic Affairs</div>
            <div style="font-size: 8px; color: #64748b; margin-top: 2px;">Executive Office</div>
>>>>>>> upstream/main
        </td>
    </tr>
</table>

<div class="footer">
<<<<<<< HEAD
    Gambella University RPMS &bull; Academic Performance Directorate &bull; Report by {{ $generatedBy }}
=======
    RPMS &bull; Academic Performance Directorate &bull; Report by {{ $generatedBy }}
>>>>>>> upstream/main
</div>

</body>
</html>
