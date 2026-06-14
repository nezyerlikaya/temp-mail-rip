<?php

namespace App\Modules\Compliance\Services;

use App\Modules\Compliance\DTOs\LegalDocumentDefinition;
use App\Modules\Compliance\Enums\LegalDocumentType;

class LegalDocumentDefinitionProvider
{
    /**
     * @return list<LegalDocumentDefinition>
     */
    public function definitions(): array
    {
        return [
            new LegalDocumentDefinition(
                type: LegalDocumentType::PrivacyPolicy,
                defaultSlug: 'privacy-policy',
                labelKey: 'legal.navigation.privacy_policy',
                routeName: 'legal.privacy_policy',
            ),
            new LegalDocumentDefinition(
                type: LegalDocumentType::TermsOfService,
                defaultSlug: 'terms-of-service',
                labelKey: 'legal.navigation.terms_of_service',
                routeName: 'legal.terms_of_service',
            ),
            new LegalDocumentDefinition(
                type: LegalDocumentType::CookiePolicy,
                defaultSlug: 'cookie-policy',
                labelKey: 'legal.navigation.cookie_policy',
                routeName: 'legal.cookie_policy',
            ),
            new LegalDocumentDefinition(
                type: LegalDocumentType::AcceptableUsePolicy,
                defaultSlug: 'acceptable-use-policy',
                labelKey: 'legal.navigation.acceptable_use_policy',
                routeName: 'legal.acceptable_use_policy',
            ),
        ];
    }
}
