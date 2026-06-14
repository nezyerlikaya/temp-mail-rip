<?php

namespace App\Modules\FeatureFlags\Exceptions;

class ProtectedFeatureFlagException extends FeatureFlagException
{
    public static function forKey(string $key): self
    {
        return new self("Security protection [{$key}] cannot be controlled by feature flags.");
    }
}
