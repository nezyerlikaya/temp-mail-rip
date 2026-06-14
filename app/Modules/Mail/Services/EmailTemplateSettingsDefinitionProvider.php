<?php

namespace App\Modules\Mail\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class EmailTemplateSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'mail.template_fallback_mode',
                type: SettingType::String,
                default: 'default_locale',
                validationRules: ['value' => ['required', 'string', 'in:default_locale,none']],
                description: 'Controls whether email template preparation may explicitly fall back to the default locale.',
                group: 'mail',
            ),
            new SettingDefinition(
                key: 'mail.support_display_name',
                type: SettingType::String,
                default: 'Temp Mail Support',
                validationRules: ['value' => ['required', 'string', 'min:1', 'max:120']],
                isPublic: true,
                description: 'Public-safe sender display name. SMTP credentials remain outside Settings.',
                group: 'mail',
            ),
        ];
    }
}
