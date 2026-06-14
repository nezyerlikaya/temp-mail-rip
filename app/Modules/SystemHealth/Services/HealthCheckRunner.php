<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\SystemHealth\DTOs\HealthCheckDefinition;
use App\Modules\SystemHealth\DTOs\HealthCheckResult;
use App\Modules\SystemHealth\Enums\HealthStatus;
use App\Modules\SystemHealth\Exceptions\UnknownHealthCheckException;
use Throwable;

class HealthCheckRunner
{
    public function __construct(
        private readonly HealthCheckRegistry $registry,
        private readonly HealthResultFactory $results,
    ) {}

    /**
     * @return list<HealthCheckResult>
     */
    public function runAll(): array
    {
        return array_map(
            fn (HealthCheckDefinition $definition): HealthCheckResult => $this->runDefinition($definition),
            $this->registry->all(),
        );
    }

    public function run(string $key): HealthCheckResult
    {
        return $this->runDefinition($this->registry->get($key));
    }

    private function runDefinition(HealthCheckDefinition $definition): HealthCheckResult
    {
        $start = microtime(true);

        try {
            $result = ($definition->callback)();
        } catch (UnknownHealthCheckException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            return $this->results->exception($definition->key, $exception, $this->durationMs($start), $definition->blocksProduction);
        }

        $duration = $this->durationMs($start);
        $status = $result->status;

        if ($duration > $definition->timeoutMs && $status === HealthStatus::Healthy) {
            $status = HealthStatus::Warning;
        }

        return new HealthCheckResult(
            key: $result->key,
            status: $status,
            message: $status === HealthStatus::Warning && $result->status === HealthStatus::Healthy
                ? 'Health check completed but exceeded its expected duration.'
                : $result->message,
            durationMs: $duration,
            checkedAt: $result->checkedAt,
            context: $result->context,
            blocksProduction: $definition->blocksProduction && $result->blocksProduction,
        );
    }

    private function durationMs(float $start): int
    {
        return max(0, (int) round((microtime(true) - $start) * 1000));
    }
}
