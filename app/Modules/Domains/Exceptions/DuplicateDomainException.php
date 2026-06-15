<?php

namespace App\Modules\Domains\Exceptions;

class DuplicateDomainException extends DomainInventoryException
{
    public static function forDomain(string $domain): self
    {
        return new self("Domain [{$domain}] already exists.");
    }
}
