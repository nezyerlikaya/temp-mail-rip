<?php

namespace App\Modules\Navigation\DTOs;

readonly class ResolvedNavigationItem
{
    /**
     * @param  list<ResolvedNavigationItem>  $children
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $url,
        public bool $active,
        public int $order,
        public ?string $icon = null,
        public array $children = [],
    ) {}
}
