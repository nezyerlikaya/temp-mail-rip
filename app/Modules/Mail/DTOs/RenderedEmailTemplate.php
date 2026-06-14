<?php

namespace App\Modules\Mail\DTOs;

readonly class RenderedEmailTemplate
{
    /**
     * @param  array<string, mixed>  $diagnostics
     */
    public function __construct(
        public string $key,
        public string $locale,
        public int $version,
        public string $subject,
        public string $body,
        public bool $fallbackUsed,
        public array $diagnostics,
    ) {}
}
