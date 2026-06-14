<?php

namespace App\Modules\Settings\Enums;

enum SettingType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Boolean = 'boolean';
    case Decimal = 'decimal';
    case Array = 'array';
    case DurationSeconds = 'duration_seconds';
}
