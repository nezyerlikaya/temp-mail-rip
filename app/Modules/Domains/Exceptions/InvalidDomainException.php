<?php

namespace App\Modules\Domains\Exceptions;

class InvalidDomainException extends DomainInventoryException
{
    public static function forInput(string $reason): self
    {
        return new self("Domain input is invalid: {$reason}");
    }
}
