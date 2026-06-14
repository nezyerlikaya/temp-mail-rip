<?php

namespace Tests\Feature\Installer;

use App\Modules\Installer\DTOs\PreflightCheckResult;
use App\Modules\Installer\Services\InstallationLock;
use Tests\TestCase;

class InstallerRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(InstallationLock::class, new class extends InstallationLock
        {
            public function locked(): bool
            {
                return false;
            }

            public function create(): void {}
        });
    }

    public function test_installer_route_is_named_and_renders_safe_preflight_ui(): void
    {
        $response = $this->get(route('installer.preflight'));

        $response->assertOk();
        $response->assertSee('Installer preflight');
        $response->assertDontSee('DB_PASSWORD');
        $response->assertDontSee(base_path());
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_locked_installer_blocks_access(): void
    {
        $this->app->instance(InstallationLock::class, new class extends InstallationLock
        {
            public function locked(): bool
            {
                return true;
            }
        });

        $response = $this->get(route('installer.preflight'));

        $response->assertStatus(423);
        $response->assertSee('Installer locked');
    }

    public function test_lock_action_is_post_only_and_form_contains_csrf_token(): void
    {
        $route = app('router')->getRoutes()->getByName('installer.lock');

        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
        $this->assertNotContains('GET', $route->methods());

        $this->get(route('installer.preflight'))
            ->assertSee('name="_token"', false);
    }

    public function test_installer_ui_escapes_output(): void
    {
        $check = new PreflightCheckResult(
            key: 'x',
            label: '<script>alert(1)</script>',
            status: 'warning',
            message: '<script>alert(2)</script>',
        );

        $this->blade('<x-layouts.public locale="en" direction="ltr" theme="system"><p>{{ $check->label }}</p><p>{{ $check->message }}</p></x-layouts.public>', [
            'check' => $check,
        ])
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>', false);
    }
}
