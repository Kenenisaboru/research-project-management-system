<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            \App\Services\AuditService::log(
                'PRIVILEGE_ESCALATION_ATTEMPT',
                'User',
                $user->id,
                "User attempted to access role-restricted resource [{$request->method()} {$request->path()}] without role(s): " . implode(',', $roles)
            );
            abort(403, 'Unauthorized. You do not have the required role to access this resource.');
        }

        return $next($request);
    }
}
