<?php

namespace App\Modules\Mail\Exceptions;

class InvalidEmailPlaceholderException extends EmailTemplateException
{
    public static function missing(string $placeholder): self
    {
        return new self("Required email placeholder [{$placeholder}] is missing.");
    }

    public static function unexpected(string $placeholder): self
    {
        return new self("Unexpected email placeholder [{$placeholder}] was provided.");
    }
}
