<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    protected function normalizeRoles(array $roles): array
    {
        $normalized = [];

        foreach ($roles as $roleGroup) {
            foreach (preg_split('/[|,]/', (string) $roleGroup) as $role) {
                $role = trim($role);

                if ($role !== '') {
                    $normalized[] = $role;
                }
            }
        }

        return $normalized;
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowedRoles = $this->normalizeRoles($roles);

        if (!in_array($user->role, $allowedRoles, true)) {
            \App\Services\AuditService::log(
                'PRIVILEGE_ESCALATION_ATTEMPT',
                'User',
                $user->id,
                "User attempted to access role-restricted resource [{$request->method()} {$request->path()}] without role(s): " . implode(',', $allowedRoles)
            );
            abort(403, 'Unauthorized. You do not have the required role to access this resource.');
        }

        return $next($request);
    }
}
