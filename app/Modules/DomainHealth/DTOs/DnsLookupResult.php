<?php

namespace App\Modules\DomainHealth\DTOs;

use App\Modules\DomainHealth\Enums\DnsErrorCode;

readonly class DnsLookupResult
{
    public function __construct(
        public bool $dnsVisible,
        public bool $mxPresent,
        public ?DnsErrorCode $errorCode = null,
    ) {}
}
