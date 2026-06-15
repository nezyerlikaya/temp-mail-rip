<?php

namespace App\Modules\Domains\Events;

use App\Modules\Domains\Enums\DomainStatus;

readonly class DomainStatusChanged
{
    public function __construct(
        public string $domain,
        public DomainStatus $status,
    ) {}
}
