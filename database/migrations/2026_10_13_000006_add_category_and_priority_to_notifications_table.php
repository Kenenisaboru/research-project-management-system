<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'category')) {
                $table->string('category', 50)->default('workflow')->after('type');
            }
            if (!Schema::hasColumn('notifications', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('category');
            }
            if (!Schema::hasColumn('notifications', 'action_url')) {
                $table->string('action_url', 500)->nullable()->after('message');
            }
            if (!Schema::hasColumn('notifications', 'icon')) {
                $table->string('icon', 50)->nullable()->after('action_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $cols = [];
            foreach (['category', 'priority', 'action_url', 'icon'] as $col) {
                if (Schema::hasColumn('notifications', $col)) {
                    $cols[] = $col;
                }
            }
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};
