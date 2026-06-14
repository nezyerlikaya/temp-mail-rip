<?php

namespace App\Modules\Settings\DTOs;

use App\Modules\Settings\Enums\SettingType;
use InvalidArgumentException;

readonly class SettingDefinition
{
    /**
     * @param  array<string, mixed>  $validationRules
     */
    public function __construct(
        public string $key,
        public SettingType $type,
        public mixed $default,
        public array $validationRules,
        public bool $isSensitive = false,
        public bool $isPublic = false,
        public bool $nullable = false,
        public ?string $description = null,
        public ?string $group = null,
    ) {
        if ($this->key === '' || strlen($this->key) > 160 || ! preg_match('/^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9_]*)+$/', $this->key)) {
            throw new InvalidArgumentException('Setting keys must use dot notation and lowercase module-owned segments.');
        }

        if ($this->isSensitive && $this->isPublic) {
            throw new InvalidArgumentException('Sensitive settings cannot be public.');
        }

        if ($this->default === null && ! $this->nullable) {
            throw new InvalidArgumentException('Non-nullable settings must define a default value.');
        }

        if (! array_key_exists('value', $this->validationRules)) {
            throw new InvalidArgumentException('Setting definitions must include validation rules for value.');
        }
    }
}
