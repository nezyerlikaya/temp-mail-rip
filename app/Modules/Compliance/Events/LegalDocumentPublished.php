<?php

namespace App\Modules\Compliance\Events;

use App\Modules\Compliance\Enums\LegalDocumentType;

readonly class LegalDocumentPublished
{
    public function __construct(
        public LegalDocumentType $type,
        public string $locale,
        public int $version,
    ) {}
}
