<?php

namespace App\Modules\SystemHealth\DTOs;

use Closure;
use InvalidArgumentException;

readonly class HealthCheckDefinition
{
    public function __construct(
        public string $key,
        public string $label,
        public Closure $callback,
        public int $timeoutMs = 1000,
        public bool $blocksProduction = true,
    ) {
        if (! preg_match('/^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9_]*)+$/', $this->key)) {
            throw new InvalidArgumentException('Health check keys must use lowercase dot notation.');
        }

        if ($this->label === '' || mb_strlen($this->label) > 120) {
            throw new InvalidArgumentException('Health check labels must be bounded.');
        }

        if ($this->timeoutMs < 50 || $this->timeoutMs > 10000) {
            throw new InvalidArgumentException('Health check timeout expectations must be bounded.');
        }
    }
}
