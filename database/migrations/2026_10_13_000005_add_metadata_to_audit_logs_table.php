<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add metadata columns (user agent, HTTP method, URL, diff snapshot) to audit_logs table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('audit_logs', 'request_method')) {
                $table->string('request_method', 10)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('audit_logs', 'request_url')) {
                $table->string('request_url', 500)->nullable()->after('request_method');
            }
            if (!Schema::hasColumn('audit_logs', 'diff_json')) {
                $table->json('diff_json')->nullable()->after('details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $cols = [];
            foreach (['user_agent', 'request_method', 'request_url', 'diff_json'] as $col) {
                if (Schema::hasColumn('audit_logs', $col)) {
                    $cols[] = $col;
                }
            }
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};
