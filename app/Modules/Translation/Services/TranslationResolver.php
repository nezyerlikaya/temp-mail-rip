<?php

namespace App\Modules\Translation\Services;

use App\Modules\Localization\Services\LocaleRegistry;
use App\Modules\Translation\DTOs\TranslationDefinition;
use App\Modules\Translation\Exceptions\InvalidTranslationValueException;
use Illuminate\Support\Facades\Cache;

class TranslationResolver
{
    public function __construct(
        private readonly TranslationNamespaceRegistry $registry,
        private readonly TranslationValueProvider $values,
        private readonly LocaleRegistry $locales,
    ) {}

    /**
     * @param  array<string, scalar>  $replace
     */
    public function get(string $canonicalKey, string $locale, array $replace = []): string
    {
        $definition = $this->registry->get($canonicalKey);
        $translation = $this->lookup($canonicalKey, $locale)
            ?? $this->lookup($canonicalKey, $this->locales->fallbackFor($locale)->code)
            ?? $this->missing($canonicalKey);

        $this->validatePlaceholders($definition, $translation, $replace);

        return $this->replace($translation, $replace);
    }

    public function forget(string $locale, string $namespace): void
    {
        Cache::forget($this->cacheKey($locale, $namespace));
    }

    private function lookup(string $canonicalKey, string $locale): ?string
    {
        $definition = $this->registry->get($canonicalKey);

        $namespaceValues = Cache::remember(
            $this->cacheKey($locale, $definition->namespace),
            now()->addMinutes(10),
            fn (): array => $this->namespaceValues($locale, $definition->namespace),
        );

        return $namespaceValues[$canonicalKey] ?? null;
    }

    /**
     * @return array<string, string>
     */
    private function namespaceValues(string $locale, string $namespace): array
    {
        $values = $this->values->values()[$locale] ?? [];

        return array_filter(
            $values,
            fn (string $key): bool => str_starts_with($key, $namespace.'.'),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * @param  array<string, scalar>  $replace
     */
    private function validatePlaceholders(TranslationDefinition $definition, string $translation, array $replace): void
    {
        preg_match_all('/:([A-Za-z_][A-Za-z0-9_]*)/', $translation, $matches);
        $found = array_values(array_unique($matches[1] ?? []));

        sort($found);
        $expected = $definition->placeholders;
        sort($expected);
        $provided = array_keys($replace);
        sort($provided);

        if ($found !== $expected || $provided !== $expected) {
            throw new InvalidTranslationValueException('Translation placeholders do not match the registered definition.');
        }

        if (preg_match('/<\s*\/?\s*[a-zA-Z][^>]*>/', $translation)) {
            throw new InvalidTranslationValueException('HTML translations are not allowed in this foundation.');
        }
    }

    /**
     * @param  array<string, scalar>  $replace
     */
    private function replace(string $translation, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $translation = str_replace(':'.$key, e((string) $value), $translation);
        }

        return $translation;
    }

    private function missing(string $canonicalKey): string
    {
        return app()->environment('production') ? '[translation missing]' : '[missing:'.$canonicalKey.']';
    }

    private function cacheKey(string $locale, string $namespace): string
    {
        return 'translations.'.$locale.'.'.$namespace;
    }
}
