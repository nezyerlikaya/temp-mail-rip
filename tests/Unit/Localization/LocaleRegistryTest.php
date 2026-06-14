<?php

namespace Tests\Unit\Localization;

use App\Modules\Localization\DTOs\LocaleDefinition;
use App\Modules\Localization\Enums\LocaleStatus;
use App\Modules\Localization\Enums\TextDirection;
use App\Modules\Localization\Exceptions\DefaultLocaleException;
use App\Modules\Localization\Exceptions\DuplicateLocaleException;
use App\Modules\Localization\Exceptions\InvalidLocaleException;
use App\Modules\Localization\Services\LocaleDefinitionProvider;
use App\Modules\Localization\Services\LocaleNormalizer;
use App\Modules\Localization\Services\LocaleRegistry;
use PHPUnit\Framework\TestCase;

class LocaleRegistryTest extends TestCase
{
    public function test_supported_locales_register_and_default_is_unique(): void
    {
        $registry = new LocaleRegistry(new LocaleNormalizer);

        foreach ((new LocaleDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        $this->assertSame(['en', 'tr', 'de', 'fr', 'es'], array_keys($registry->all()));
        $this->assertSame('en', $registry->default()->code);
    }

    public function test_duplicate_locales_are_rejected(): void
    {
        $registry = new LocaleRegistry(new LocaleNormalizer);
        $definition = new LocaleDefinition('en', 'English', 'English', TextDirection::Ltr, LocaleStatus::Active, isDefault: true);

        $registry->register($definition);

        $this->expectException(DuplicateLocaleException::class);

        $registry->register($definition);
    }

    public function test_exactly_one_default_locale_is_enforced(): void
    {
        $registry = new LocaleRegistry(new LocaleNormalizer);
        $registry->register(new LocaleDefinition('en', 'English', 'English', TextDirection::Ltr, LocaleStatus::Active, isDefault: true));

        $this->expectException(DefaultLocaleException::class);

        $registry->register(new LocaleDefinition('tr', 'Turkish', 'Türkçe', TextDirection::Ltr, LocaleStatus::Active, isDefault: true));
    }

    public function test_normalization_and_invalid_locale_rejection(): void
    {
        $normalizer = new LocaleNormalizer;

        $this->assertSame('en-US', $normalizer->normalize('EN_us'));

        $this->expectException(InvalidLocaleException::class);

        $normalizer->normalize('../en');
    }
}
