<?php

namespace App\Models;

use App\Modules\Compliance\Enums\LegalDocumentStatus;
use App\Modules\Compliance\Enums\LegalDocumentType;
use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    protected $fillable = [
        'document_type',
        'slug',
        'status',
        'version',
        'locale_code',
        'title',
        'content',
        'published_at',
        'effective_at',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => LegalDocumentType::class,
            'status' => LegalDocumentStatus::class,
            'published_at' => 'immutable_datetime',
            'effective_at' => 'immutable_datetime',
        ];
    }
}
