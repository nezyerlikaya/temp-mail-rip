<?php

namespace App\Modules\Compliance\Exceptions;

class UnsafeLegalContentException extends LegalDocumentException
{
    public static function forReason(string $reason): self
    {
        return new self("Legal document content is unsafe: {$reason}");
    }
}
