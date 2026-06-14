<?php

namespace Tests\Unit\FeatureFlags;

use App\Modules\FeatureFlags\DTOs\FeatureFlagDefinition;
use App\Modules\FeatureFlags\Enums\FeatureFlagState;
use App\Modules\FeatureFlags\Exceptions\DuplicateFeatureFlagException;
use App\Modules\FeatureFlags\Exceptions\ProtectedFeatureFlagException;
use App\Modules\FeatureFlags\Services\FeatureFlagRegistry;
use PHPUnit\Framework\TestCase;

class FeatureFlagRegistryTest extends TestCase
{
    public function test_duplicate_registration_is_rejected(): void
    {
        $registry = new FeatureFlagRegistry;
        $definition = new FeatureFlagDefinition(
            key: 'platform.example',
            description: 'Example flag.',
            ownerModule: 'FeatureFlags',
            defaultState: FeatureFlagState::Disabled,
        );

        $registry->register($definition);

        $this->expectException(DuplicateFeatureFlagException::class);

        $registry->register($definition);
    }

    public function test_security_protections_cannot_be_registered_as_flags(): void
    {
        $this->expectException(ProtectedFeatureFlagException::class);

        (new FeatureFlagRegistry)->register(new FeatureFlagDefinition(
            key: 'security.secret_masking',
            description: 'This must not be controllable by flags.',
            ownerModule: 'Security',
            defaultState: FeatureFlagState::Enabled,
        ));
    }

    public function test_rollout_percentage_is_validated(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FeatureFlagDefinition(
            key: 'platform.invalid_rollout',
            description: 'Invalid rollout.',
            ownerModule: 'FeatureFlags',
            defaultState: FeatureFlagState::Beta,
            defaultRolloutPercentage: 101,
        );
    }
}
