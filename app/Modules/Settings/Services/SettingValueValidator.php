<?php

namespace App\Modules\Settings\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Exceptions\InvalidSettingValueException;
use Illuminate\Support\Facades\Validator;

class SettingValueValidator
{
    public function __construct(private readonly SettingValueCaster $caster) {}

    public function validate(SettingDefinition $definition, mixed $value): mixed
    {
        $cast = $this->caster->castForUse($definition, $value);
        $validator = Validator::make(['value' => $cast], $definition->validationRules);

        if ($validator->fails()) {
            throw InvalidSettingValueException::forKey($definition->key);
        }

        return $cast;
    }
}
