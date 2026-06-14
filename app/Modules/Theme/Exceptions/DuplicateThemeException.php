<?php

namespace App\Modules\Theme\Exceptions;

class DuplicateThemeException extends ThemeException
{
    public static function forKey(string $key): self
    {
        return new self("Duplicate theme [{$key}].");
    }
}
