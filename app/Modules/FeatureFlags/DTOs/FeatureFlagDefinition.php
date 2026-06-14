<?php

namespace App\Modules\FeatureFlags\DTOs;

use App\Modules\FeatureFlags\Enums\FeatureFlagState;
use InvalidArgumentException;

readonly class FeatureFlagDefinition
{
    public function __construct(
        public string $key,
        public string $description,
        public string $ownerModule,
        public FeatureFlagState $defaultState,
        public bool $isKillSwitch = false,
        public bool $failClosed = true,
        public ?int $defaultRolloutPercentage = null,
        public ?string $stateSettingKey = null,
        public ?string $rolloutSettingKey = null,
        public string $rolloutSalt = 'temp-mail-v1',
    ) {
        if ($this->key === '' || ! preg_match('/^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9_]*)+$/', $this->key)) {
            throw new InvalidArgumentException('Feature flag keys must use lowercase module-owned dot notation.');
        }

        if ($this->description === '') {
            throw new InvalidArgumentException('Feature flags require a description.');
        }

        if ($this->defaultRolloutPercentage !== null && ($this->defaultRolloutPercentage < 0 || $this->defaultRolloutPercentage > 100)) {
            throw new InvalidArgumentException('Rollout percentage must be between 0 and 100.');
        }

        if ($this->isKillSwitch && $this->defaultState === FeatureFlagState::Enabled) {
            throw new InvalidArgumentException('Kill switches must not default to enabled.');
        }
    }
}
