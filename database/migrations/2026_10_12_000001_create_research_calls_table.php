<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('research_calls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('call_type', ['project', 'abstract', 'community_service'])->default('project');
            $table->foreignId('dept_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('thematic_id')->nullable()->constrained('thematic_areas')->onDelete('set null');
            $table->string('funding_organization')->nullable();
            $table->decimal('budget_ceiling', 15, 2)->nullable();
            $table->date('submission_deadline')->nullable();
            $table->enum('status', ['Draft', 'Open', 'Closed'])->default('Open');
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_calls');
    }
};