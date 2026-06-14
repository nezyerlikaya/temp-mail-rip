<?php

namespace Tests\Feature\FeatureFlags;

use App\Modules\FeatureFlags\Enums\FeatureFlagState;
use App\Modules\FeatureFlags\Exceptions\UnknownFeatureFlagException;
use App\Modules\FeatureFlags\Services\FeatureFlagResolver;
use App\Modules\Settings\Services\SettingsResolver;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FeatureFlagResolverTest extends TestCase
{
    private FeatureFlagResolver $flags;

    private SettingsResolver $settings;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $fakeSettings = new FakeSettingsResolver;
        $this->app->instance(SettingsResolver::class, $fakeSettings);

        $this->flags = app(FeatureFlagResolver::class);
        $this->settings = $fakeSettings;
    }

    public function test_registered_flags_resolve_enabled_and_disabled_states(): void
    {
        $this->assertTrue($this->flags->available('platform.public_app'));
        $this->assertFalse($this->flags->available('platform.beta_features', 'stable-subject'));
    }

    public function test_unknown_flags_do_not_silently_enable(): void
    {
        $this->expectException(UnknownFeatureFlagException::class);

        $this->flags->available('platform.unknown');
    }

    public function test_settings_overrides_obey_precedence_over_defaults(): void
    {
        $this->flags->setState('platform.public_app', FeatureFlagState::Disabled);

        $this->assertFalse($this->flags->available('platform.public_app'));
    }

    public function test_kill_switch_overrides_ordinary_availability_by_removing_access(): void
    {
        $this->assertTrue($this->flags->available('operations.optional_modules_kill_switch'));

        $this->flags->setState('operations.optional_modules_kill_switch', FeatureFlagState::Enabled);

        $result = $this->flags->resolve('operations.optional_modules_kill_switch');

        $this->assertFalse($result->available);
        $this->assertTrue($result->killSwitchActive);
    }

    public function test_beta_rollout_is_deterministic_and_uses_validated_settings(): void
    {
        $this->flags->setState('platform.beta_features', FeatureFlagState::Beta);
        $this->flags->setRolloutPercentage('platform.beta_features', 100);

        $this->assertTrue($this->flags->available('platform.beta_features', 'stable-subject'));
        $this->assertTrue($this->flags->available('platform.beta_features', 'stable-subject'));
    }

    public function test_cache_invalidation_after_settings_update_is_selective_and_safe(): void
    {
        $this->assertTrue($this->flags->available('platform.public_app'));

        $this->flags->setState('platform.public_app', FeatureFlagState::Disabled);

        $this->assertFalse($this->flags->available('platform.public_app'));
    }

    public function test_cache_failure_uses_safe_behavior(): void
    {
        Cache::shouldReceive('remember')->once()->andThrow(new \RuntimeException('cache unavailable'));

        $this->assertTrue($this->flags->available('platform.public_app'));
    }
}

class FakeSettingsResolver extends SettingsResolver
{
    /**
     * @var array<string, mixed>
     */
    private array $values = [
        'featureflags.platform_public_app.state' => 'enabled',
        'featureflags.platform_beta_features.state' => 'disabled',
        'featureflags.platform_beta_features.rollout_percentage' => 0,
        'featureflags.operations_optional_modules_kill_switch.state' => 'disabled',
    ];

    public function __construct() {}

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? throw new \RuntimeException("Unknown fake setting [{$key}].");
    }

    public function put(string $key, mixed $value): mixed
    {
        $this->values[$key] = $value;

        return $value;
    }
}
