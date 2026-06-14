<?php

namespace App\Modules\Security\Services;

use UnitEnum;

class SecretMasker
{
    private const MASK = '[MASKED]';

    /**
     * @var list<string>
     */
    private array $sensitiveKeyFragments = [
        'authorization',
        'cookie',
        'credential',
        'database_url',
        'db_password',
        'encryption_key',
        'key',
        'mail_password',
        'password',
        'provider_secret',
        'secret',
        'session',
        'storage_secret',
        'token',
        'webhook_secret',
        'api_key',
    ];

    public function mask(mixed $value, ?string $key = null): mixed
    {
        if ($key !== null && $this->isSensitiveKey($key)) {
            return self::MASK;
        }

        if (is_array($value)) {
            $masked = [];

            foreach ($value as $itemKey => $itemValue) {
                $masked[$itemKey] = $this->mask($itemValue, is_string($itemKey) ? $itemKey : null);
            }

            return $masked;
        }

        if (is_string($value)) {
            return $this->maskText($value);
        }

        if (is_null($value) || is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        return '[object '.str_replace('\\', '.', $value::class).']';
    }

    public function maskText(string $value): string
    {
        $patterns = [
            '/\b(Authorization\s*:\s*)(Bearer|Basic)\s+[A-Za-z0-9._~+\/=-]+/i' => '$1$2 '.self::MASK,
            '/\b(password|passwd|pwd|token|api[_-]?key|secret|session[_-]?id|webhook[_-]?secret)=([^&\s,;]+)/i' => '$1='.self::MASK,
            '/("?(?:password|passwd|pwd|token|api[_-]?key|secret|session[_-]?id|webhook[_-]?secret)"?\s*:\s*)"[^"]*"/i' => '$1"'.self::MASK.'"',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $value = preg_replace($pattern, $replacement, $value) ?? $value;
        }

        return $value;
    }

    public function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower(str_replace(['-', '.', ' '], '_', $key));

        foreach ($this->sensitiveKeyFragments as $fragment) {
            if (str_contains($normalized, $fragment)) {
                return true;
            }
        }

        return false;
    }
}
