<?php

namespace App\Modules\Security\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class SecurityExceptionMapper
{
    public function toResponse(Throwable $exception, Request $request): ?Response
    {
        if ((bool) config('app.debug')) {
            return null;
        }

        if (
            $exception instanceof ValidationException
            || $exception instanceof AuthenticationException
            || $exception instanceof AuthorizationException
        ) {
            return null;
        }

        $status = $this->statusCode($exception);
        $message = $this->publicMessage($status);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response([
                'message' => $message,
            ], $status);
        }

        return response($message, $status);
    }

    private function statusCode(Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();

            if ($status >= 400 && $status < 600) {
                return $status;
            }
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function publicMessage(int $status): string
    {
        return match ($status) {
            Response::HTTP_NOT_FOUND => 'Not Found',
            Response::HTTP_FORBIDDEN => 'Forbidden',
            Response::HTTP_UNAUTHORIZED => 'Unauthenticated',
            Response::HTTP_TOO_MANY_REQUESTS => 'Too Many Requests',
            default => 'Server Error',
        };
    }
}
