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
            new NavigationItemDefinition(
                key: 'public.legal.privacy_policy',
                labelKey: 'legal.navigation.privacy_policy',
                routeName: 'legal.privacy_policy',
                area: 'public',
                order: 900,
                icon: 'shield-check',
                activeRoutePatterns: ['legal.privacy_policy'],
            ),
            new NavigationItemDefinition(
                key: 'public.legal.terms_of_service',
                labelKey: 'legal.navigation.terms_of_service',
                routeName: 'legal.terms_of_service',
                area: 'public',
                order: 910,
                icon: 'file-text',
                activeRoutePatterns: ['legal.terms_of_service'],
            ),
            new NavigationItemDefinition(
                key: 'public.legal.cookie_policy',
                labelKey: 'legal.navigation.cookie_policy',
                routeName: 'legal.cookie_policy',
                area: 'public',
                order: 920,
                icon: 'cookie',
                activeRoutePatterns: ['legal.cookie_policy'],
            ),
            new NavigationItemDefinition(
                key: 'public.legal.acceptable_use_policy',
                labelKey: 'legal.navigation.acceptable_use_policy',
                routeName: 'legal.acceptable_use_policy',
                area: 'public',
                order: 930,
                icon: 'scale',
                activeRoutePatterns: ['legal.acceptable_use_policy'],
            ),
        ];
    }
}
