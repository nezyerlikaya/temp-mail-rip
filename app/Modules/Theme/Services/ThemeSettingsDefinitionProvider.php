<?php

namespace App\Modules\Theme\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class ThemeSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'theme.default',
                type: SettingType::String,
                default: 'system',
                validationRules: ['value' => ['required', 'string', 'in:light,dark,system']],
                description: 'Application default theme preference.',
                group: 'theme',
            ),
        ];
    }
}
