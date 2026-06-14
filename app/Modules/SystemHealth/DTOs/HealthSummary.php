<?php

namespace App\Modules\SystemHealth\DTOs;

use App\Modules\SystemHealth\Enums\HealthStatus;
use DateTimeImmutable;

readonly class HealthSummary
{
    /**
     * @param  list<HealthCheckResult>  $results
     */
    public function __construct(
        public HealthStatus $status,
        public array $results,
        public DateTimeImmutable $checkedAt,
    ) {}

    public function hasProductionBlocker(): bool
    {
        foreach ($this->results as $result) {
            if ($result->productionBlocking()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function toInternalArray(): array
    {
        return [
            'status' => $this->status->value,
            'checked_at' => $this->checkedAt->format(DATE_ATOM),
            'production_blocking' => $this->hasProductionBlocker(),
            'checks' => array_map(
                fn (HealthCheckResult $result): array => $result->toSafeArray(),
                $this->results,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'status' => $this->status->value,
        ];
    }
}
