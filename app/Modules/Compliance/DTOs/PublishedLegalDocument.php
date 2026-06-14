<?php

namespace App\Modules\Compliance\DTOs;

use App\Modules\Compliance\Enums\LegalDocumentType;
use DateTimeImmutable;

readonly class PublishedLegalDocument
{
    public function __construct(
        public LegalDocumentType $type,
        public string $locale,
        public string $slug,
        public int $version,
        public string $title,
        public string $content,
        public string $safeHtml,
        public ?DateTimeImmutable $publishedAt,
        public ?DateTimeImmutable $effectiveAt,
        public bool $fallbackUsed = false,
    ) {}
}
