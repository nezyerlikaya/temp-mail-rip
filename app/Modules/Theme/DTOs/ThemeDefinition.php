<?php

namespace App\Modules\Theme\DTOs;

use InvalidArgumentException;

readonly class ThemeDefinition
{
    /**
     * @param  array<string, string>  $tokens
     */
    public function __construct(
        public string $key,
        public string $mode,
        public array $tokens,
        public bool $isDefault = false,
    ) {
        if (! preg_match('/^[a-z][a-z0-9_-]*$/', $this->key)) {
            throw new InvalidArgumentException('Theme keys must be stable identifiers.');
        }

        if (! in_array($this->mode, ['light', 'dark', 'system'], true)) {
            throw new InvalidArgumentException('Theme mode is unsupported.');
        }

        foreach ($this->tokens as $name => $value) {
            if (! preg_match('/^[a-z][a-z0-9-]*$/', $name) || ! preg_match('/^[#(),.%\/\sa-zA-Z0-9-]+$/', $value) || str_contains(strtolower($value), 'javascript')) {
                throw new InvalidArgumentException('Theme tokens must be safe CSS values.');
            }
        }
    }
}
