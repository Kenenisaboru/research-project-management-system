<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paper_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->enum('decision', ['RecommendAccept', 'RecommendAcceptWithRevisions', 'Reject']);
            $table->decimal('score', 5, 2)->nullable()->comment('Journal/publication quality score out of 100');
            $table->text('comments');
            $table->timestamps();
            $table->unique(['paper_id', 'evaluator_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('paper_evaluations');
    }
};