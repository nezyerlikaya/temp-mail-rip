<?php

namespace App\Modules\Compliance\Enums;

enum LegalDocumentStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';
}
