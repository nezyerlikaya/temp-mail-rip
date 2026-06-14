<?php

namespace App\Modules\FeatureFlags\Services;

use App\Modules\FeatureFlags\DTOs\FeatureFlagDefinition;
use InvalidArgumentException;

class RolloutEvaluator
{
    public function included(FeatureFlagDefinition $definition, string $subjectKey, int $percentage): bool
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Rollout percentage must be between 0 and 100.');
        }

        if ($percentage === 0) {
            return false;
        }

        if ($percentage === 100) {
            return true;
        }

        if ($this->looksLikePii($subjectKey)) {
            throw new InvalidArgumentException('Rollout subject keys must not contain email, IP address, or user-agent-like PII.');
        }

        $hash = hash('sha256', $definition->rolloutSalt.'|'.$definition->key.'|'.$subjectKey);
        $bucket = (hexdec(substr($hash, 0, 8)) % 100) + 1;

        return $bucket <= $percentage;
    }

    private function looksLikePii(string $subjectKey): bool
    {
        return str_contains($subjectKey, '@')
            || filter_var($subjectKey, FILTER_VALIDATE_IP) !== false
            || preg_match('/mozilla|chrome|safari|firefox|edge|opera/i', $subjectKey) === 1;
    }
}
