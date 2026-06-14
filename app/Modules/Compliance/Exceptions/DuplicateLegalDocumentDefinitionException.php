<?php

namespace App\Modules\Compliance\Exceptions;

class DuplicateLegalDocumentDefinitionException extends LegalDocumentException
{
    public static function forType(string $type): self
    {
        return new self("Legal document definition [{$type}] is already registered.");
    }
}
