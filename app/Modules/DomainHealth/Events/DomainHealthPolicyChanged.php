<?php

namespace App\Modules\DomainHealth\Events;

readonly class DomainHealthPolicyChanged
{
    public function __construct(
        public string $settingKey,
    ) {}
}
