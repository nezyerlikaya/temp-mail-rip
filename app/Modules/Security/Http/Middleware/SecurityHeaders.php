<?php

namespace App\Modules\Security\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->setIfMissing($response, 'X-Content-Type-Options', (string) config('security.headers.content_type_options', 'nosniff'));
        $this->setIfMissing($response, 'Referrer-Policy', (string) config('security.headers.referrer_policy', 'strict-origin-when-cross-origin'));
        $this->setIfMissing($response, 'X-Frame-Options', (string) config('security.headers.frame_options', 'SAMEORIGIN'));
        $this->setIfMissing($response, 'Permissions-Policy', (string) config('security.headers.permissions_policy', 'camera=(), microphone=(), geolocation=(), payment=()'));

        if ($this->shouldSendHsts($request)) {
            $this->setIfMissing($response, 'Strict-Transport-Security', $this->hstsHeader());
        }

        return $response;
    }

    private function setIfMissing(Response $response, string $header, string $value): void
    {
        if ($value !== '' && ! $response->headers->has($header)) {
            $response->headers->set($header, $value);
        }
    }

    private function shouldSendHsts(Request $request): bool
    {
        return $request->isSecure()
            && app()->environment('production')
            && (bool) config('security.headers.hsts.enabled', false);
    }

    private function hstsHeader(): string
    {
        $value = 'max-age='.(int) config('security.headers.hsts.max_age', 31536000);

        if ((bool) config('security.headers.hsts.include_subdomains', true)) {
            $value .= '; includeSubDomains';
        }

        if ((bool) config('security.headers.hsts.preload', false)) {
            $value .= '; preload';
        }

        return $value;
    }
}
