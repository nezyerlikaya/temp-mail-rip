<?php

namespace App\Modules\Installer\DTOs;

readonly class PreflightCheckResult
{
    public function __construct(
        public string $key,
        public string $label,
        public string $status,
        public string $message,
    ) {}

    public function blocksInstallation(): bool
    {
        return $this->status === 'blocker';
    }
}
