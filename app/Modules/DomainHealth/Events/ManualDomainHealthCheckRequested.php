<?php

namespace App\Modules\DomainHealth\Events;

readonly class ManualDomainHealthCheckRequested
{
    public function __construct(
        public string $domain,
    ) {}
}
