<?php

namespace App\Modules\SystemHealth\Exceptions;

class UnknownHealthCheckException extends SystemHealthException
{
    public static function forKey(string $key): self
    {
        return new self("Health check [{$key}] is not registered.");
    }
}
