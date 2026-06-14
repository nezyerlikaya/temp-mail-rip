<?php

namespace App\Modules\FeatureFlags\DTOs;

use App\Modules\FeatureFlags\Enums\FeatureFlagState;

readonly class FeatureFlagResult
{
    public function __construct(
        public string $key,
        public FeatureFlagState $state,
        public bool $available,
        public bool $killSwitchActive = false,
        public ?int $rolloutPercentage = null,
    ) {}
}
