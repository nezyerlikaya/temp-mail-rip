<?php

namespace App\Modules\DomainHealth\Enums;

enum DomainHealthStatus: string
{
    case Unknown = 'unknown';
    case Healthy = 'healthy';
    case Warning = 'warning';
    case Degraded = 'degraded';
    case Failing = 'failing';
}
