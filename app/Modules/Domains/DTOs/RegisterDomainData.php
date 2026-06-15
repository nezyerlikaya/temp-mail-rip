<?php

namespace App\Modules\Domains\DTOs;

use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;

readonly class RegisterDomainData
{
    public function __construct(
        public string $domain,
        public DomainStatus $status = DomainStatus::Pending,
        public DomainType $type = DomainType::Disposable,
        public bool $supportsCatchAll = false,
        public ?string $notes = null,
    ) {}
}
