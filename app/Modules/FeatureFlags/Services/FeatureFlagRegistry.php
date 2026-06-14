<?php

namespace App\Modules\FeatureFlags\Services;

use App\Modules\FeatureFlags\DTOs\FeatureFlagDefinition;
use App\Modules\FeatureFlags\Exceptions\DuplicateFeatureFlagException;
use App\Modules\FeatureFlags\Exceptions\ProtectedFeatureFlagException;
use App\Modules\FeatureFlags\Exceptions\UnknownFeatureFlagException;

class FeatureFlagRegistry
{
    /**
     * @var array<string, FeatureFlagDefinition>
     */
    private array $definitions = [];

    /**
     * @var list<string>
     */
    private array $protectedPrefixes = [
        'auth.',
        'authorization.',
        'csrf.',
        'rate_limits.',
        'security.',
        'validation.',
    ];

    public function register(FeatureFlagDefinition $definition): void
    {
        $this->guardProtectedSecurityCapability($definition->key);

        if (isset($this->definitions[$definition->key])) {
            throw DuplicateFeatureFlagException::forKey($definition->key);
        }

        $this->definitions[$definition->key] = $definition;
    }

    public function get(string $key): FeatureFlagDefinition
    {
        return $this->definitions[$key] ?? throw UnknownFeatureFlagException::forKey($key);
    }

    /**
     * @return array<string, FeatureFlagDefinition>
     */
    public function all(): array
    {
        return $this->definitions;
    }

    private function guardProtectedSecurityCapability(string $key): void
    {
        foreach ($this->protectedPrefixes as $prefix) {
            if (str_starts_with($key, $prefix)) {
                throw ProtectedFeatureFlagException::forKey($key);
            }
        }
    }
}
