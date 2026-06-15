<?php

namespace App\Modules\Installer\Http\Middleware;

use App\Modules\Installer\Services\InstallationLock;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isInstallerRequest($request)) {
            if (! app(InstallationLock::class)->locked()) {
                config(['session.driver' => 'file']);
            }

            return $next($request);
        }

        if (app(InstallationLock::class)->locked()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Installation is required before the application can be used.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return redirect()->route('installer.preflight');
    }

    private function isInstallerRequest(Request $request): bool
    {
        return $request->is('install') || $request->is('install/*');
    }
}
