<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $resolvedPermissions = [];
        foreach ($permissions as $perm) {
            foreach (explode('|', $perm) as $p) {
                $resolvedPermissions[] = trim($p);
            }
        }

        if (!RbacService::userHasAnyPermission($user, $resolvedPermissions)) {
            \App\Services\AuditService::log(
                'PRIVILEGE_ESCALATION_ATTEMPT',
                'User',
                $user->id,
                "User attempted to access resource [{$request->method()} {$request->path()}] without permission(s): " . implode(',', $resolvedPermissions)
            );
            abort(403, 'Unauthorized. You do not have the required permission to access this resource.');
        }

        return $next($request);
    }
}
