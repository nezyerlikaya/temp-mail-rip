<?php

namespace Tests\Unit\DomainHealth;

use App\Modules\DomainHealth\DTOs\DnsLookupResult;
use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;
use App\Modules\DomainHealth\Services\DomainHealthStatusCalculator;
use PHPUnit\Framework\TestCase;

class DomainHealthStatusCalculatorTest extends TestCase
{
    public function test_health_statuses_and_scores_calculate_deterministically(): void
    {
        $calculator = new DomainHealthStatusCalculator;

        $cases = [
            [new DnsLookupResult(true, true), DomainHealthStatus::Healthy, 100],
            [new DnsLookupResult(true, false, DnsErrorCode::NoRecords), DomainHealthStatus::Warning, 65],
            [new DnsLookupResult(false, false, DnsErrorCode::NoRecords), DomainHealthStatus::Degraded, 30],
            [new DnsLookupResult(false, false, DnsErrorCode::ResolverUnavailable), DomainHealthStatus::Unknown, 0],
            [new DnsLookupResult(false, false, DnsErrorCode::InvalidResponse), DomainHealthStatus::Failing, 10],
        ];

        foreach ($cases as [$result, $status, $score]) {
            $this->assertSame($status, $calculator->status($result));
            $this->assertSame($score, $calculator->score($result));
        }
    }
}
