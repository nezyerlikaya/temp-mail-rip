<?php

namespace App\Modules\Installer\Http\Middleware;

use App\Modules\Installer\Services\InstallationLock;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockLockedInstaller
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app(InstallationLock::class)->locked()) {
            return response(view('installer.locked'), Response::HTTP_LOCKED);
        }

        return $next($request);
    }
}
