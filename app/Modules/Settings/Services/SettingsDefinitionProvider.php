<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class SettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'platform.display_name',
                type: SettingType::String,
                default: 'Temp Mail',
                validationRules: ['value' => ['required', 'string', 'min:1', 'max:80']],
                isPublic: true,
                description: 'Public product display name.',
                group: 'platform',
            ),
            new SettingDefinition(
                key: 'platform.default_page_size',
                type: SettingType::Integer,
                default: 25,
                validationRules: ['value' => ['required', 'integer', 'min:1', 'max:100']],
                description: 'Default bounded page size for internal lists.',
                group: 'platform',
            ),
            new SettingDefinition(
                key: 'platform.show_branding',
                type: SettingType::Boolean,
                default: true,
                validationRules: ['value' => ['required', 'boolean']],
                isPublic: true,
                description: 'Whether future public Blade surfaces may show product branding.',
                group: 'platform',
            ),
            new SettingDefinition(
                key: 'platform.public_metadata',
                type: SettingType::Array,
                default: ['tagline' => 'Temporary email, carefully bounded.'],
                validationRules: [
                    'value' => ['required', 'array', 'max:5'],
                    'value.tagline' => ['sometimes', 'string', 'max:120'],
                ],
                isPublic: true,
                description: 'Small public-safe metadata for future Blade consumers.',
                group: 'platform',
            ),
            new SettingDefinition(
                key: 'security.diagnostics_retention_seconds',
                type: SettingType::DurationSeconds,
                default: 604800,
                validationRules: ['value' => ['required', 'integer', 'min:3600', 'max:2592000']],
                description: 'Duration in seconds for future sanitized diagnostic retention policy.',
                group: 'security',
            ),
            new SettingDefinition(
                key: 'security.operator_notice',
                type: SettingType::String,
                default: null,
                validationRules: ['value' => ['nullable', 'string', 'max:240']],
                isSensitive: true,
                nullable: true,
                description: 'Sensitive operator-only note; never exposed publicly.',
                group: 'security',
            ),
        ];
    }
}
