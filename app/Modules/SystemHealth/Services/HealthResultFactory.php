<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\SystemHealth\DTOs\HealthCheckResult;
use App\Modules\SystemHealth\Enums\HealthStatus;
use DateTimeImmutable;
use Throwable;

class HealthResultFactory
{
    public function __construct(
        private readonly SafeDiagnosticsFormatter $diagnostics,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function result(
        string $key,
        HealthStatus $status,
        string $message,
        int $durationMs = 0,
        array $context = [],
        bool $blocksProduction = true,
    ): HealthCheckResult {
        return new HealthCheckResult(
            key: $key,
            status: $status,
            message: $this->safeMessage($message),
            durationMs: $durationMs,
            checkedAt: new DateTimeImmutable,
            context: $this->diagnostics->format($context),
            blocksProduction: $blocksProduction,
        );
    }

    public function exception(string $key, Throwable $exception, int $durationMs = 0, bool $blocksProduction = true): HealthCheckResult
    {
        return $this->result(
            key: $key,
            status: HealthStatus::Degraded,
            message: 'Health check failed safely.',
            durationMs: $durationMs,
            context: [
                'exception' => str_replace('\\', '.', $exception::class),
            ],
            blocksProduction: $blocksProduction,
        );
    }

    private function safeMessage(string $message): string
    {
        $formatted = $this->diagnostics->format(['message' => $message]);

        return (string) ($formatted['message'] ?? 'Health status unavailable.');
    }
}
