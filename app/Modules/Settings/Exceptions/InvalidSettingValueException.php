<?php

namespace App\Modules\Settings\Exceptions;

class InvalidSettingValueException extends SettingException
{
    public static function forKey(string $key): self
    {
        return new self("Invalid value for setting [{$key}].");
    }
}
