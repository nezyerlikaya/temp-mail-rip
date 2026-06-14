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
        ];
    }
}
