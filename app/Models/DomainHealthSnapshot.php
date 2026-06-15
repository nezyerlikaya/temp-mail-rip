<?php

namespace App\Models;

use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;
use Illuminate\Database\Eloquent\Model;

class DomainHealthSnapshot extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'domain_id',
        'health_status',
        'health_score',
        'formula_version',
        'mx_present',
        'dns_visible',
        'error_code',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'health_status' => DomainHealthStatus::class,
            'error_code' => DnsErrorCode::class,
            'mx_present' => 'boolean',
            'dns_visible' => 'boolean',
            'checked_at' => 'immutable_datetime',
        ];
    }
}
