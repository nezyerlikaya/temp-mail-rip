<?php

namespace App\Modules\Mail\Services;

use App\Modules\Mail\DTOs\EmailTemplateDefinition;
use App\Modules\Mail\Enums\EmailTemplateStatus;

class EmailTemplateDefinitionProvider
{
    /**
     * @return list<EmailTemplateDefinition>
     */
    public function definitions(): array
    {
        return [
            new EmailTemplateDefinition(
                key: 'account_welcome_preparation',
                purpose: 'Prepare the first account welcome email without sending it.',
                locale: 'en',
                version: 1,
                status: EmailTemplateStatus::Active,
                subject: 'Welcome to {{ platform_name }}',
                body: "Hello {{ display_name }},\n\nYour account preparation is ready for {{ platform_name }}.\n\n{{ action_url }}",
                placeholders: ['platform_name', 'display_name', 'action_url'],
            ),
            new EmailTemplateDefinition(
                key: 'email_verification_preparation',
                purpose: 'Prepare an email verification message without delivery orchestration.',
                locale: 'en',
                version: 1,
                status: EmailTemplateStatus::Active,
                subject: 'Verify your email for {{ platform_name }}',
                body: "Hello {{ display_name }},\n\nVerify your email using this link:\n{{ verification_url }}",
                placeholders: ['platform_name', 'display_name', 'verification_url'],
            ),
            new EmailTemplateDefinition(
                key: 'password_reset_preparation',
                purpose: 'Prepare a password reset email body without managing reset tokens.',
                locale: 'en',
                version: 1,
                status: EmailTemplateStatus::Active,
                subject: 'Reset your {{ platform_name }} password',
                body: "Hello {{ display_name }},\n\nUse this link to continue:\n{{ reset_url }}",
                placeholders: ['platform_name', 'display_name', 'reset_url'],
            ),
            new EmailTemplateDefinition(
                key: 'system_notification_preparation',
                purpose: 'Prepare a bounded system notification email.',
                locale: 'en',
                version: 1,
                status: EmailTemplateStatus::Active,
                subject: '{{ platform_name }} notification',
                body: "{{ message_title }}\n\n{{ message_body }}",
                placeholders: ['platform_name', 'message_title', 'message_body'],
            ),
            new EmailTemplateDefinition(
                key: 'contact_confirmation_preparation',
                purpose: 'Prepare contact confirmation without a contact center workflow.',
                locale: 'en',
                version: 1,
                status: EmailTemplateStatus::Active,
                subject: 'We received your message',
                body: "Hello {{ display_name }},\n\nYour message was received by {{ platform_name }}.",
                placeholders: ['platform_name', 'display_name'],
            ),
            new EmailTemplateDefinition(
                key: 'support_update_preparation',
                purpose: 'Prepare support update text without ticket orchestration.',
                locale: 'en',
                version: 1,
                status: EmailTemplateStatus::Active,
                subject: 'Support update: {{ reference }}',
                body: "Hello {{ display_name }},\n\n{{ update_summary }}\n\n{{ action_url }}",
                placeholders: ['display_name', 'reference', 'update_summary', 'action_url'],
            ),
        ];
    }
}
