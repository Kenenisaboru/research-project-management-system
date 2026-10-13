<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\Project;
use App\Models\ThematicArea;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProcessCsvImportJob
 *
 * Background job to process large legacy CSV data migrations asynchronously without web timeouts.
 */
class ProcessCsvImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    /**
     * Create a new job instance.
     *
     * @param  string  $storedCsvPath
     * @param  int|null  $adminUserId
     */
    public function __construct(
        public string $storedCsvPath,
        public ?int $adminUserId = null
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->storedCsvPath);
        if (!file_exists($fullPath)) {
            return;
        }

        $handle = fopen($fullPath, 'r');
        if ($handle === false) {
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return;
        }

        $headerMap = array_flip(array_map('trim', $header));
        $imported  = 0;
        $fallbackPi = User::where('role', 'pi')->first();

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $title = $row[$headerMap['title'] ?? 0] ?? null;
            if (!$title) {
                continue;
            }

            $piEmail = $row[$headerMap['pi_email'] ?? 2] ?? null;
            $pi = $piEmail ? User::where('email', $piEmail)->first() : null;
            $piId = $pi?->id ?? $fallbackPi?->id;

            $deptCode = $row[$headerMap['department_code'] ?? 5] ?? null;
            $dept = $deptCode ? Department::where('code', $deptCode)->first() : null;
            $deptId = $dept?->id ?? $pi?->dept_id ?? Department::first()?->id;

            $thematicTitle = $row[$headerMap['thematic_title'] ?? 6] ?? null;
            $thematic = $thematicTitle ? ThematicArea::where('title', 'like', "%{$thematicTitle}%")->first() : null;
            $thematicId = $thematic?->id ?? ThematicArea::first()?->id;

            Project::create([
                'title'            => $title,
                'abstract_text'    => $row[$headerMap['abstract_text'] ?? 1] ?? 'Imported project.',
                'pi_id'            => $piId,
                'dept_id'          => $deptId,
                'thematic_id'      => $thematicId,
                'requested_budget' => (float) ($row[$headerMap['requested_budget'] ?? 3] ?? 0),
                'status'           => $row[$headerMap['status'] ?? 4] ?? 'Draft',
            ]);

            $imported++;
        }

        fclose($handle);
        Storage::disk('local')->delete($this->storedCsvPath);

        AuditService::log(
            'LEGACY_IMPORT_ASYNC',
            'Project',
            null,
            "Async legacy data migration completed. Imported {$imported} project record(s).",
            $this->adminUserId
        );
    }
}
