<?php

namespace App\Modules\Localization\DTOs;

use App\Modules\Localization\Enums\LocaleStatus;
use App\Modules\Localization\Enums\TextDirection;
use InvalidArgumentException;

readonly class LocaleDefinition
{
    public function __construct(
        public string $code,
        public string $name,
        public string $nativeName,
        public TextDirection $direction,
        public LocaleStatus $status,
        public bool $isDefault = false,
        public ?string $fallbackLocale = null,
    ) {
        if ($this->code === '' || $this->name === '' || $this->nativeName === '') {
            throw new InvalidArgumentException('Locale definitions require code, name, and native name.');
        }

        if ($this->status === LocaleStatus::Deprecated && $this->fallbackLocale === null) {
            throw new InvalidArgumentException('Deprecated locales require a fallback locale.');
        }
    }

    public function selectable(): bool
    {
        return $this->status === LocaleStatus::Active;
    }

    public function resolvable(): bool
    {
        return in_array($this->status, [LocaleStatus::Active, LocaleStatus::Hidden], true);
    }
}
