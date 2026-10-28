<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Record an audit event into the immutable audit_logs table.
     *
     * @param string $action E.g. LOGIN, LOGOUT, CREATE, UPDATE, DELETE, SUBMIT, APPROVE, REJECT, PASSWORD_RESET
     * @param string|null $entityType E.g. User, Project, MilestoneReport, BudgetRequest, Evaluation, ThematicArea
     * @param int|string|null $entityId
     * @param string|null $details
     * @param int|null $userId
     * @param array|null $diffJson
     * @return AuditLog
     */
    public static function log(
        string $action,
        ?string $entityType = null,
        $entityId = null,
        ?string $details = null,
        ?int $userId = null,
        ?array $diffJson = null
    ): AuditLog {
        $url = null;
        try {
            $url = Request::fullUrl() ?: Request::url();
            if ($url) {
                $url = substr($url, 0, 500);
            }
        } catch (\Throwable $e) {
            $url = null;
        }

        return AuditLog::create([
            'user_id'        => $userId ?: Auth::id(),
            'action'         => strtoupper($action),
            'entity_type'    => $entityType,
            'entity_id'      => $entityId ? (int)$entityId : null,
            'details'        => $details,
            'ip_address'     => Request::ip() ?: '127.0.0.1',
            'user_agent'     => Request::userAgent(),
            'request_method' => Request::method() ?: 'CLI',
            'request_url'    => $url,
            'diff_json'      => $diffJson,
        ]);
    }

    /**
     * Log a model modification with before/after attribute diff snapshot.
     *
     * @param string $action
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $before
     * @param array $after
     * @param string|null $details
     * @return AuditLog
     */
    public static function diff(
        string $action,
        \Illuminate\Database\Eloquent\Model $model,
        array $before,
        array $after,
        ?string $details = null
    ): AuditLog {
        $changes = [];
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($allKeys as $key) {
            $oldVal = $before[$key] ?? null;
            $newVal = $after[$key] ?? null;
            if ($oldVal !== $newVal) {
                $changes[$key] = [
                    'before' => $oldVal,
                    'after'  => $newVal,
                ];
            }
        }

        $entityClass = class_basename($model);
        $diffPayload = [
            'entity'  => $entityClass,
            'id'      => $model->getKey(),
            'table'   => $model->getTable(),
            'changes' => $changes,
        ];

        $desc = $details ?: sprintf(
            '%s #%s updated (%d attribute%s changed)',
            $entityClass,
            $model->getKey(),
            count($changes),
            count($changes) === 1 ? '' : 's'
        );

        return self::log(
            $action,
            $entityClass,
            $model->getKey(),
            $desc,
            Auth::id(),
            $diffPayload
        );
    }
}

