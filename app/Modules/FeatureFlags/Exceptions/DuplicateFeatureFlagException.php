<?php

namespace App\Modules\FeatureFlags\Exceptions;

class DuplicateFeatureFlagException extends FeatureFlagException
{
    public static function forKey(string $key): self
    {
        return new self("Duplicate feature flag [{$key}].");
    }
}
