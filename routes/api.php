<?php

use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — RPMS Version 1
|--------------------------------------------------------------------------
|
| Stateless REST API endpoints secured with Laravel Sanctum token auth
| and granular rate limiting (60 req/min auth, 10 req/min guest).
|
*/

Route::prefix('v1')->group(function () {

    // Authentication (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('api.v1.auth.login');
    });

    // Protected API Endpoints (Requires valid Sanctum Bearer Token)
    Route::middleware(['auth:sanctum'])->group(function () {

        // Session & Token Management
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
            Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
        });

        // Research Projects
        Route::apiResource('projects', ProjectController::class)
            ->only(['index', 'show', 'store', 'update'])
            ->names([
                'index'  => 'api.v1.projects.index',
                'show'   => 'api.v1.projects.show',
                'store'  => 'api.v1.projects.store',
                'update' => 'api.v1.projects.update',
            ]);

        // Real-Time Dashboard Stats
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('api.v1.dashboard.stats');

        // In-App Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('api.v1.notifications.index');
            Route::get('/poll', [NotificationController::class, 'poll'])->name('api.v1.notifications.poll');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('api.v1.notifications.unread-count');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.v1.notifications.read');
            Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('api.v1.notifications.read-all');
        });

        // Institutional Research Analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('api.v1.analytics.index');
    });
});
