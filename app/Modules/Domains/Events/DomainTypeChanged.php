<?php

namespace App\Modules\Domains\Events;

use App\Modules\Domains\Enums\DomainType;

readonly class DomainTypeChanged
{
    public function __construct(
        public string $domain,
        public DomainType $type,
    ) {}
}
