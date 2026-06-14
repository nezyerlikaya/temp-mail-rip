<?php

namespace App\Modules\Compliance\Exceptions;

class ImmutablePublishedLegalDocumentException extends LegalDocumentException
{
    public static function forDocument(int|string $id): self
    {
        return new self("Published legal document [{$id}] cannot be overwritten; create a new version.");
    }
}
