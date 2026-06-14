<?php

namespace App\Modules\Compliance\Enums;

enum LegalDocumentType: string
{
    case PrivacyPolicy = 'privacy_policy';
    case TermsOfService = 'terms_of_service';
    case CookiePolicy = 'cookie_policy';
    case AcceptableUsePolicy = 'acceptable_use_policy';
    case DataProcessingAddendum = 'data_processing_addendum';
}
