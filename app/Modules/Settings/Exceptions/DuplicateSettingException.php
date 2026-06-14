<?php

namespace App\Modules\Settings\Exceptions;

class DuplicateSettingException extends SettingException
{
    public static function forKey(string $key): self
    {
        return new self("Duplicate setting key [{$key}].");
    }
}
