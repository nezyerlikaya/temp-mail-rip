<?php

namespace App\Modules\SystemHealth\Events;

readonly class HealthConfigurationChanged
{
    public function __construct(
        public string $settingKey,
    ) {}
}
