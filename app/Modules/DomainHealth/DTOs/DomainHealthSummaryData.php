<?php

namespace App\Modules\DomainHealth\DTOs;

use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;
use DateTimeImmutable;

readonly class DomainHealthSummaryData
{
    public function __construct(
        public int|string $domainId,
        public DomainHealthStatus $status,
        public int $score,
        public ?DateTimeImmutable $lastCheckedAt,
        public ?DateTimeImmutable $lastSuccessAt,
        public ?DnsErrorCode $lastErrorCode,
    ) {}
}
