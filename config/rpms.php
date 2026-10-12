<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Overdue project threshold (months)
    |--------------------------------------------------------------------------
    |
    | A project that is still open (Active/Ongoing) and whose last activity is
    | older than this many months is flagged as overdue by the AlertsService
    | and surfaced on dashboards / the notifications centre.
    |
    */
    'overdue_months' => env('RPMS_OVERDUE_MONTHS', 12),

    /*
    |--------------------------------------------------------------------------
    | Budget utilisation alert threshold (%)
    |--------------------------------------------------------------------------
    |
    | When released disbursements exceed this percentage of the approved
    | (or requested) budget, an over-budget alert is raised.
    |
    */
    'budget_alert_percent' => env('RPMS_BUDGET_ALERT_PERCENT', 90),

    /*
    |--------------------------------------------------------------------------
    | Backup retention (days)
    |--------------------------------------------------------------------------
    |
    | The `backups:create --prune` command and the scheduler remove backup
    | files older than this window.
    |
    */
    'backup_retention_days' => env('RPMS_BACKUP_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Scheduler: automatic backup cadence
    |--------------------------------------------------------------------------
    |
    | A daily backup is taken automatically. Set to false to disable the
    | scheduled backup (manual backups from the admin panel remain available).
    |
    */
    'scheduled_backup_enabled' => env('RPMS_SCHEDULED_BACKUP', true),

];
