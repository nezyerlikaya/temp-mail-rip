<?php

namespace App\Models;

use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;
use Illuminate\Database\Eloquent\Model;

class DomainHealthSummary extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'domain_id',
        'current_status',
        'current_score',
        'last_checked_at',
        'last_success_at',
        'last_error_code',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'current_status' => DomainHealthStatus::class,
            'last_error_code' => DnsErrorCode::class,
            'last_checked_at' => 'immutable_datetime',
            'last_success_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
