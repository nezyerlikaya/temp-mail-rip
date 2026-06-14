<?php

namespace App\Modules\Translation\Enums;

enum TranslationStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Active = 'active';
    case Deprecated = 'deprecated';
}
