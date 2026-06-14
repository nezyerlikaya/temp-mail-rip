<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Exceptions\DuplicateSettingException;
use App\Modules\Settings\Exceptions\UnknownSettingException;

class SettingsRegistry
{
    /**
     * @var array<string, SettingDefinition>
     */
    private array $definitions = [];

    public function register(SettingDefinition $definition): void
    {
        if (isset($this->definitions[$definition->key])) {
            throw DuplicateSettingException::forKey($definition->key);
        }

        $this->definitions[$definition->key] = $definition;
    }

    public function get(string $key): SettingDefinition
    {
        return $this->definitions[$key] ?? throw UnknownSettingException::forKey($key);
    }

    /**
     * @return array<string, SettingDefinition>
     */
    public function all(): array
    {
        return $this->definitions;
    }

    /**
     * @return array<string, SettingDefinition>
     */
    public function publicDefinitions(): array
    {
        return array_filter(
            $this->definitions,
            fn (SettingDefinition $definition): bool => $definition->isPublic && ! $definition->isSensitive,
        );
    }
}
