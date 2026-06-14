<?php

namespace App\Modules\Localization\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class LocalizationSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'localization.default_locale',
                type: SettingType::String,
                default: 'en',
                validationRules: ['value' => ['required', 'string', 'max:35']],
                description: 'System default locale code.',
                group: 'localization',
            ),
            new SettingDefinition(
                key: 'localization.fallback_locale',
                type: SettingType::String,
                default: 'en',
                validationRules: ['value' => ['required', 'string', 'max:35']],
                description: 'Fallback locale code for missing translations.',
                group: 'localization',
            ),
            new SettingDefinition(
                key: 'localization.cookie_lifetime_seconds',
                type: SettingType::DurationSeconds,
                default: 31536000,
                validationRules: ['value' => ['required', 'integer', 'min:3600', 'max:63072000']],
                description: 'Locale cookie lifetime in seconds.',
                group: 'localization',
            ),
            new SettingDefinition(
                key: 'localization.default_locale_prefix',
                type: SettingType::Boolean,
                default: false,
                validationRules: ['value' => ['required', 'boolean']],
                description: 'Whether default locale URLs should include a locale prefix.',
                group: 'localization',
            ),
        ];
    }
}
