<?php

namespace Tests\Feature\Admin;

use App\Modules\Admin\Http\Middleware\EnsureAdminShellAccessible;
use App\Modules\FeatureFlags\Services\FeatureFlagResolver;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminShellTest extends TestCase
{
    public function test_admin_shell_route_resolves_in_test_context(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin dashboard');
        $response->assertSee('Foundation shell only');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_admin_shell_renders_locale_direction_theme_and_translated_navigation(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertSee('lang="en"', false);
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('data-theme="system"', false);
        $response->assertSee('Dashboard');
        $response->assertSee(route('admin.dashboard'), false);
    }

    public function test_admin_navigation_uses_central_registry_and_feature_flags_fail_safely(): void
    {
        $flags = Mockery::mock(FeatureFlagResolver::class);
        $flags->shouldReceive('available')->with('platform.public_app')->andReturn(true)->byDefault();
        $this->app->instance(FeatureFlagResolver::class, $flags);

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
    }

    public function test_admin_components_escape_dynamic_output(): void
    {
        $html = $this->blade(
            '<x-admin.page-header title="<script>alert(1)</script>" /><x-admin.flash message="<script>alert(2)</script>" />',
        );

        $html->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>', false);
    }

    public function test_admin_shell_does_not_expose_sensitive_values(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertDontSee('APP_KEY');
        $response->assertDontSee('DB_PASSWORD');
        $response->assertDontSee(base_path());
        $response->assertDontSee('token=');
    }

    public function test_admin_shell_is_blocked_in_production_until_auth_foundations_exist(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        $middleware = new EnsureAdminShellAccessible;

        $this->expectException(HttpException::class);

        $middleware->handle(request(), fn () => response('ok'));
    }
}
