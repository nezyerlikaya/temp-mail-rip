<?php

namespace Tests\Feature\Translation;

use App\Modules\Localization\Services\LocaleRegistry;
use App\Modules\Translation\DTOs\TranslationDefinition;
use App\Modules\Translation\Exceptions\DuplicateTranslationKeyException;
use App\Modules\Translation\Exceptions\InvalidTranslationValueException;
use App\Modules\Translation\Services\TranslationNamespaceRegistry;
use App\Modules\Translation\Services\TranslationResolver;
use App\Modules\Translation\Services\TranslationValueProvider;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TranslationResolverTest extends TestCase
{
    public function test_translation_keys_resolve_with_fallback(): void
    {
        $resolver = app(TranslationResolver::class);

        $this->assertSame('Ana sayfa', $resolver->get('navigation.home', 'tr'));
        $this->assertSame('Home', $resolver->get('navigation.home', 'de'));
    }

    public function test_namespace_duplicate_keys_are_rejected(): void
    {
        $registry = new TranslationNamespaceRegistry;
        $definition = new TranslationDefinition('auth', 'login');
        $registry->register($definition);

        $this->expectException(DuplicateTranslationKeyException::class);

        $registry->register($definition);
    }

    public function test_placeholder_validation_and_escaping(): void
    {
        $resolver = app(TranslationResolver::class);

        $result = $resolver->get('mailboxes.create.success', 'en', [
            'address' => '<script>alert(1)</script>',
        ]);

        $this->assertStringContainsString('&lt;script&gt;', $result);
        $this->assertStringNotContainsString('<script>', $result);

        $this->expectException(InvalidTranslationValueException::class);

        $resolver->get('mailboxes.create.success', 'en', []);
    }

    public function test_translation_cache_can_be_invalidated_by_locale_and_namespace(): void
    {
        $resolver = app(TranslationResolver::class);

        $this->assertSame('Home', $resolver->get('navigation.home', 'en'));
        $this->assertTrue(Cache::has('translations.en.navigation'));

        $resolver->forget('en', 'navigation');

        $this->assertFalse(Cache::has('translations.en.navigation'));
    }

    public function test_html_translation_values_are_rejected(): void
    {
        $registry = new TranslationNamespaceRegistry;
        $registry->register(new TranslationDefinition('system', 'unsafe'));

        $resolver = new TranslationResolver(
            $registry,
            new class extends TranslationValueProvider
            {
                public function values(): array
                {
                    return ['en' => ['system.unsafe' => '<strong>Unsafe</strong>']];
                }
            },
            app(LocaleRegistry::class),
        );

        $this->expectException(InvalidTranslationValueException::class);

        $resolver->get('system.unsafe', 'en');
    }
}
