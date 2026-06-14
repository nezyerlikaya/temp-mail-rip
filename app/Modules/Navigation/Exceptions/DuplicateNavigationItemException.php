<?php

namespace App\Modules\Navigation\Exceptions;

class DuplicateNavigationItemException extends NavigationException
{
    public static function forKey(string $key): self
    {
        return new self("Duplicate navigation item [{$key}].");
    }
}
