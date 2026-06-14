<?php

namespace App\Modules\Theme\Exceptions;

class UnknownThemeException extends ThemeException
{
    public static function forKey(string $key): self
    {
        return new self("Unknown theme [{$key}].");
    }
}
