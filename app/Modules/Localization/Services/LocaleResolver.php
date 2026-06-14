<?php

namespace App\Modules\Localization\Services;

use App\Modules\Localization\DTOs\LocaleDefinition;
use App\Modules\Localization\Exceptions\InvalidLocaleException;
use Illuminate\Http\Request;

class LocaleResolver
{
    public function __construct(
        private readonly LocaleRegistry $registry,
        private readonly LocaleNormalizer $normalizer,
    ) {}

    public function resolve(
        ?string $routeLocale = null,
        ?string $userPreference = null,
        ?string $cookieLocale = null,
        ?string $acceptLanguage = null,
    ): LocaleDefinition {
        foreach ([$routeLocale, $userPreference, $cookieLocale] as $candidate) {
            $resolved = $this->resolvableCandidate($candidate);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        foreach ($this->parseAcceptLanguage($acceptLanguage) as $candidate) {
            $resolved = $this->resolvableCandidate($candidate);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return $this->registry->default();
    }

    public function resolveFromRequest(Request $request, ?string $userPreference = null): LocaleDefinition
    {
        return $this->resolve(
            routeLocale: $request->route('locale'),
            userPreference: $userPreference,
            cookieLocale: $request->cookie('locale'),
            acceptLanguage: $request->headers->get('Accept-Language'),
        );
    }

    public function validateRouteLocale(string $locale): string
    {
        $code = $this->normalizer->normalize($locale);

        if (! $this->registry->resolvable($code)) {
            throw InvalidLocaleException::forInput($locale);
        }

        return $code;
    }

    /**
     * @return list<string>
     */
    public function parseAcceptLanguage(?string $header): array
    {
        if ($header === null || strlen($header) > 512) {
            return [];
        }

        $locales = [];

        foreach (array_slice(explode(',', $header), 0, 10) as $part) {
            $locale = trim(explode(';', $part)[0]);
            $normalized = $this->normalizer->tryNormalize($locale);

            if ($normalized !== null) {
                $locales[] = $normalized;
                $language = explode('-', $normalized)[0];

                if ($language !== $normalized) {
                    $locales[] = $language;
                }
            }
        }

        return array_values(array_unique($locales));
    }

    private function resolvableCandidate(?string $candidate): ?LocaleDefinition
    {
        $code = $this->normalizer->tryNormalize($candidate);

        if ($code === null || ! $this->registry->resolvable($code)) {
            return null;
        }

        return $this->registry->get($code);
    }
}
