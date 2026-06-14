<?php

namespace Tests\Unit\Navigation;

use App\Modules\Navigation\DTOs\NavigationItemDefinition;
use App\Modules\Navigation\Exceptions\DuplicateNavigationItemException;
use App\Modules\Navigation\Exceptions\InvalidNavigationHierarchyException;
use App\Modules\Navigation\Services\NavigationRegistry;
use PHPUnit\Framework\TestCase;

class NavigationRegistryTest extends TestCase
{
    public function test_items_register_and_order_deterministically(): void
    {
        $registry = new NavigationRegistry;
        $registry->register(new NavigationItemDefinition('public.zed', 'navigation.home', 'home', 'public', 20));
        $registry->register(new NavigationItemDefinition('public.alpha', 'navigation.home', 'home', 'public', 10));

        $this->assertSame(['public.alpha', 'public.zed'], array_map(
            fn (NavigationItemDefinition $item): string => $item->key,
            $registry->forArea('public'),
        ));
    }

    public function test_duplicate_keys_and_missing_parents_fail(): void
    {
        $registry = new NavigationRegistry;
        $item = new NavigationItemDefinition('public.home', 'navigation.home', 'home', 'public');
        $registry->register($item);

        $this->expectException(DuplicateNavigationItemException::class);
        $registry->register($item);
    }

    public function test_missing_parent_and_cycles_fail(): void
    {
        $registry = new NavigationRegistry;
        $registry->register(new NavigationItemDefinition('public.child', 'navigation.home', 'home', 'public', parentKey: 'public.missing'));

        $this->expectException(InvalidNavigationHierarchyException::class);
        $registry->validate();
    }

    public function test_cycle_detection_fails(): void
    {
        $registry = new NavigationRegistry;
        $registry->register(new NavigationItemDefinition('public.a', 'navigation.home', 'home', 'public', parentKey: 'public.b'));
        $registry->register(new NavigationItemDefinition('public.b', 'navigation.home', 'home', 'public', parentKey: 'public.a'));

        $this->expectException(InvalidNavigationHierarchyException::class);
        $registry->validate();
    }
}
