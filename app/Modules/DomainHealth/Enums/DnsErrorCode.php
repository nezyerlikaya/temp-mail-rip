<?php

namespace App\Modules\DomainHealth\Enums;

enum DnsErrorCode: string
{
    case Timeout = 'timeout';
    case NoRecords = 'no_records';
    case InvalidResponse = 'invalid_response';
    case ResolverUnavailable = 'resolver_unavailable';
    case UnsupportedEnvironment = 'unsupported_environment';
    case UnknownError = 'unknown_error';
}
