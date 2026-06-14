<?php

namespace App\Providers;

use App\Modules\Security\Logging\SanitizedLogProcessor;
use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use App\Modules\Security\Services\SecurityExceptionMapper;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
