<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Project;
use App\Models\ThematicArea;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class DataMigrationController extends Controller
{
    private const HEADER = ['title', 'abstract_text', 'pi_email', 'requested_budget', 'status', 'department_code', 'thematic_title'];

    public function index()
    {
        $existing = Project::count();
        $lastImport = \App\Models\AuditLog::where('action', 'LEGACY_IMPORT')
            ->latest()
            ->first();

        return view('admin.data_migration', compact('existing', 'lastImport'));
    }

    public function template()
    {
        $rows = [
            self::HEADER,
            ['IoT Flood Early Warning System', 'Deploys sensor nodes along the Baro river.', 'pi@rpms.local', '480000', 'Draft', 'CS', 'Digital Infrastructure & Smart Governance Solutions'],
        ];

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="legacy_projects_template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Unable to read the uploaded file.');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return back()->with('error', 'The uploaded CSV is empty.');
        }

        // Normalise header (strip BOM, lowercase, trim)
        $header = array_map(fn ($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $h))), $header);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, array_pad($row, count($header), null));
            if ($data === false) {
                $skipped++;
                $errors[] = "Row {$rowNum}: column count mismatch.";
                continue;
            }

            $title = trim((string) ($data['title'] ?? ''));
            if ($title === '') {
                $skipped++;
                $errors[] = "Row {$rowNum}: missing title.";
                continue;
            }

            $pi = null;
            $piEmail = trim((string) ($data['pi_email'] ?? ''));
            if ($piEmail !== '') {
                $pi = User::where('email', $piEmail)->first();
            }
            if (! $pi) {
                $pi = User::where('role', 'pi')->first();
            }
            if (! $pi) {
                $skipped++;
                $errors[] = "Row {$rowNum}: no PI account available.";
                continue;
            }

            $dept = null;
            $deptCode = trim((string) ($data['department_code'] ?? ''));
            if ($deptCode !== '') {
                $dept = Department::where('code', $deptCode)->orWhere('name', 'LIKE', $deptCode)->first();
            }
            $dept = $dept ?: Department::first();

            $thematic = null;
            $thematicTitle = trim((string) ($data['thematic_title'] ?? ''));
            if ($thematicTitle !== '') {
                $thematic = ThematicArea::where('title', 'LIKE', '%' . $thematicTitle . '%')->first();
            }
            $thematic = $thematic ?: ThematicArea::first();

            $status = trim((string) ($data['status'] ?? 'Draft')) ?: 'Draft';
            $allowed = ['Draft', 'Submitted', 'DH_Screened', 'UnderReview', 'Returned', 'Approved', 'Active', 'Completed', 'Terminated', 'Rejected', 'Withdrawn'];
            if (! in_array($status, $allowed, true)) {
                $status = 'Draft';
            }

            Project::create([
                'title'            => $title,
                'abstract_text'    => (string) ($data['abstract_text'] ?? 'Imported legacy project record.'),
                'pi_id'            => $pi->id,
                'dept_id'          => optional($dept)->id,
                'thematic_id'      => optional($thematic)->id,
                'requested_budget' => (float) ($data['requested_budget'] ?? 0),
                'status'           => $status,
                'current_stage'    => 1,
                'ethical_cleared'  => false,
            ]);
            $imported++;
        }

        fclose($handle);

        AuditService::log('LEGACY_IMPORT', 'Project', null, "Legacy data migration: {$imported} project(s) imported, {$skipped} skipped.");

        if ($imported === 0) {
            return back()->with('error', 'No projects were imported. ' . implode(' ', array_slice($errors, 0, 5)));
        }

        $msg = "{$imported} project(s) imported successfully.";
        if ($skipped > 0) {
            $msg .= " {$skipped} row(s) skipped.";
        }

        return back()->with('success', $msg)->with('import_errors', array_slice($errors, 0, 10));
    }
}