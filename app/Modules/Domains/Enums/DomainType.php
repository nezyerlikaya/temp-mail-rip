<?php

namespace App\Modules\Domains\Enums;

enum DomainType: string
{
    case System = 'system';
    case Disposable = 'disposable';
    case Premium = 'premium';
}
