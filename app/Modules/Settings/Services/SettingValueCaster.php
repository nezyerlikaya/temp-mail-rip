<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Exceptions\InvalidSettingValueException;

class SettingValueCaster
{
    public function castForUse(SettingDefinition $definition, mixed $value): mixed
    {
        if ($value === null) {
            if ($definition->nullable) {
                return null;
            }

            throw InvalidSettingValueException::forKey($definition->key);
        }

        return match ($definition->type) {
            SettingType::String => is_string($value) ? $value : throw InvalidSettingValueException::forKey($definition->key),
            SettingType::Integer, SettingType::DurationSeconds => $this->castInteger($definition, $value),
            SettingType::Boolean => $this->castBoolean($definition, $value),
            SettingType::Decimal => $this->castDecimal($definition, $value),
            SettingType::Array => $this->castArray($definition, $value),
        };
    }

    public function serializeForStorage(SettingDefinition $definition, mixed $value): ?string
    {
        $cast = $this->castForUse($definition, $value);

        if ($cast === null) {
            return null;
        }

        return match ($definition->type) {
            SettingType::Boolean => $cast ? 'true' : 'false',
            SettingType::Array => json_encode($cast, JSON_THROW_ON_ERROR),
            default => (string) $cast,
        };
    }

    private function castInteger(SettingDefinition $definition, mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && preg_match('/^-?\d+$/', $value)) {
            return (int) $value;
        }

        throw InvalidSettingValueException::forKey($definition->key);
    }

    private function castBoolean(SettingDefinition $definition, mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return match (strtolower($value)) {
                'true', '1' => true,
                'false', '0' => false,
                default => throw InvalidSettingValueException::forKey($definition->key),
            };
        }

        if ($value === 1) {
            return true;
        }

        if ($value === 0) {
            return false;
        }

        throw InvalidSettingValueException::forKey($definition->key);
    }

    private function castDecimal(SettingDefinition $definition, mixed $value): string
    {
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_string($value) && preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            return $value;
        }

        throw InvalidSettingValueException::forKey($definition->key);
    }

    /**
     * @return array<mixed>
     */
    private function castArray(SettingDefinition $definition, mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || strlen($value) > 10000) {
            throw InvalidSettingValueException::forKey($definition->key);
        }

        try {
            $decoded = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw InvalidSettingValueException::forKey($definition->key);
        }

        if (! is_array($decoded)) {
            throw InvalidSettingValueException::forKey($definition->key);
        }

        return $decoded;
    }
}
