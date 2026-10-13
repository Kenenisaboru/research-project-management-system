<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ForceHttps
 *
 * Redirects insecure HTTP requests to HTTPS in production environments.
 */
class ForceHttps
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
        if (app()->environment('production') && !$request->secure() && !$request->is('health*')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
