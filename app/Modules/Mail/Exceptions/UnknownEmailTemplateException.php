<?php

namespace App\Modules\Mail\Exceptions;

class UnknownEmailTemplateException extends EmailTemplateException
{
    public static function forTemplate(string $key, string $locale): self
    {
        return new self("Active email template [{$key}] for locale [{$locale}] was not found.");
    }
}
