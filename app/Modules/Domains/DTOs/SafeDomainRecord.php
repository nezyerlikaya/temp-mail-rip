<?php

namespace App\Modules\Domains\DTOs;

use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;

readonly class SafeDomainRecord
{
    public function __construct(
        public int|string|null $id,
        public string $domain,
        public ?string $displayDomain,
        public DomainStatus $status,
        public DomainType $type,
        public bool $supportsCatchAll,
    ) {}

    public function usable(): bool
    {
        return $this->status->usable();
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'domain' => $this->domain,
            'display_domain' => $this->displayDomain,
            'status' => $this->status->value,
            'domain_type' => $this->type->value,
            'supports_catch_all' => $this->supportsCatchAll,
            'usable' => $this->usable(),
        ];
    }
}
