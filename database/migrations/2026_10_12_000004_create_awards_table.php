<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('awarded_by')->constrained('users')->onDelete('cascade');
            $table->string('award_name');
            $table->decimal('award_amount', 15, 2)->default(0);
            $table->text('citation')->nullable()->comment('Reason for the award');
            $table->date('awarded_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awards');
    }
};