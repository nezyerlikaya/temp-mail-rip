<?php

namespace App\Modules\Mail\Events;

readonly class EmailTemplatePlaceholderContractChanged
{
    /**
     * @param  list<string>  $placeholders
     */
    public function __construct(
        public string $key,
        public string $locale,
        public int $version,
        public array $placeholders,
    ) {}
}
