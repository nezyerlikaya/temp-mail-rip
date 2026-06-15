<?php

namespace App\Modules\DomainHealth\Exceptions;

class UnknownHealthDomainException extends DomainHealthException
{
    public static function forDomain(string $domain): self
    {
        return new self("Domain [{$domain}] is not present in Domain Inventory.");
    }
}
