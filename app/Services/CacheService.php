<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Closure;

/**
 * Centralised caching helper for the RPMS application.
 *
 * All cache keys and TTLs are defined here as constants so they can
 * be invalidated reliably from any part of the application without
 * hard-coding key strings in multiple places.
 *
 * @author  RPMS Pro Upgrade - Tier 1 Item 4
 */
class CacheService
{
    // ─── TTL constants (seconds) ──────────────────────────────────────────────

    /** Stats cards on the dashboard re-calculate every 60 s. */
    public const TTL_DASHBOARD_STATS = 60;

    /** Reference data (thematic areas, colleges, departments) – 5 min. */
    public const TTL_REF_DATA = 300;

    /** Alerts banner (overdue / over-budget) – 2 min. */
    public const TTL_ALERTS = 120;

    // ─── Key builders ─────────────────────────────────────────────────────────

    /**
     * Cache key for role-specific dashboard statistics.
     *
     * @param  int    $userId  The authenticated user's ID.
     * @param  string $role    The user's system role string.
     */
    public static function dashboardStatsKey(int $userId, string $role): string
    {
        return "rpms.dashboard.stats.{$role}.{$userId}";
    }

    /** Cache key for the full thematic areas list. */
    public static function thematicAreasKey(): string
    {
        return 'rpms.ref.thematic_areas';
    }

    /** Cache key for the full colleges list. */
    public static function collegesKey(): string
    {
        return 'rpms.ref.colleges';
    }

    /** Cache key for the full departments list. */
    public static function departmentsKey(): string
    {
        return 'rpms.ref.departments';
    }

    /** Cache key for the system-wide alerts banner. */
    public static function alertsKey(): string
    {
        return 'rpms.alerts.system';
    }

    // ─── Generic helpers ──────────────────────────────────────────────────────

    /**
     * Retrieve a cached value, computing it via $callback if missing.
     *
     * @template T
     * @param  string   $key      Cache key.
     * @param  int      $ttl      Time-to-live in seconds.
     * @param  Closure():T $callback Value factory called on a cache miss.
     * @return T
     */
    public static function remember(string $key, int $ttl, Closure $callback): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Store a value directly in the cache.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $ttl   Time-to-live in seconds.
     */
    public static function put(string $key, mixed $value, int $ttl): void
    {
        Cache::put($key, $value, $ttl);
    }

    /**
     * Remove one or more cache keys.
     *
     * @param  string ...$keys
     */
    public static function forget(string ...$keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    // ─── Domain-specific invalidation helpers ─────────────────────────────────

    /**
     * Flush all dashboard stat entries for a specific user+role pair.
     *
     * @param  int    $userId
     * @param  string $role
     */
    public static function invalidateDashboardStats(int $userId, string $role): void
    {
        static::forget(static::dashboardStatsKey($userId, $role));
    }

    /**
     * Flush the reference data caches (thematic areas, colleges, departments).
     * Call this from Admin CRUD actions on those tables.
     */
    public static function invalidateRefData(): void
    {
        static::forget(
            static::thematicAreasKey(),
            static::collegesKey(),
            static::departmentsKey(),
        );
    }

    /**
     * Flush the alerts banner cache.
     */
    public static function invalidateAlerts(): void
    {
        static::forget(static::alertsKey());
    }
}
