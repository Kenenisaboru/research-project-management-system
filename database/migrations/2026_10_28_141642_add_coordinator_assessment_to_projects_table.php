<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('coordinator_assessment')->nullable()->after('feedback');
            $table->timestamp('coordinator_assessed_at')->nullable()->after('coordinator_assessment');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['coordinator_assessment', 'coordinator_assessed_at']);
        });
    }
};
