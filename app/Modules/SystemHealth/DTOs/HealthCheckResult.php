<?php

namespace App\Modules\SystemHealth\DTOs;

use App\Modules\SystemHealth\Enums\HealthStatus;
use DateTimeImmutable;
use InvalidArgumentException;

readonly class HealthCheckResult
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $key,
        public HealthStatus $status,
        public string $message,
        public int $durationMs,
        public DateTimeImmutable $checkedAt,
        public array $context = [],
        public bool $blocksProduction = true,
    ) {
        if ($this->durationMs < 0 || $this->durationMs > 60000) {
            throw new InvalidArgumentException('Health check durations must be bounded.');
        }

        if ($this->message === '' || mb_strlen($this->message) > 240) {
            throw new InvalidArgumentException('Health check messages must be non-empty and bounded.');
        }
    }

    public function productionBlocking(): bool
    {
        return $this->blocksProduction && $this->status->blocksProductionReadiness();
    }

    /**
     * @return array<string, mixed>
     */
    public function toSafeArray(): array
    {
        return [
            'key' => $this->key,
            'status' => $this->status->value,
            'message' => $this->message,
            'duration_ms' => $this->durationMs,
            'checked_at' => $this->checkedAt->format(DATE_ATOM),
            'context' => $this->context,
            'production_blocking' => $this->productionBlocking(),
        ];
    }
}
