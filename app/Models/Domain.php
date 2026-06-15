<?php

namespace App\Models;

use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'display_domain',
        'status',
        'domain_type',
        'supports_catch_all',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => DomainStatus::class,
            'domain_type' => DomainType::class,
            'supports_catch_all' => 'boolean',
        ];
    }
}
