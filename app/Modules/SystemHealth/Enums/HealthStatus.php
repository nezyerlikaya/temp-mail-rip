<?php

namespace App\Modules\SystemHealth\Enums;

enum HealthStatus: string
{
    case Healthy = 'healthy';
    case Warning = 'warning';
    case Degraded = 'degraded';
    case Critical = 'critical';
    case Unknown = 'unknown';

    public function severity(): int
    {
        return match ($this) {
            self::Healthy => 0,
            self::Warning => 1,
            self::Unknown => 2,
            self::Degraded => 3,
            self::Critical => 4,
        };
    }

    public function blocksProductionReadiness(): bool
    {
        return in_array($this, [self::Degraded, self::Critical], true);
    }
}
