<?php

namespace App\Modules\Compliance\DTOs;

use App\Modules\Compliance\Enums\LegalDocumentType;
use InvalidArgumentException;

readonly class LegalDocumentDefinition
{
    public function __construct(
        public LegalDocumentType $type,
        public string $defaultSlug,
        public string $labelKey,
        public string $routeName,
        public bool $requiredForV1 = true,
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->defaultSlug)) {
            throw new InvalidArgumentException('Legal document slugs must be lowercase URL-safe segments.');
        }

        if (! str_starts_with($this->routeName, 'legal.')) {
            throw new InvalidArgumentException('Legal document routes must use the legal route namespace.');
        }
    }
}
