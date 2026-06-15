<?php

namespace Tests\Unit\DomainHealth;

use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Services\DnsReadinessResolver;
use PHPUnit\Framework\TestCase;

class DnsReadinessResolverTest extends TestCase
{
    public function test_dns_resolver_returns_safe_classified_result_without_raw_payloads(): void
    {
        $result = (new DnsReadinessResolver)->lookup('invalid-domain-for-temp-mail-test.invalid');

        $this->assertFalse($result->mxPresent);
        $this->assertContains($result->errorCode, [DnsErrorCode::NoRecords, DnsErrorCode::ResolverUnavailable, DnsErrorCode::UnsupportedEnvironment]);
    }
}
