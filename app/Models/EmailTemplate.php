<?php

namespace App\Models;

use App\Modules\Mail\Enums\EmailTemplateFormat;
use App\Modules\Mail\Enums\EmailTemplateStatus;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'template_key',
        'locale_code',
        'version',
        'status',
        'subject',
        'body',
        'format',
        'placeholder_schema',
        'purpose',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EmailTemplateStatus::class,
            'format' => EmailTemplateFormat::class,
            'placeholder_schema' => 'array',
            'activated_at' => 'immutable_datetime',
        ];
    }
}
