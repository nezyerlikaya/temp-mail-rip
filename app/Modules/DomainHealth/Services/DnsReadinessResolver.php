<?php

namespace App\Modules\DomainHealth\Services;

use App\Modules\DomainHealth\DTOs\DnsLookupResult;
use App\Modules\DomainHealth\Enums\DnsErrorCode;
use Throwable;

class DnsReadinessResolver
{
    public function lookup(string $canonicalDomain): DnsLookupResult
    {
        if (! function_exists('checkdnsrr')) {
            return new DnsLookupResult(false, false, DnsErrorCode::UnsupportedEnvironment);
        }

        try {
            $mxPresent = checkdnsrr($canonicalDomain, 'MX');
            $dnsVisible = $mxPresent || checkdnsrr($canonicalDomain, 'A') || checkdnsrr($canonicalDomain, 'AAAA');
        } catch (Throwable) {
            return new DnsLookupResult(false, false, DnsErrorCode::ResolverUnavailable);
        }

        if (! $dnsVisible) {
            return new DnsLookupResult(false, false, DnsErrorCode::NoRecords);
        }

        if (! $mxPresent) {
            return new DnsLookupResult(true, false, DnsErrorCode::NoRecords);
        }

        return new DnsLookupResult(true, true);
    }
}
