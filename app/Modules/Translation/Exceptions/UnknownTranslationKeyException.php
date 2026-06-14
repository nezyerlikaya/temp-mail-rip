<?php

namespace App\Modules\Translation\Exceptions;

class UnknownTranslationKeyException extends TranslationException
{
    public static function forKey(string $key): self
    {
        return new self("Unknown translation key [{$key}].");
    }
}
