<?php

namespace App\Modules\Navigation\Services;

use App\Modules\Navigation\DTOs\NavigationItemDefinition;

class NavigationDefinitionProvider
{
    /**
     * @return list<NavigationItemDefinition>
     */
    public function definitions(): array
    {
        return [
            new NavigationItemDefinition(
                key: 'public.home',
                labelKey: 'navigation.home',
                routeName: 'home',
                area: 'public',
                order: 10,
                icon: 'home',
                featureFlag: 'platform.public_app',
                activeRoutePatterns: ['home'],
            ),
            new NavigationItemDefinition(
                key: 'admin.dashboard',
                labelKey: 'admin.navigation.dashboard',
                routeName: 'admin.dashboard',
                area: 'admin',
                order: 10,
                icon: 'layout-dashboard',
                activeRoutePatterns: ['admin.dashboard'],
            ),
        ];
    }
}
