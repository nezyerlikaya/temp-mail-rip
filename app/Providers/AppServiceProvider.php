<?php

namespace App\Providers;

use App\Modules\Compliance\Repositories\LegalDocumentRepository;
use App\Modules\Compliance\Services\LegalContentSanitizer;
use App\Modules\Compliance\Services\LegalDocumentDefinitionProvider;
use App\Modules\Compliance\Services\LegalDocumentRegistry;
use App\Modules\Compliance\Services\LegalDocumentResolver;
use App\Modules\Compliance\Services\LegalSettingsDefinitionProvider;
use App\Modules\DomainHealth\Repositories\DomainHealthRepository;
use App\Modules\DomainHealth\Services\DnsReadinessResolver;
use App\Modules\DomainHealth\Services\DomainHealthBatchChecker;
use App\Modules\DomainHealth\Services\DomainHealthChecker;
use App\Modules\DomainHealth\Services\DomainHealthSettingsDefinitionProvider;
use App\Modules\DomainHealth\Services\DomainHealthStatusCalculator;
use App\Modules\Domains\Repositories\DomainRepository;
use App\Modules\Domains\Services\DomainInventory;
use App\Modules\Domains\Services\DomainNormalizer;
use App\Modules\Domains\Services\DomainNotesPolicy;
use App\Modules\Domains\Services\DomainSettingsDefinitionProvider;
use App\Modules\FeatureFlags\Services\FeatureFlagDefinitionProvider;
use App\Modules\FeatureFlags\Services\FeatureFlagRegistry;
use App\Modules\FeatureFlags\Services\FeatureFlagResolver;
use App\Modules\FeatureFlags\Services\FeatureFlagSettingsDefinitionProvider;
use App\Modules\FeatureFlags\Services\RolloutEvaluator;
use App\Modules\Installer\Services\InstallationLock;
use App\Modules\Installer\Services\InstallationStateDetector;
use App\Modules\Installer\Services\PreflightChecker;
use App\Modules\Localization\Services\LocaleDefinitionProvider;
use App\Modules\Localization\Services\LocaleNormalizer;
use App\Modules\Localization\Services\LocaleRegistry;
use App\Modules\Localization\Services\LocaleResolver;
use App\Modules\Localization\Services\LocalizationSettingsDefinitionProvider;
use App\Modules\Mail\Services\EmailPlaceholderRenderer;
use App\Modules\Mail\Services\EmailTemplateDefinitionProvider;
use App\Modules\Mail\Services\EmailTemplateRegistry;
use App\Modules\Mail\Services\EmailTemplateResolver;
use App\Modules\Mail\Services\EmailTemplateSettingsDefinitionProvider;
use App\Modules\Navigation\Services\NavigationDefinitionProvider;
use App\Modules\Navigation\Services\NavigationRegistry;
use App\Modules\Navigation\Services\NavigationResolver;
use App\Modules\Security\Logging\SanitizedLogProcessor;
use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use App\Modules\Security\Services\SecurityExceptionMapper;
use App\Modules\Settings\Repositories\SettingsRepository;
use App\Modules\Settings\Services\SettingsDefinitionProvider;
use App\Modules\Settings\Services\SettingsRegistry;
use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\SystemHealth\Services\DegradedStateResolver;
use App\Modules\SystemHealth\Services\HealthCheckDefinitionProvider;
use App\Modules\SystemHealth\Services\HealthCheckRegistry;
use App\Modules\SystemHealth\Services\HealthCheckRunner;
use App\Modules\SystemHealth\Services\HealthResultFactory;
use App\Modules\SystemHealth\Services\HealthSummaryResolver;
use App\Modules\SystemHealth\Services\SchedulerHeartbeat;
use App\Modules\SystemHealth\Services\SystemHealthSettingsDefinitionProvider;
use App\Modules\Theme\Services\ThemeDefinitionProvider;
use App\Modules\Theme\Services\ThemeRegistry;
use App\Modules\Theme\Services\ThemeResolver;
use App\Modules\Theme\Services\ThemeSettingsDefinitionProvider;
use App\Modules\Translation\Services\TranslationDefinitionProvider;
use App\Modules\Translation\Services\TranslationNamespaceRegistry;
use App\Modules\Translation\Services\TranslationResolver;
use App\Modules\Translation\Services\TranslationValueProvider;
use App\Modules\Uploads\Services\FilenameNormalizer;
use App\Modules\Uploads\Services\UploadPathGenerator;
use App\Modules\Uploads\Services\UploadScopeDefinitionProvider;
use App\Modules\Uploads\Services\UploadScopeRegistry;
use App\Modules\Uploads\Services\UploadSettingsDefinitionProvider;
use App\Modules\Uploads\Services\UploadValidator;
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
        $this->app->singleton(DomainSettingsDefinitionProvider::class);
        $this->app->singleton(DomainNormalizer::class);
        $this->app->singleton(DomainNotesPolicy::class);
        $this->app->singleton(DomainRepository::class);
        $this->app->singleton(DomainInventory::class);
        $this->app->singleton(DomainHealthSettingsDefinitionProvider::class);
        $this->app->singleton(DnsReadinessResolver::class);
        $this->app->singleton(DomainHealthStatusCalculator::class);
        $this->app->singleton(DomainHealthRepository::class);
        $this->app->singleton(DomainHealthChecker::class);
        $this->app->singleton(DomainHealthBatchChecker::class);
        $this->app->singleton(SystemHealthSettingsDefinitionProvider::class);
        $this->app->singleton(HealthResultFactory::class);
        $this->app->singleton(HealthCheckDefinitionProvider::class);
        $this->app->singleton(HealthCheckRunner::class);
        $this->app->singleton(HealthSummaryResolver::class);
        $this->app->singleton(DegradedStateResolver::class);
        $this->app->singleton(SchedulerHeartbeat::class);
        $this->app->singleton(FeatureFlagDefinitionProvider::class);
        $this->app->singleton(FeatureFlagSettingsDefinitionProvider::class);
        $this->app->singleton(FeatureFlagResolver::class);
        $this->app->singleton(RolloutEvaluator::class);
        $this->app->singleton(LegalDocumentDefinitionProvider::class);
        $this->app->singleton(LegalSettingsDefinitionProvider::class);
        $this->app->singleton(LegalDocumentRepository::class);
        $this->app->singleton(LegalContentSanitizer::class);
        $this->app->singleton(LegalDocumentResolver::class);
        $this->app->singleton(EmailTemplateDefinitionProvider::class);
        $this->app->singleton(EmailTemplateSettingsDefinitionProvider::class);
        $this->app->singleton(EmailPlaceholderRenderer::class);
        $this->app->singleton(EmailTemplateResolver::class);
        $this->app->singleton(InstallationLock::class);
        $this->app->singleton(InstallationStateDetector::class);
        $this->app->singleton(PreflightChecker::class);
        $this->app->singleton(LocaleNormalizer::class);
        $this->app->singleton(LocaleDefinitionProvider::class);
        $this->app->singleton(LocalizationSettingsDefinitionProvider::class);
        $this->app->singleton(LocaleResolver::class);
        $this->app->singleton(TranslationDefinitionProvider::class);
        $this->app->singleton(TranslationValueProvider::class);
        $this->app->singleton(TranslationResolver::class);
        $this->app->singleton(NavigationDefinitionProvider::class);
        $this->app->singleton(NavigationResolver::class);
        $this->app->singleton(ThemeDefinitionProvider::class);
        $this->app->singleton(ThemeSettingsDefinitionProvider::class);
        $this->app->singleton(ThemeResolver::class);
        $this->app->singleton(FilenameNormalizer::class);
        $this->app->singleton(UploadPathGenerator::class);
        $this->app->singleton(UploadScopeDefinitionProvider::class);
        $this->app->singleton(UploadSettingsDefinitionProvider::class);
        $this->app->singleton(UploadValidator::class);

        $this->app->singleton(FeatureFlagRegistry::class, function ($app): FeatureFlagRegistry {
            $registry = new FeatureFlagRegistry;

            foreach ($app->make(FeatureFlagDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(HealthCheckRegistry::class, function ($app): HealthCheckRegistry {
            $registry = new HealthCheckRegistry;

            foreach ($app->make(HealthCheckDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(LegalDocumentRegistry::class, function ($app): LegalDocumentRegistry {
            $registry = new LegalDocumentRegistry;

            foreach ($app->make(LegalDocumentDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(EmailTemplateRegistry::class, function ($app): EmailTemplateRegistry {
            $registry = new EmailTemplateRegistry;

            foreach ($app->make(EmailTemplateDefinitionProvider::class)->definitions() as $definition) {
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

        $this->app->singleton(NavigationRegistry::class, function ($app): NavigationRegistry {
            $registry = new NavigationRegistry;

            foreach ($app->make(NavigationDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            $registry->validate();

            return $registry;
        });

        $this->app->singleton(ThemeRegistry::class, function ($app): ThemeRegistry {
            $registry = new ThemeRegistry;

            foreach ($app->make(ThemeDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            return $registry;
        });

        $this->app->singleton(UploadScopeRegistry::class, function ($app): UploadScopeRegistry {
            $registry = new UploadScopeRegistry;

            foreach ($app->make(UploadScopeDefinitionProvider::class)->definitions() as $definition) {
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

            foreach ($app->make(ThemeSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(UploadSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(LegalSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(EmailTemplateSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(SystemHealthSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(DomainSettingsDefinitionProvider::class)->definitions() as $definition) {
                $registry->register($definition);
            }

            foreach ($app->make(DomainHealthSettingsDefinitionProvider::class)->definitions() as $definition) {
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
