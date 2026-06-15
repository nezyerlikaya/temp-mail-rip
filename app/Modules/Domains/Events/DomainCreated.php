<?php

namespace App\Modules\Domains\Events;

readonly class DomainCreated
{
    public function __construct(
        public string $domain,
    ) {}
}
