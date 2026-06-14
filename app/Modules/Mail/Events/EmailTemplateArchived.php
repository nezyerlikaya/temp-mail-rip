<?php

namespace App\Modules\Mail\Events;

readonly class EmailTemplateArchived
{
    public function __construct(
        public string $key,
        public string $locale,
        public int $version,
    ) {}
}
