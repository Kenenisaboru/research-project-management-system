<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * HealthCheckController
 *
 * Provides a public /health endpoint returning system status.
 * No authentication required — suitable for uptime monitors.
 *
 * @package App\Http\Controllers
 */
class HealthCheckController extends Controller
{
    /**
     * Return a JSON health check response.
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $status = 'ok';
        $details = [
            'db'      => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'queue'   => $this->checkQueue(),
        ];

        // Degrade overall status if any subsystem is degraded
        foreach ($details as $check) {
            if ($check !== 'ok') {
                $status = 'degraded';
                break;
            }
        }

        return response()->json([
            'status'    => $status,
            'db'        => $details['db'],
            'storage'   => $details['storage'],
            'queue'     => $details['queue'],
            'version'   => config('app.version', '1.0.0'),
            'timestamp' => now()->toIso8601String(),
        ], $status === 'ok' ? 200 : 503);
    }

    /**
     * Check database connectivity.
     *
     * @return string 'ok' | 'error'
     */
    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            return 'ok';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * Check storage writable.
     *
     * @return string 'ok' | 'error'
     */
    private function checkStorage(): string
    {
        try {
            $testFile = 'health_check_' . time() . '.tmp';
            Storage::disk('local')->put($testFile, 'ok');
            Storage::disk('local')->delete($testFile);
            return 'ok';
        } catch (\Throwable $e) {
            return 'error';
        }
    }

    /**
     * Check queue depth (warns if jobs table has > 100 pending jobs).
     *
     * @return string 'ok' | 'degraded'
     */
    private function checkQueue(): string
    {
        try {
            if (config('queue.default') === 'database') {
                $pending = DB::table('jobs')->count();
                $failed  = DB::table('failed_jobs')->count();
                if ($pending > 100 || $failed > 10) {
                    return 'degraded';
                }
            }
            return 'ok';
        } catch (\Throwable $e) {
            return 'degraded';
        }
    }
}
