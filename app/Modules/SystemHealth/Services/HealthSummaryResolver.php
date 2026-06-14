<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\SystemHealth\DTOs\HealthCheckResult;
use App\Modules\SystemHealth\DTOs\HealthSummary;
use App\Modules\SystemHealth\Enums\HealthStatus;
use DateTimeImmutable;

class HealthSummaryResolver
{
    public function __construct(
        private readonly HealthCheckRunner $runner,
    ) {}

    public function summarize(): HealthSummary
    {
        $results = $this->runner->runAll();

        return new HealthSummary(
            status: $this->aggregateStatus($results),
            results: $results,
            checkedAt: new DateTimeImmutable,
        );
    }

    /**
     * @param  list<HealthCheckResult>  $results
     */
    public function aggregateStatus(array $results): HealthStatus
    {
        $status = HealthStatus::Healthy;

        foreach ($results as $result) {
            if ($result->status->severity() > $status->severity()) {
                $status = $result->status;
            }
        }

        return $status;
    }
}
