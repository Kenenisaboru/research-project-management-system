<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Add `project_code` column to projects table.
 * Format: UNI-PRJ-{YEAR}-{5-digit-sequence}
 * Auto-generated via the Project model's boot() hook.
 *
 * @author  RPMS Pro Upgrade - Tier 1 Item 3
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Nullable initially so existing rows don't fail
            $table->string('project_code', 30)->nullable()->unique()->after('project_id');
        });

        // Back-fill existing projects with sequential codes
        $year    = date('Y');
        $seq     = 1;
        $projects = DB::table('projects')->orderBy('project_id')->get();

        foreach ($projects as $project) {
            $code = sprintf('UNI-PRJ-%s-%05d', $year, $seq++);
            DB::table('projects')
                ->where('project_id', $project->project_id)
                ->update(['project_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['project_code']);
            $table->dropColumn('project_code');
        });
    }
};
