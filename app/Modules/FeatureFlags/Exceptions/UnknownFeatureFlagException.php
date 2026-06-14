<?php

namespace App\Modules\FeatureFlags\Exceptions;

class UnknownFeatureFlagException extends FeatureFlagException
{
    public static function forKey(string $key): self
    {
        return new self("Unknown feature flag [{$key}].");
    }
}
