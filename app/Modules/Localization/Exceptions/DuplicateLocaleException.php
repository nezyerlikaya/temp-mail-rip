<?php

namespace App\Modules\Localization\Exceptions;

class DuplicateLocaleException extends LocaleException
{
    public static function forCode(string $code): self
    {
        return new self("Duplicate locale [{$code}].");
    }
}
