<?php

namespace App\Providers;

use App\Modules\Security\Logging\SanitizedLogProcessor;
use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use App\Modules\Security\Services\SecurityExceptionMapper;
use App\Modules\Settings\Repositories\SettingsRepository;
use App\Modules\Settings\Services\SettingsDefinitionProvider;
use App\Modules\Settings\Services\SettingsRegistry;
use App\Modules\Settings\Services\SettingsResolver;
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

        $this->app->singleton(SettingsRegistry::class, function ($app): SettingsRegistry {
            $registry = new SettingsRegistry;

            foreach ($app->make(SettingsDefinitionProvider::class)->definitions() as $definition) {
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
