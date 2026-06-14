<?php

namespace Tests\Unit\FeatureFlags;

use App\Modules\FeatureFlags\DTOs\FeatureFlagDefinition;
use App\Modules\FeatureFlags\Enums\FeatureFlagState;
use App\Modules\FeatureFlags\Services\RolloutEvaluator;
use PHPUnit\Framework\TestCase;

class RolloutEvaluatorTest extends TestCase
{
    public function test_rollout_result_is_deterministic_for_same_subject_and_feature(): void
    {
        $definition = new FeatureFlagDefinition(
            key: 'platform.beta_features',
            description: 'Beta flag.',
            ownerModule: 'FeatureFlags',
            defaultState: FeatureFlagState::Beta,
            defaultRolloutPercentage: 25,
            rolloutSalt: 'test-salt',
        );
        $evaluator = new RolloutEvaluator;

        $first = $evaluator->included($definition, 'stable-subject-123', 25);
        $second = $evaluator->included($definition, 'stable-subject-123', 25);

        $this->assertSame($first, $second);
    }

    public function test_rollout_rejects_pii_subjects(): void
    {
        $definition = new FeatureFlagDefinition(
            key: 'platform.beta_features',
            description: 'Beta flag.',
            ownerModule: 'FeatureFlags',
            defaultState: FeatureFlagState::Beta,
        );

        $this->expectException(\InvalidArgumentException::class);

        (new RolloutEvaluator)->included($definition, 'person@example.com', 50);
    }
}
