<?php

namespace Tests\Feature\Settings;

use App\Modules\Security\Services\SecretMasker;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Exceptions\InvalidSettingValueException;
use App\Modules\Settings\Exceptions\UnknownSettingException;
use App\Modules\Settings\Repositories\SettingsRepository;
use App\Modules\Settings\Services\SettingsDefinitionProvider;
use App\Modules\Settings\Services\SettingsRegistry;
use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\Settings\Services\SettingValueCaster;
use App\Modules\Settings\Services\SettingValueValidator;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\TestCase;

class SettingsResolverTest extends TestCase
{
    private SettingsResolver $settings;

    private FakeSettingsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $registry = new SettingsRegistry;

        foreach ((new SettingsDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        $this->repository = new FakeSettingsRepository(new SettingValueCaster);
        $this->settings = new SettingsResolver(
            $registry,
            $this->repository,
            new SettingValueValidator(new SettingValueCaster),
            app(SecretMasker::class),
        );
    }

    public function test_registered_default_settings_resolve_with_typed_values(): void
    {
        $this->assertSame('Temp Mail', $this->settings->get('platform.display_name'));
        $this->assertSame(25, $this->settings->get('platform.default_page_size'));
        $this->assertTrue($this->settings->get('platform.show_branding'));
        $this->assertSame(604800, $this->settings->get('security.diagnostics_retention_seconds'));
    }

    public function test_unknown_keys_fail_predictably(): void
    {
        $this->expectException(UnknownSettingException::class);

        $this->settings->get('platform.unknown');
    }

    public function test_values_are_validated_and_cast_safely_before_persistence(): void
    {
        $this->assertSame(50, $this->settings->put('platform.default_page_size', '50'));
        $this->assertSame(50, $this->settings->get('platform.default_page_size'));
        $this->assertFalse($this->settings->put('platform.show_branding', 'false'));
        $this->assertFalse($this->settings->get('platform.show_branding'));

        $this->settings->put('platform.public_metadata', ['tagline' => 'Safe']);
        $this->assertSame(['tagline' => 'Safe'], $this->settings->get('platform.public_metadata'));
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->expectException(InvalidSettingValueException::class);

        $this->settings->put('platform.default_page_size', '10.5');
    }

    public function test_invalid_stored_values_fail_instead_of_silent_coercion(): void
    {
        $this->repository->forceRaw('platform.default_page_size', 'integer', 'truthy', false);

        $this->expectException(InvalidSettingValueException::class);

        $this->settings->get('platform.default_page_size');
    }

    public function test_public_values_use_explicit_non_sensitive_allow_list(): void
    {
        $this->settings->put('security.operator_notice', 'secret operator note');

        $public = $this->settings->publicValues();

        $this->assertArrayHasKey('platform.display_name', $public);
        $this->assertArrayHasKey('platform.show_branding', $public);
        $this->assertArrayHasKey('platform.public_metadata', $public);
        $this->assertArrayNotHasKey('security.operator_notice', $public);
        $this->assertArrayNotHasKey('platform.default_page_size', $public);
        $this->assertStringNotContainsString('secret operator note', json_encode($public, JSON_THROW_ON_ERROR));
    }

    public function test_sensitive_setting_diagnostics_are_masked(): void
    {
        $this->settings->put('security.operator_notice', 'secret operator note');

        $diagnostics = $this->settings->diagnostics('security.operator_notice');

        $this->assertSame('[MASKED]', $diagnostics['value']);
        $this->assertStringNotContainsString('secret operator note', json_encode($diagnostics, JSON_THROW_ON_ERROR));
    }

    public function test_cache_invalidates_after_updates(): void
    {
        $this->settings->put('platform.display_name', 'First');
        $this->assertSame('First', $this->settings->get('platform.display_name'));

        $this->repository->forceRaw('platform.display_name', 'string', 'Stale', false);

        $this->assertSame('First', $this->settings->get('platform.display_name'));

        $this->settings->put('platform.display_name', 'Fresh');

        $this->assertSame('Fresh', $this->settings->get('platform.display_name'));
    }

    public function test_cache_flush_falls_back_to_valid_database_value_without_exposing_secrets(): void
    {
        $this->settings->put('security.operator_notice', 'secret operator note');

        Cache::flush();

        $this->assertSame('secret operator note', $this->settings->get('security.operator_notice'));
        $this->assertSame('[MASKED]', $this->settings->diagnostics('security.operator_notice')['value']);
    }

    public function test_database_failure_is_explicit_and_does_not_return_public_secret_defaults(): void
    {
        $this->repository->failReads();

        $this->expectException(RuntimeException::class);

        $this->settings->get('security.operator_notice');
    }
}

class FakeSettingsRepository extends SettingsRepository
{
    /**
     * @var array<string, array{value: string|null, type: string, is_sensitive: bool}>
     */
    private array $rows = [];

    private bool $failReads = false;

    public function __construct(private readonly SettingValueCaster $caster) {}

    public function find(SettingDefinition $definition): ?array
    {
        if ($this->failReads) {
            throw new RuntimeException('settings storage unavailable');
        }

        return $this->rows[$definition->key] ?? null;
    }

    public function put(SettingDefinition $definition, mixed $value): void
    {
        $this->rows[$definition->key] = [
            'value' => $this->caster->serializeForStorage($definition, $value),
            'type' => $definition->type->value,
            'is_sensitive' => $definition->isSensitive,
        ];
    }

    public function delete(SettingDefinition $definition): void
    {
        unset($this->rows[$definition->key]);
    }

    public function forceRaw(string $key, string $type, ?string $value, bool $isSensitive): void
    {
        $this->rows[$key] = [
            'value' => $value,
            'type' => $type,
            'is_sensitive' => $isSensitive,
        ];
    }

    public function failReads(): void
    {
        $this->failReads = true;
    }
}
