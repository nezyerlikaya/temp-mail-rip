<?php

namespace App\Modules\SystemHealth\Exceptions;

class DuplicateHealthCheckException extends SystemHealthException
{
    public static function forKey(string $key): self
    {
        return new self("Health check [{$key}] is already registered.");
    }
}
