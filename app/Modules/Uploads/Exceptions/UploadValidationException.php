<?php

namespace App\Modules\Uploads\Exceptions;

class UploadValidationException extends UploadException
{
    public static function forReason(string $reason): self
    {
        return new self($reason);
    }
}
