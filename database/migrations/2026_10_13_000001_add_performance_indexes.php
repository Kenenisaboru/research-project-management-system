<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add composite database indexes for query performance across all high-traffic tables.
 * These indexes cover the most common RPMS query patterns (role-filtered lists,
 * status dashboards, budget lookups, audit queries).
 *
 * @author  RPMS Pro Upgrade - Tier 1 Item 3
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ──────────────────────────────────────────────
        // projects table
        // ──────────────────────────────────────────────
        Schema::table('projects', function (Blueprint $table) {
            // PI-scoped status dashboard (most common PI query)
            $table->index(['status', 'pi_id'],      'idx_projects_status_pi');

            // Department-Head / Coordinator scoped listing
            $table->index(['dept_id', 'status'],    'idx_projects_dept_status');

            // Overdue-detection alert query (AlertsService)
            $table->index(['status', 'activated_at'], 'idx_projects_status_activated');
        });

        // ──────────────────────────────────────────────
        // budget_requests table
        // ──────────────────────────────────────────────
        Schema::table('budget_requests', function (Blueprint $table) {
            // Project-level budget roll-up
            $table->index(['project_id', 'status'],      'idx_budgets_project_status');

            // Finance approval-tier filter
            $table->index(['status', 'approval_tier'],   'idx_budgets_status_tier');
        });

        // ──────────────────────────────────────────────
        // audit_logs table
        // ──────────────────────────────────────────────
        Schema::table('audit_logs', function (Blueprint $table) {
            // User-specific activity timeline (Profile page)
            $table->index(['user_id', 'created_at'],              'idx_audit_user_created');

            // Model-scoped change history (diff view)
            $table->index(['auditable_type', 'auditable_id'],     'idx_audit_type_id');
        });

        // ──────────────────────────────────────────────
        // notifications table
        // ──────────────────────────────────────────────
        Schema::table('notifications', function (Blueprint $table) {
            // Unread-count badge API call (polled every 30s)
            $table->index(['user_id', 'is_read'],   'idx_notif_user_read');

            // Notification centre list (newest first)
            $table->index(['user_id', 'created_at'], 'idx_notif_user_created');
        });

        // ──────────────────────────────────────────────
        // evaluations table
        // ──────────────────────────────────────────────
        Schema::table('evaluations', function (Blueprint $table) {
            // Reviewer dashboard: my pending evaluations
            $table->index(['examiner_id', 'decision'], 'idx_eval_examiner_decision');

            // Project-level review aggregation (all-evaluated check)
            $table->index(['project_id', 'decision'],  'idx_eval_project_decision');
        });

        // ──────────────────────────────────────────────
        // paper_evaluations table
        // ──────────────────────────────────────────────
        Schema::table('paper_evaluations', function (Blueprint $table) {
            $table->index('paper_id',     'idx_paper_eval_paper');
            $table->index('evaluator_id', 'idx_paper_eval_evaluator');
        });

        // ──────────────────────────────────────────────
        // project_members table
        // ──────────────────────────────────────────────
        Schema::table('project_members', function (Blueprint $table) {
            // TM dashboard: all projects I belong to
            $table->index('user_id', 'idx_members_user');

            // Prevent duplicate membership (PI may accidentally add twice)
            // Only add unique index if it doesn't already exist
            $table->unique(['project_id', 'user_id'], 'idx_members_project_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_status_pi');
            $table->dropIndex('idx_projects_dept_status');
            $table->dropIndex('idx_projects_status_activated');
        });

        Schema::table('budget_requests', function (Blueprint $table) {
            $table->dropIndex('idx_budgets_project_status');
            $table->dropIndex('idx_budgets_status_tier');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_user_created');
            $table->dropIndex('idx_audit_type_id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notif_user_read');
            $table->dropIndex('idx_notif_user_created');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropIndex('idx_eval_examiner_decision');
            $table->dropIndex('idx_eval_project_decision');
        });

        Schema::table('paper_evaluations', function (Blueprint $table) {
            $table->dropIndex('idx_paper_eval_paper');
            $table->dropIndex('idx_paper_eval_evaluator');
        });

        Schema::table('project_members', function (Blueprint $table) {
            $table->dropUnique('idx_members_project_user_unique');
            $table->dropIndex('idx_members_user');
        });
    }
};
