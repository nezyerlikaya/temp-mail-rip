<?php

namespace App\Modules\Mail\Exceptions;

class InactiveEmailTemplateException extends EmailTemplateException
{
    public static function forTemplate(string $key): self
    {
        return new self("Email template [{$key}] is not active and cannot be rendered.");
    }
}
