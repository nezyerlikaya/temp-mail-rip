<?php

namespace App\Modules\DomainHealth\Services;

use App\Modules\DomainHealth\DTOs\DnsLookupResult;
use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;

class DomainHealthStatusCalculator
{
    public const FORMULA_VERSION = 'dns-mx-v1';

    public function status(DnsLookupResult $result): DomainHealthStatus
    {
        if ($result->mxPresent && $result->dnsVisible && $result->errorCode === null) {
            return DomainHealthStatus::Healthy;
        }

        if ($result->dnsVisible && ! $result->mxPresent) {
            return DomainHealthStatus::Warning;
        }

        return match ($result->errorCode) {
            DnsErrorCode::NoRecords => DomainHealthStatus::Degraded,
            DnsErrorCode::UnsupportedEnvironment, DnsErrorCode::ResolverUnavailable, DnsErrorCode::Timeout => DomainHealthStatus::Unknown,
            DnsErrorCode::InvalidResponse, DnsErrorCode::UnknownError, null => DomainHealthStatus::Failing,
        };
    }

    public function score(DnsLookupResult $result): int
    {
        if ($result->mxPresent && $result->dnsVisible && $result->errorCode === null) {
            return 100;
        }

        if ($result->dnsVisible && ! $result->mxPresent) {
            return 65;
        }

        return match ($result->errorCode) {
            DnsErrorCode::NoRecords => 30,
            DnsErrorCode::UnsupportedEnvironment, DnsErrorCode::ResolverUnavailable, DnsErrorCode::Timeout => 0,
            DnsErrorCode::InvalidResponse, DnsErrorCode::UnknownError, null => 10,
        };
    }
}
