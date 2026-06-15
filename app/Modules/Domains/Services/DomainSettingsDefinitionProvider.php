<?php

namespace App\Modules\Domains\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class DomainSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'domains.max_list_limit',
                type: SettingType::Integer,
                default: 50,
                validationRules: ['value' => ['required', 'integer', 'min:1', 'max:250']],
                description: 'Maximum bounded domain inventory list size.',
                group: 'domains',
            ),
            new SettingDefinition(
                key: 'domains.allow_idn',
                type: SettingType::Boolean,
                default: true,
                validationRules: ['value' => ['required', 'boolean']],
                description: 'Whether domain inventory accepts IDN input when PHP intl normalization is available.',
                group: 'domains',
            ),
        ];
    }
}
