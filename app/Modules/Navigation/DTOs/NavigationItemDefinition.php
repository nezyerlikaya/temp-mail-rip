<?php

namespace App\Modules\Navigation\DTOs;

use InvalidArgumentException;

readonly class NavigationItemDefinition
{
    /**
     * @param  list<string>  $activeRoutePatterns
     */
    public function __construct(
        public string $key,
        public string $labelKey,
        public string $routeName,
        public string $area,
        public int $order = 100,
        public ?string $parentKey = null,
        public ?string $icon = null,
        public ?string $featureFlag = null,
        public array $activeRoutePatterns = [],
    ) {
        if (! preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)*$/', $this->key)) {
            throw new InvalidArgumentException('Navigation keys must use lowercase dot notation.');
        }

        if (! in_array($this->area, ['public', 'guest', 'user', 'admin'], true)) {
            throw new InvalidArgumentException('Navigation area is not supported.');
        }

        if ($this->icon !== null && ! preg_match('/^[a-z0-9-]+$/', $this->icon)) {
            throw new InvalidArgumentException('Navigation icons must use allow-listed identifier format.');
        }

        foreach ($this->activeRoutePatterns as $pattern) {
            if (! preg_match('/^[a-zA-Z0-9_.-]+\*?$/', $pattern)) {
                throw new InvalidArgumentException('Active route patterns must be bounded route-name patterns.');
            }
        }
    }
}
