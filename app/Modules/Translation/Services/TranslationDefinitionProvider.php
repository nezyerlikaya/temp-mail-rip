<?php

namespace App\Modules\Translation\Services;

use App\Modules\Translation\DTOs\TranslationDefinition;

class TranslationDefinitionProvider
{
    /**
     * @return list<TranslationDefinition>
     */
    public function definitions(): array
    {
        return [
            new TranslationDefinition('system', 'app.name'),
            new TranslationDefinition('system', 'missing_key', ['key']),
            new TranslationDefinition('navigation', 'home'),
            new TranslationDefinition('legal', 'navigation.privacy_policy'),
            new TranslationDefinition('legal', 'navigation.terms_of_service'),
            new TranslationDefinition('legal', 'navigation.cookie_policy'),
            new TranslationDefinition('legal', 'navigation.acceptable_use_policy'),
            new TranslationDefinition('admin', 'navigation.dashboard'),
            new TranslationDefinition('admin', 'dashboard.title'),
            new TranslationDefinition('admin', 'dashboard.foundation_status'),
            new TranslationDefinition('admin', 'shell.skip_to_content'),
            new TranslationDefinition('admin', 'shell.primary_navigation'),
            new TranslationDefinition('admin', 'shell.temporary_access_notice'),
            new TranslationDefinition('admin', 'empty.title'),
            new TranslationDefinition('admin', 'empty.description'),
            new TranslationDefinition('auth', 'login'),
            new TranslationDefinition('auth', 'logout'),
            new TranslationDefinition('mailboxes', 'create.success', ['address']),
        ];
    }
}
