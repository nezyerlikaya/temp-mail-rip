<?php

namespace Tests\Feature\Navigation;

use App\Modules\FeatureFlags\Services\FeatureFlagResolver;
use App\Modules\Navigation\DTOs\ResolvedNavigationItem;
use App\Modules\Navigation\Services\NavigationResolver;
use Mockery;
use Tests\TestCase;

class NavigationResolverTest extends TestCase
{
    public function test_navigation_resolves_named_route_translation_and_active_state(): void
    {
        $items = app(NavigationResolver::class)->resolve('public', 'tr', 'home');

        $this->assertCount(1, $items);
        $this->assertSame('public.home', $items[0]->key);
        $this->assertSame('Ana sayfa', $items[0]->label);
        $this->assertSame(url('/'), $items[0]->url);
        $this->assertTrue($items[0]->active);
    }

    public function test_feature_flags_can_hide_navigation_without_granting_access(): void
    {
        $flags = Mockery::mock(FeatureFlagResolver::class);
        $flags->shouldReceive('available')->with('platform.public_app')->andReturn(false);
        $this->app->instance(FeatureFlagResolver::class, $flags);
        $this->app->forgetInstance(NavigationResolver::class);

        $items = app(NavigationResolver::class)->resolve('public', 'en', 'home');

        $this->assertSame([], $items);
    }

    public function test_navigation_component_escapes_labels(): void
    {
        $item = new ResolvedNavigationItem(
            key: 'public.home',
            label: '<script>alert(1)</script>',
            url: url('/'),
            active: false,
            order: 10,
        );

        $this->blade('<x-navigation.menu :items="$items" />', ['items' => [$item]])
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>', false);
    }
}
