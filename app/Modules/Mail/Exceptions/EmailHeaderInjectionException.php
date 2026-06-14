<?php

namespace App\Modules\Mail\Exceptions;

class EmailHeaderInjectionException extends EmailTemplateException
{
    public static function detected(): self
    {
        return new self('Email subject/header value contains CRLF injection characters.');
    }
}
