<?php

namespace App\Modules\Translation\Exceptions;

class DuplicateTranslationKeyException extends TranslationException
{
    public static function forKey(string $key): self
    {
        return new self("Duplicate translation key [{$key}].");
    }
}
