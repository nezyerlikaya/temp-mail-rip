<?php

namespace App\Modules\SystemHealth\Events;

use App\Modules\SystemHealth\Enums\HealthStatus;

readonly class CriticalHealthStateDetected
{
    /**
     * @param  list<string>  $blockingChecks
     */
    public function __construct(
        public HealthStatus $status,
        public array $blockingChecks,
    ) {}
}
