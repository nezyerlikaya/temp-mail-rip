<?php

namespace App\Modules\FeatureFlags\Services;

use App\Modules\FeatureFlags\DTOs\FeatureFlagDefinition;
use App\Modules\FeatureFlags\Enums\FeatureFlagState;

class FeatureFlagDefinitionProvider
{
    /**
     * @return list<FeatureFlagDefinition>
     */
    public function definitions(): array
    {
        return [
            new FeatureFlagDefinition(
                key: 'platform.public_app',
                description: 'Operational availability for the existing public application shell.',
                ownerModule: 'FeatureFlags',
                defaultState: FeatureFlagState::Enabled,
                failClosed: false,
                stateSettingKey: 'featureflags.platform_public_app.state',
            ),
            new FeatureFlagDefinition(
                key: 'platform.beta_features',
                description: 'Controlled beta availability for future explicitly opted platform surfaces.',
                ownerModule: 'FeatureFlags',
                defaultState: FeatureFlagState::Disabled,
                failClosed: true,
                defaultRolloutPercentage: 0,
                stateSettingKey: 'featureflags.platform_beta_features.state',
                rolloutSettingKey: 'featureflags.platform_beta_features.rollout_percentage',
                rolloutSalt: 'platform-beta-features-v1',
            ),
            new FeatureFlagDefinition(
                key: 'operations.optional_modules_kill_switch',
                description: 'Emergency kill switch for bounded optional modules only.',
                ownerModule: 'FeatureFlags',
                defaultState: FeatureFlagState::Disabled,
                isKillSwitch: true,
                failClosed: true,
                stateSettingKey: 'featureflags.operations_optional_modules_kill_switch.state',
            ),
        ];
    }
}
