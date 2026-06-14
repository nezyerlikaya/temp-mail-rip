<?php

namespace Tests\Feature\Localization;

use App\Modules\Localization\DTOs\LocaleDefinition;
use App\Modules\Localization\Enums\LocaleStatus;
use App\Modules\Localization\Enums\TextDirection;
use App\Modules\Localization\Exceptions\InvalidLocaleException;
use App\Modules\Localization\Services\LocaleNormalizer;
use App\Modules\Localization\Services\LocaleRegistry;
use App\Modules\Localization\Services\LocaleResolver;
use Tests\TestCase;

class LocaleResolverTest extends TestCase
{
    public function test_resolution_priority_is_deterministic(): void
    {
        $resolver = app(LocaleResolver::class);

        $this->assertSame('tr', $resolver->resolve(routeLocale: 'tr', userPreference: 'de', cookieLocale: 'fr', acceptLanguage: 'es')->code);
        $this->assertSame('de', $resolver->resolve(routeLocale: null, userPreference: 'de', cookieLocale: 'fr', acceptLanguage: 'es')->code);
        $this->assertSame('fr', $resolver->resolve(routeLocale: '../bad', userPreference: null, cookieLocale: 'fr', acceptLanguage: 'es')->code);
        $this->assertSame('es', $resolver->resolve(routeLocale: null, userPreference: null, cookieLocale: null, acceptLanguage: 'es-MX,fr;q=0.8')->code);
        $this->assertSame('en', $resolver->resolve(routeLocale: null, userPreference: null, cookieLocale: null, acceptLanguage: 'zz')->code);
    }

    public function test_disabled_and_deprecated_locales_are_not_resolved_for_new_requests(): void
    {
        $registry = new LocaleRegistry(new LocaleNormalizer);
        $registry->register(new LocaleDefinition('en', 'English', 'English', TextDirection::Ltr, LocaleStatus::Active, isDefault: true));
        $registry->register(new LocaleDefinition('fr', 'French', 'Français', TextDirection::Ltr, LocaleStatus::Hidden));
        $registry->register(new LocaleDefinition('de', 'German', 'Deutsch', TextDirection::Ltr, LocaleStatus::Disabled));
        $registry->register(new LocaleDefinition('es', 'Spanish', 'Español', TextDirection::Ltr, LocaleStatus::Deprecated, fallbackLocale: 'en'));
        $resolver = new LocaleResolver($registry, new LocaleNormalizer);

        $this->assertSame('fr', $resolver->resolve(routeLocale: 'fr')->code);
        $this->assertSame('en', $resolver->resolve(routeLocale: 'de')->code);
        $this->assertSame('en', $resolver->resolve(routeLocale: 'es')->code);
        $this->assertSame('en', $registry->fallbackFor('es')->code);
    }

    public function test_route_locale_validation_uses_registry(): void
    {
        $resolver = app(LocaleResolver::class);

        $this->assertSame('tr', $resolver->validateRouteLocale('TR'));

        $this->expectException(InvalidLocaleException::class);

        $resolver->validateRouteLocale('zz');
    }
}
