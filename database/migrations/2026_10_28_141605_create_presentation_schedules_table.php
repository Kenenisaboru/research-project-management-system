<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presentation_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->enum('type', ['InitialDefence', 'BudgetApproval', 'ProgressReport', 'FinalDefence']);
            $table->dateTime('scheduled_at');
            $table->string('venue', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('arranged_by');
            $table->enum('status', ['Scheduled', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->timestamps();

            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreign('arranged_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['project_id', 'type']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presentation_schedules');
    }
};
