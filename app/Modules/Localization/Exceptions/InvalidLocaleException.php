<?php

namespace App\Modules\Localization\Exceptions;

class InvalidLocaleException extends LocaleException
{
    public static function forInput(string $locale): self
    {
        return new self("Invalid or unsupported locale [{$locale}].");
    }
}
