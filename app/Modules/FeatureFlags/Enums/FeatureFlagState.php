<?php

namespace App\Modules\FeatureFlags\Enums;

enum FeatureFlagState: string
{
    case Enabled = 'enabled';
    case Disabled = 'disabled';
    case Beta = 'beta';
    case Deprecated = 'deprecated';
}
