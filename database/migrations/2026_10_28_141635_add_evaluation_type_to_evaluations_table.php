<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->enum('evaluation_type', ['Proposal', 'Progress', 'FinalDefence'])
                  ->default('Proposal')
                  ->after('project_id');
            $table->enum('final_decision', ['Accepted', 'AcceptedWithMinorModification', 'Rejected'])
                  ->nullable()
                  ->after('decision');
            $table->text('revision_comments')->nullable()->after('final_decision');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['evaluation_type', 'final_decision', 'revision_comments']);
        });
    }
};
