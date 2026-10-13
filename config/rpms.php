<?php

/*
|--------------------------------------------------------------------------
| RPMS Application Configuration
|--------------------------------------------------------------------------
|
| This file centralises all RPMS_* environment variables with full
| documentation comments for each setting. Every value is read via env()
| so no secrets are ever committed to source control.
|
| File: config/rpms.php
| Last updated: 2026-10-13
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Demo Data Seeding
    |--------------------------------------------------------------------------
    |
    | When set to true, the DatabaseSeeder will seed fake projects, users,
    | and workflow events for demonstration purposes. Must be false in production.
    |
    */
    'seed_demo_data' => env('RPMS_SEED_DEMO_DATA', false),

    /*
    |--------------------------------------------------------------------------
    | Overdue Project Threshold (Months)
    |--------------------------------------------------------------------------
    |
    | A project that is still open (Active/Ongoing) and whose last activity is
    | older than this many months is flagged as overdue by the AlertsService
    | and surfaced on dashboards and the Notifications Centre.
    |
    */
    'overdue_months' => (int) env('RPMS_OVERDUE_MONTHS', 12),

    /*
    |--------------------------------------------------------------------------
    | Budget Utilisation Alert Threshold (%)
    |--------------------------------------------------------------------------
    |
    | When released disbursements exceed this percentage of the approved
    | (or requested) budget, an over-budget alert is raised.
    |
    */
    'budget_alert_percent' => (int) env('RPMS_BUDGET_ALERT_PERCENT', 90),

    /*
    |--------------------------------------------------------------------------
    | Backup Retention (Days)
    |--------------------------------------------------------------------------
    |
    | The `backups:create --prune` command and the scheduler remove backup
    | files older than this window.
    |
    */
    'backup_retention_days' => (int) env('RPMS_BACKUP_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Scheduler: Automatic Backup Cadence
    |--------------------------------------------------------------------------
    |
    | A daily backup is taken automatically. Set to false to disable the
    | scheduled backup (manual backups from the admin panel remain available).
    |
    */
    'scheduled_backup_enabled' => env('RPMS_SCHEDULED_BACKUP', true),

    /*
    |--------------------------------------------------------------------------
    | 2FA (Two-Factor Authentication) Issuer Name
    |--------------------------------------------------------------------------
    |
    | This string appears in authenticator apps (Google Authenticator, Authy)
    | as the account issuer label. Should match the institution name.
    |
    */
    '2fa_issuer' => env('RPMS_2FA_ISSUER', 'Gambella University RPMS'),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Upload Size (MB)
    |--------------------------------------------------------------------------
    |
    | Maximum allowed file size for uploaded documents (proposals, ethics docs,
    | progress report attachments, etc.). Must be consistent with php.ini
    | upload_max_filesize and post_max_size settings.
    |
    */
    'max_upload_mb' => (int) env('RPMS_MAX_UPLOAD_MB', 10),

    /*
    |--------------------------------------------------------------------------
    | Session Timeout (Minutes)
    |--------------------------------------------------------------------------
    |
    | Idle session timeout in minutes. Should match the SESSION_LIFETIME value
    | in .env. Used by the front-end idle timer and the AccountLocked middleware.
    |
    */
    'session_timeout_minutes' => (int) env('RPMS_SESSION_TIMEOUT_MINUTES', 120),

    /*
    |--------------------------------------------------------------------------
    | Password Rotation Warning (Days)
    |--------------------------------------------------------------------------
    |
    | After this many days without a password change, a non-blocking warning
    | flash notice is shown on login. Set to 0 to disable.
    |
    */
    'password_rotation_days' => (int) env('RPMS_PASSWORD_ROTATION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Enable (Supervisor / entrypoint.sh)
    |--------------------------------------------------------------------------
    |
    | When set to true, the Docker entrypoint.sh launches a supervisord-managed
    | queue:work process alongside PHP-FPM + Nginx. Requires the supervisor
    | package in the Docker image.
    |
    */
    'enable_queue_worker' => env('RPMS_ENABLE_QUEUE_WORKER', false),

    /*
    |--------------------------------------------------------------------------
    | Dual-Threshold Financial Governance Limits (ETB)
    |--------------------------------------------------------------------------
    |
    | Budgets below dean_approval_limit are routed for Dean approval.
    | Budgets at or above are escalated to RCSC/VP ARTTCS.
    |
    */
    'dean_approval_limit' => (int) env('RPMS_DEAN_APPROVAL_LIMIT', 500000),

    /*
    |--------------------------------------------------------------------------
    | Notification Polling Interval (Seconds)
    |--------------------------------------------------------------------------
    |
    | How often the front-end polls /api/v1/notifications/poll.
    | The default (30s) balances real-time feel with server load.
    |
    */
    'notification_poll_interval' => (int) env('RPMS_NOTIFICATION_POLL_INTERVAL', 30),

];
