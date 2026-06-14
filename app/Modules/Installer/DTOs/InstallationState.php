<?php

namespace App\Modules\Installer\DTOs;

readonly class InstallationState
{
    /**
     * @param  list<PreflightCheckResult>  $checks
     */
    public function __construct(
        public bool $locked,
        public bool $complete,
        public array $checks,
    ) {}
}
