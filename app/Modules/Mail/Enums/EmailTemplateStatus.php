<?php

namespace App\Modules\Mail\Enums;

enum EmailTemplateStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Active = 'active';
    case Archived = 'archived';
}
