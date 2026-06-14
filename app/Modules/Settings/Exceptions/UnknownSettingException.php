<?php

namespace App\Modules\Settings\Exceptions;

class UnknownSettingException extends SettingException
{
    public static function forKey(string $key): self
    {
        return new self("Unknown setting key [{$key}].");
    }
}
