<?php

namespace App\Modules\DomainHealth\DTOs;

use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;
use DateTimeImmutable;

readonly class DomainHealthSnapshotData
{
    public function __construct(
        public int|string $domainId,
        public string $domain,
        public DomainHealthStatus $status,
        public int $score,
        public string $formulaVersion,
        public bool $mxPresent,
        public bool $dnsVisible,
        public ?DnsErrorCode $errorCode,
        public DateTimeImmutable $checkedAt,
    ) {}
}
