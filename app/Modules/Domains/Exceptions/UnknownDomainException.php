<?php

namespace App\Modules\Domains\Exceptions;

class UnknownDomainException extends DomainInventoryException
{
    public static function forDomain(string $domain): self
    {
        return new self("Domain [{$domain}] was not found.");
    }
}
