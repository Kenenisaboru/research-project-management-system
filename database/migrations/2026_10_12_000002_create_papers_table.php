<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects', 'project_id')->onDelete('set null');
            $table->foreignId('submitter_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('abstract_text');
            $table->text('authors');
            $table->string('journal_name')->nullable();
            $table->string('journal_index')->nullable()->comment('SCIE, SSCI, Scopus, ESCI, National, None');
            $table->string('paper_file_path')->nullable();
            $table->enum('status', ['Submitted', 'UnderEvaluation', 'Approved', 'Rejected', 'Recorded'])->default('Submitted');
            $table->text('evaluation_summary')->nullable();
            $table->date('publication_date')->nullable();
            $table->enum('award_status', ['None', 'Nominated', 'Awarded'])->default('None');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('papers');
    }
};