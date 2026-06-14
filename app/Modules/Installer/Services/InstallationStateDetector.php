<?php

namespace App\Modules\Installer\Services;

use App\Modules\Installer\DTOs\InstallationState;

class InstallationStateDetector
{
    public function __construct(
        private readonly InstallationLock $lock,
        private readonly PreflightChecker $preflight,
    ) {}

    public function detect(): InstallationState
    {
        $checks = $this->preflight->run();
        $hasBlockers = false;

        foreach ($checks as $check) {
            if ($check->blocksInstallation()) {
                $hasBlockers = true;
                break;
            }
        }

        return new InstallationState(
            locked: $this->lock->locked(),
            complete: $this->lock->locked() && ! $hasBlockers,
            checks: $checks,
        );
    }
}
