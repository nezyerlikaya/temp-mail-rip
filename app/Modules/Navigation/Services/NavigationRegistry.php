<?php

namespace App\Modules\Navigation\Services;

use App\Modules\Navigation\DTOs\NavigationItemDefinition;
use App\Modules\Navigation\Exceptions\DuplicateNavigationItemException;
use App\Modules\Navigation\Exceptions\InvalidNavigationHierarchyException;

class NavigationRegistry
{
    /**
     * @var array<string, NavigationItemDefinition>
     */
    private array $items = [];

    public function register(NavigationItemDefinition $item): void
    {
        if (isset($this->items[$item->key])) {
            throw DuplicateNavigationItemException::forKey($item->key);
        }

        $this->items[$item->key] = $item;
    }

    public function validate(): void
    {
        foreach ($this->items as $item) {
            if ($item->parentKey !== null && ! isset($this->items[$item->parentKey])) {
                throw new InvalidNavigationHierarchyException("Missing parent [{$item->parentKey}] for [{$item->key}].");
            }

            $this->assertNoCycle($item);
        }
    }

    /**
     * @return list<NavigationItemDefinition>
     */
    public function forArea(string $area): array
    {
        $items = array_filter($this->items, fn (NavigationItemDefinition $item): bool => $item->area === $area);
        usort($items, fn (NavigationItemDefinition $a, NavigationItemDefinition $b): int => [$a->order, $a->key] <=> [$b->order, $b->key]);

        return array_values($items);
    }

    private function assertNoCycle(NavigationItemDefinition $item): void
    {
        $seen = [];
        $cursor = $item;
        $depth = 0;

        while ($cursor->parentKey !== null) {
            if (++$depth > 5 || isset($seen[$cursor->key])) {
                throw new InvalidNavigationHierarchyException("Navigation hierarchy cycle or excessive depth at [{$item->key}].");
            }

            $seen[$cursor->key] = true;
            $cursor = $this->items[$cursor->parentKey];
        }
    }
}
