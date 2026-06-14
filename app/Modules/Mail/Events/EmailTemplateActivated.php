<?php

namespace App\Modules\Mail\Events;

readonly class EmailTemplateActivated
{
    public function __construct(
        public string $key,
        public string $locale,
        public int $version,
    ) {}
}
