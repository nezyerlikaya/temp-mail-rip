<?php

namespace App\Modules\DomainHealth\Events;

readonly class DomainHealthSummaryReset
{
    public function __construct(
        public int|string $domainId,
    ) {}
}
