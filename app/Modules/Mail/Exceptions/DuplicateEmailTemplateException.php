<?php

namespace App\Modules\Mail\Exceptions;

class DuplicateEmailTemplateException extends EmailTemplateException
{
    public static function forTemplate(string $key, string $locale, int $version): self
    {
        return new self("Email template [{$key}] locale [{$locale}] version [{$version}] is already registered.");
    }
}
