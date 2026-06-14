<?php

namespace App\Modules\Navigation\Services;

use App\Modules\FeatureFlags\Services\FeatureFlagResolver;
use App\Modules\Navigation\DTOs\NavigationItemDefinition;
use App\Modules\Navigation\DTOs\ResolvedNavigationItem;
use App\Modules\Translation\Services\TranslationResolver;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use Throwable;

class NavigationResolver
{
    public function __construct(
        private readonly NavigationRegistry $registry,
        private readonly TranslationResolver $translations,
        private readonly FeatureFlagResolver $flags,
        private readonly UrlGenerator $url,
    ) {}

    /**
     * @return list<ResolvedNavigationItem>
     */
    public function resolve(string $area, string $locale, ?string $currentRouteName = null): array
    {
        $definitions = array_filter(
            $this->registry->forArea($area),
            fn (NavigationItemDefinition $item): bool => $item->parentKey === null && $this->visible($item),
        );

        return array_values(array_map(
            fn (NavigationItemDefinition $item): ResolvedNavigationItem => $this->resolveItem($item, $locale, $currentRouteName),
            $definitions,
        ));
    }

    private function resolveItem(NavigationItemDefinition $item, string $locale, ?string $currentRouteName): ResolvedNavigationItem
    {
        return new ResolvedNavigationItem(
            key: $item->key,
            label: $this->translations->get($item->labelKey, $locale),
            url: Route::has($item->routeName) ? $this->url->route($item->routeName) : '#',
            active: $this->active($item, $currentRouteName),
            order: $item->order,
            icon: $item->icon,
        );
    }

    private function visible(NavigationItemDefinition $item): bool
    {
        if (! Route::has($item->routeName)) {
            return false;
        }

        if ($item->featureFlag === null) {
            return true;
        }

        try {
            return $this->flags->available($item->featureFlag);
        } catch (Throwable) {
            return false;
        }
    }

    private function active(NavigationItemDefinition $item, ?string $currentRouteName): bool
    {
        if ($currentRouteName === null) {
            return false;
        }

        foreach ([$item->routeName, ...$item->activeRoutePatterns] as $pattern) {
            if ($pattern === $currentRouteName) {
                return true;
            }

            if (str_ends_with($pattern, '*') && str_starts_with($currentRouteName, rtrim($pattern, '*'))) {
                return true;
            }
        }

        return false;
    }
}
