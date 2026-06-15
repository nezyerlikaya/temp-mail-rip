<?php

namespace App\Modules\Domains\Enums;

enum DomainStatus: string
{
    case Active = 'active';
    case Disabled = 'disabled';
    case Pending = 'pending';
    case Retired = 'retired';

    public function usable(): bool
    {
        return $this === self::Active;
    }
}
