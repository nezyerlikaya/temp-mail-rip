<?php

namespace App\Modules\Domains\Events;

readonly class DomainRetired
{
    public function __construct(
        public string $domain,
    ) {}
}
