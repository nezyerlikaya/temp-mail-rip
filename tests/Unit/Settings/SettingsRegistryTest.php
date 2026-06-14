<?php

namespace Tests\Unit\Settings;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Exceptions\DuplicateSettingException;
use App\Modules\Settings\Services\SettingsRegistry;
use PHPUnit\Framework\TestCase;

class SettingsRegistryTest extends TestCase
{
    public function test_duplicate_key_registration_is_rejected(): void
    {
        $registry = new SettingsRegistry;
        $definition = new SettingDefinition(
            key: 'platform.example',
            type: SettingType::String,
            default: 'value',
            validationRules: ['value' => ['required', 'string']],
        );

        $registry->register($definition);

        $this->expectException(DuplicateSettingException::class);

        $registry->register($definition);
    }

    public function test_sensitive_settings_cannot_be_public(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new SettingDefinition(
            key: 'security.secret_note',
            type: SettingType::String,
            default: 'secret',
            validationRules: ['value' => ['required', 'string']],
            isSensitive: true,
            isPublic: true,
        );
    }
}
