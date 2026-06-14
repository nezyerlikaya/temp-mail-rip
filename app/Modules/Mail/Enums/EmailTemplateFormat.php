<?php

namespace App\Modules\Mail\Enums;

enum EmailTemplateFormat: string
{
    case Text = 'text';
    case Html = 'html';
}
