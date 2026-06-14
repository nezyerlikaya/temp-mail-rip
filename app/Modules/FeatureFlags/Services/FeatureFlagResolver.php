<?php

namespace App\Modules\FeatureFlags\Services;

use App\Modules\FeatureFlags\DTOs\FeatureFlagDefinition;
use App\Modules\FeatureFlags\DTOs\FeatureFlagResult;
use App\Modules\FeatureFlags\Enums\FeatureFlagState;
use App\Modules\Settings\Exceptions\SettingException;
use App\Modules\Settings\Services\SettingsResolver;
use Illuminate\Support\Facades\Cache;
use Throwable;

class FeatureFlagResolver
{
    public function __construct(
        private readonly FeatureFlagRegistry $registry,
        private readonly SettingsResolver $settings,
        private readonly RolloutEvaluator $rollout,
    ) {}

    public function available(string $key, ?string $rolloutSubjectKey = null): bool
    {
        return $this->resolve($key, $rolloutSubjectKey)->available;
    }

    public function resolve(string $key, ?string $rolloutSubjectKey = null): FeatureFlagResult
    {
        $definition = $this->registry->get($key);

        if ($rolloutSubjectKey !== null) {
            try {
                return $this->resolveUncached($definition, $rolloutSubjectKey);
            } catch (Throwable) {
                return $this->safeFailure($definition);
            }
        }

        $cacheKey = $this->cacheKey($definition, $rolloutSubjectKey);

        try {
            return Cache::remember($cacheKey, now()->addMinutes(2), fn (): FeatureFlagResult => $this->resolveUncached($definition, $rolloutSubjectKey));
        } catch (Throwable) {
            return $this->safeFailure($definition);
        }
    }

    public function forget(string $key): void
    {
        $definition = $this->registry->get($key);

        Cache::forget($this->cacheKey($definition, null));
    }

    public function setState(string $key, FeatureFlagState $state): FeatureFlagResult
    {
        $definition = $this->registry->get($key);

        if ($definition->stateSettingKey !== null) {
            $this->settings->put($definition->stateSettingKey, $state->value);
        }

        $this->forget($key);

        return $this->resolve($key);
    }

    public function setRolloutPercentage(string $key, int $percentage): void
    {
        $definition = $this->registry->get($key);

        if ($percentage < 0 || $percentage > 100) {
            throw new \InvalidArgumentException('Rollout percentage must be between 0 and 100.');
        }

        if ($definition->rolloutSettingKey !== null) {
            $this->settings->put($definition->rolloutSettingKey, $percentage);
        }

        $this->forget($key);
    }

    private function resolveUncached(FeatureFlagDefinition $definition, ?string $rolloutSubjectKey): FeatureFlagResult
    {
        $state = $this->state($definition);
        $rolloutPercentage = $this->rolloutPercentage($definition);

        if ($definition->isKillSwitch) {
            $active = $state === FeatureFlagState::Enabled;

            return new FeatureFlagResult(
                key: $definition->key,
                state: $state,
                available: ! $active,
                killSwitchActive: $active,
                rolloutPercentage: null,
            );
        }

        $available = match ($state) {
            FeatureFlagState::Enabled => true,
            FeatureFlagState::Disabled, FeatureFlagState::Deprecated => false,
            FeatureFlagState::Beta => $rolloutSubjectKey !== null
                && $rolloutPercentage !== null
                && $this->rollout->included($definition, $rolloutSubjectKey, $rolloutPercentage),
        };

        return new FeatureFlagResult(
            key: $definition->key,
            state: $state,
            available: $available,
            rolloutPercentage: $rolloutPercentage,
        );
    }

    private function state(FeatureFlagDefinition $definition): FeatureFlagState
    {
        if ($definition->stateSettingKey === null) {
            return $definition->defaultState;
        }

        try {
            $value = $this->settings->get($definition->stateSettingKey);
        } catch (SettingException) {
            return $definition->defaultState;
        }

        return FeatureFlagState::tryFrom((string) $value) ?? $definition->defaultState;
    }

    private function rolloutPercentage(FeatureFlagDefinition $definition): ?int
    {
        if ($definition->rolloutSettingKey === null) {
            return $definition->defaultRolloutPercentage;
        }

        try {
            $value = $this->settings->get($definition->rolloutSettingKey);
        } catch (SettingException) {
            return $definition->defaultRolloutPercentage;
        }

        return is_int($value) && $value >= 0 && $value <= 100
            ? $value
            : $definition->defaultRolloutPercentage;
    }

    private function safeFailure(FeatureFlagDefinition $definition): FeatureFlagResult
    {
        return new FeatureFlagResult(
            key: $definition->key,
            state: $definition->defaultState,
            available: ! $definition->failClosed,
            killSwitchActive: false,
            rolloutPercentage: $definition->defaultRolloutPercentage,
        );
    }

    private function cacheKey(FeatureFlagDefinition $definition, ?string $rolloutSubjectKey): string
    {
        $subjectHash = $rolloutSubjectKey === null ? 'none' : hash('sha256', $definition->key.'|'.$rolloutSubjectKey);

        return 'feature_flags.resolved.'.$definition->key.'.'.$subjectHash;
    }
}
