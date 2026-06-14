<?php

namespace App\Modules\Localization\Services;

use App\Modules\Localization\Exceptions\InvalidLocaleException;

class LocaleNormalizer
{
    public function normalize(string $locale): string
    {
        $locale = str_replace('_', '-', trim($locale));

        if (! preg_match('/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8}){0,2}$/', $locale)) {
            throw InvalidLocaleException::forInput($locale);
        }

        $parts = explode('-', $locale);
        $normalized = [strtolower($parts[0])];

        foreach (array_slice($parts, 1) as $part) {
            $normalized[] = strlen($part) === 2 ? strtoupper($part) : ucfirst(strtolower($part));
        }

        return implode('-', $normalized);
    }

    public function tryNormalize(?string $locale): ?string
    {
        if ($locale === null || trim($locale) === '') {
            return null;
        }

        try {
            return $this->normalize($locale);
        } catch (InvalidLocaleException) {
            return null;
        }
    }
}
