<?php

namespace App\Modules\Localization\Services;

use App\Modules\Localization\DTOs\LocaleDefinition;
use App\Modules\Localization\Exceptions\DefaultLocaleException;
use App\Modules\Localization\Exceptions\DuplicateLocaleException;
use App\Modules\Localization\Exceptions\InvalidLocaleException;

class LocaleRegistry
{
    /**
     * @var array<string, LocaleDefinition>
     */
    private array $locales = [];

    public function __construct(private readonly LocaleNormalizer $normalizer) {}

    public function register(LocaleDefinition $definition): void
    {
        $code = $this->normalizer->normalize($definition->code);

        if (isset($this->locales[$code])) {
            throw DuplicateLocaleException::forCode($code);
        }

        if ($definition->isDefault && $this->defaultOrNull() !== null) {
            throw new DefaultLocaleException('Exactly one default locale may be registered.');
        }

        $this->locales[$code] = $definition;
    }

    public function get(string $locale): LocaleDefinition
    {
        $code = $this->normalizer->normalize($locale);

        return $this->locales[$code] ?? throw InvalidLocaleException::forInput($locale);
    }

    public function supports(string $locale): bool
    {
        $code = $this->normalizer->tryNormalize($locale);

        return $code !== null && isset($this->locales[$code]);
    }

    public function selectable(string $locale): bool
    {
        return $this->supports($locale) && $this->get($locale)->selectable();
    }

    public function resolvable(string $locale): bool
    {
        return $this->supports($locale) && $this->get($locale)->resolvable();
    }

    public function default(): LocaleDefinition
    {
        return $this->defaultOrNull() ?? throw new DefaultLocaleException('No default locale is registered.');
    }

    public function fallbackFor(string $locale): LocaleDefinition
    {
        $definition = $this->get($locale);

        return $definition->fallbackLocale !== null ? $this->get($definition->fallbackLocale) : $this->default();
    }

    /**
     * @return array<string, LocaleDefinition>
     */
    public function all(): array
    {
        return $this->locales;
    }

    private function defaultOrNull(): ?LocaleDefinition
    {
        foreach ($this->locales as $definition) {
            if ($definition->isDefault) {
                return $definition;
            }
        }

        return null;
    }
}
