<?php

namespace App\Providers;

use App\Modules\FeatureFlags\Services\FeatureFlagDefinitionProvider;
use App\Modules\FeatureFlags\Services\FeatureFlagRegistry;
use App\Modules\FeatureFlags\Services\FeatureFlagResolver;
use App\Modules\FeatureFlags\Services\FeatureFlagSettingsDefinitionProvider;
use App\Modules\FeatureFlags\Services\RolloutEvaluator;
use App\Modules\Localization\Services\LocaleDefinitionProvider;
use App\Modules\Localization\Services\LocaleNormalizer;
use App\Modules\Localization\Services\LocaleRegistry;
use App\Modules\Localization\Services\LocaleResolver;
use App\Modules\Localization\Services\LocalizationSettingsDefinitionProvider;
use App\Modules\Security\Logging\SanitizedLogProcessor;
use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use App\Modules\Security\Services\SecurityExceptionMapper;
use App\Modules\Settings\Repositories\SettingsRepository;
use App\Modules\Settings\Services\SettingsDefinitionProvider;
use App\Modules\Settings\Services\SettingsRegistry;
use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\Translation\Services\TranslationDefinitionProvider;
use App\Modules\Translation\Services\TranslationNamespaceRegistry;
use App\Modules\Translation\Services\TranslationResolver;
use App\Modules\Translation\Services\TranslationValueProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SecretMasker::class);
        $this->app->singleton(PathAnonymizer::class);
        $this->app->singleton(SafeDiagnosticsFormatter::class);
        $this->app->singleton(SecurityExceptionMapper::class);
        $this->app->singleton(SanitizedLogProcessor::class);
        $this->app->singleton(SettingsRepository::class);
        $this->app->singleton(SettingsDefinitionProvider::class);
        $this->app->singleton(SettingsResolver::class);
        $this->app->singleton(FeatureFlagDefinitionProvider::class);
        $this->app->singleton(FeatureFlagSettingsDefinitionProvider::class);
        $this->app->singleton(FeatureFlagResolver::class);
        $this->app->singleton(RolloutEvaluator::class);
        $this->app->singleton(LocaleNormalizer::class);
        $this->app->singleton(LocaleDefinitionProvider::class);
        $this->app->singleton(LocalizationSettingsDefinitionProvider::class);
        $this->app->singleton(LocaleResolver::class);
        $this->app->singleton(TranslationDefinitionProvider::class);
        $this->app->singleton(TranslationValueProvider::class);
        $this->app->singleton(TranslationResolver::class);

        $this->app->singleton(FeatureFlagRegistry::class, function ($app): FeatureFlagRegistry {
            $registry = new FeatureFlagRegistry;

            foreach ($app->make(FeatureFlagDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(LocaleRegistry::class, function ($app): LocaleRegistry {
            $registry = new LocaleRegistry($app->make(LocaleNormalizer::class));

            foreach ($app->make(LocaleDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(TranslationNamespaceRegistry::class, function ($app): TranslationNamespaceRegistry {
            $registry = new TranslationNamespaceRegistry;

            foreach ($app->make(TranslationDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(SettingsRegistry::class, function ($app): SettingsRegistry {
            $registry = new SettingsRegistry;

            foreach ($app->make(SettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(FeatureFlagSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(LocalizationSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
