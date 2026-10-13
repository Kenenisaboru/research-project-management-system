<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountLocked
 *
 * Verifies active account standing on authenticated requests, terminating suspended sessions.
 */
class AccountLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $inactiveStatuses = ['inactive', 'suspended', 'locked', 'deactivated'];
        if ($user && in_array(strtolower((string) $user->status), $inactiveStatuses, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }
                return response()->json([
                    'success' => false,
                    'message' => __('Your account has been deactivated. Please contact the RPMS administrator.'),
                    'data'    => null,
                    'errors'  => null,
                ], 403);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => __('Your account has been deactivated. Please contact the RPMS administrator.'),
            ]);
        }

        return $next($request);
    }
}
