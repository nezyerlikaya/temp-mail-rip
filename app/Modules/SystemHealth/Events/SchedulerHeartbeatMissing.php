<?php

namespace App\Modules\SystemHealth\Events;

readonly class SchedulerHeartbeatMissing
{
    public function __construct(
        public int $maxAgeSeconds,
    ) {}
}
