<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('pi_id');
            $table->enum('challenge_type', ['Technical', 'Financial']);
            $table->text('description');
            $table->text('proposed_solution')->nullable();
            $table->enum('status', ['Pending', 'Acknowledged', 'Resolved'])->default('Pending');
            $table->text('coordinator_response')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreign('pi_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_challenges');
    }
};
