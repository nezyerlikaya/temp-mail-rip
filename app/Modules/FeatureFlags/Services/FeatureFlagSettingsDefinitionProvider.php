<?php

namespace App\Modules\FeatureFlags\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class FeatureFlagSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        $stateRules = ['value' => ['required', 'string', 'in:enabled,disabled,beta,deprecated']];

        return [
            new SettingDefinition(
                key: 'featureflags.platform_public_app.state',
                type: SettingType::String,
                default: 'enabled',
                validationRules: $stateRules,
                description: 'Runtime operational state override for platform.public_app.',
                group: 'feature_flags',
            ),
            new SettingDefinition(
                key: 'featureflags.platform_beta_features.state',
                type: SettingType::String,
                default: 'disabled',
                validationRules: $stateRules,
                description: 'Runtime operational state override for platform.beta_features.',
                group: 'feature_flags',
            ),
            new SettingDefinition(
                key: 'featureflags.platform_beta_features.rollout_percentage',
                type: SettingType::Integer,
                default: 0,
                validationRules: ['value' => ['required', 'integer', 'min:0', 'max:100']],
                description: 'Validated deterministic rollout percentage for platform.beta_features.',
                group: 'feature_flags',
            ),
            new SettingDefinition(
                key: 'featureflags.operations_optional_modules_kill_switch.state',
                type: SettingType::String,
                default: 'disabled',
                validationRules: $stateRules,
                description: 'Runtime operational state override for operations.optional_modules_kill_switch.',
                group: 'feature_flags',
            ),
        ];
    }
}
