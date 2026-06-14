<?php

namespace App\Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminShellAccessible
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Admin shell requires authentication and authorization foundations before production use.');
        }

        return $next($request);
    }
}
