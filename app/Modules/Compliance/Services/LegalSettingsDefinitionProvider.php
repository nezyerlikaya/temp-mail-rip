<?php

namespace App\Modules\Compliance\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class LegalSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'legal.fallback_mode',
                type: SettingType::String,
                default: 'default_locale',
                validationRules: ['value' => ['required', 'string', 'in:default_locale,none']],
                description: 'Controls whether public legal pages may explicitly fall back to the default locale.',
                group: 'legal',
            ),
            new SettingDefinition(
                key: 'legal.company_display_name',
                type: SettingType::String,
                default: 'Temp Mail',
                validationRules: ['value' => ['required', 'string', 'min:1', 'max:120']],
                isPublic: true,
                description: 'Public-safe company display name for legal page presentation.',
                group: 'legal',
            ),
        ];
    }
}
