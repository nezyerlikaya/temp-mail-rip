<?php

namespace App\Modules\Compliance\Exceptions;

class UnknownLegalDocumentException extends LegalDocumentException
{
    public static function forType(string $type): self
    {
        return new self("Legal document type [{$type}] is not registered.");
    }
}
