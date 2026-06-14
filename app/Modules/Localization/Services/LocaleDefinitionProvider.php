<?php

namespace App\Modules\Localization\Services;

use App\Modules\Localization\DTOs\LocaleDefinition;
use App\Modules\Localization\Enums\LocaleStatus;
use App\Modules\Localization\Enums\TextDirection;

class LocaleDefinitionProvider
{
    /**
     * @return list<LocaleDefinition>
     */
    public function definitions(): array
    {
        return [
            new LocaleDefinition('en', 'English', 'English', TextDirection::Ltr, LocaleStatus::Active, isDefault: true),
            new LocaleDefinition('tr', 'Turkish', 'Türkçe', TextDirection::Ltr, LocaleStatus::Active),
            new LocaleDefinition('de', 'German', 'Deutsch', TextDirection::Ltr, LocaleStatus::Active),
            new LocaleDefinition('fr', 'French', 'Français', TextDirection::Ltr, LocaleStatus::Active),
            new LocaleDefinition('es', 'Spanish', 'Español', TextDirection::Ltr, LocaleStatus::Active),
        ];
    }
}
