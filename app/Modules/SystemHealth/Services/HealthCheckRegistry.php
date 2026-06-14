<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\SystemHealth\DTOs\HealthCheckDefinition;
use App\Modules\SystemHealth\Exceptions\DuplicateHealthCheckException;
use App\Modules\SystemHealth\Exceptions\UnknownHealthCheckException;

class HealthCheckRegistry
{
    /**
     * @var array<string, HealthCheckDefinition>
     */
    private array $checks = [];

    public function register(HealthCheckDefinition $check): void
    {
        if (isset($this->checks[$check->key])) {
            throw DuplicateHealthCheckException::forKey($check->key);
        }

        $this->checks[$check->key] = $check;
    }

    public function get(string $key): HealthCheckDefinition
    {
        return $this->checks[$key] ?? throw UnknownHealthCheckException::forKey($key);
    }

    /**
     * @return list<HealthCheckDefinition>
     */
    public function all(): array
    {
        ksort($this->checks);

        return array_values($this->checks);
    }
}
