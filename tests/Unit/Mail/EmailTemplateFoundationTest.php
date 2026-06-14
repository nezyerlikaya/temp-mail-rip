<?php

namespace Tests\Unit\Mail;

use App\Modules\Mail\DTOs\EmailTemplateDefinition;
use App\Modules\Mail\Enums\EmailTemplateStatus;
use App\Modules\Mail\Exceptions\DuplicateEmailTemplateException;
use App\Modules\Mail\Exceptions\EmailHeaderInjectionException;
use App\Modules\Mail\Exceptions\InactiveEmailTemplateException;
use App\Modules\Mail\Exceptions\InvalidEmailPlaceholderException;
use App\Modules\Mail\Services\EmailPlaceholderRenderer;
use App\Modules\Mail\Services\EmailTemplateDefinitionProvider;
use App\Modules\Mail\Services\EmailTemplateRegistry;
use App\Modules\Mail\Services\EmailTemplateResolver;
use Tests\TestCase;

class EmailTemplateFoundationTest extends TestCase
{
    public function test_email_templates_resolve_by_key_and_locale(): void
    {
        $registry = $this->registry();

        $template = $registry->active('email_verification_preparation', 'en');

        $this->assertSame(1, $template->version);
        $this->assertSame(EmailTemplateStatus::Active, $template->status);
        $this->assertSame(['platform_name', 'display_name', 'verification_url'], $template->placeholders);
    }

    public function test_duplicate_template_versions_are_rejected(): void
    {
        $template = new EmailTemplateDefinition(
            key: 'system_notification_preparation',
            purpose: 'Test',
            locale: 'en',
            version: 1,
            status: EmailTemplateStatus::Active,
            subject: 'Subject',
            body: 'Body {{ message }}',
            placeholders: ['message'],
        );

        $registry = new EmailTemplateRegistry;
        $registry->register($template);

        $this->expectException(DuplicateEmailTemplateException::class);

        $registry->register($template);
    }

    public function test_only_active_templates_render(): void
    {
        $template = new EmailTemplateDefinition(
            key: 'draft_template',
            purpose: 'Draft',
            locale: 'en',
            version: 1,
            status: EmailTemplateStatus::Draft,
            subject: 'Draft {{ name }}',
            body: 'Draft {{ name }}',
            placeholders: ['name'],
        );

        $this->expectException(InactiveEmailTemplateException::class);

        (new EmailPlaceholderRenderer)->renderSubject($template, ['name' => 'Alex']);
    }

    public function test_missing_and_unexpected_placeholders_fail_predictably(): void
    {
        $template = $this->registry()->active('contact_confirmation_preparation', 'en');
        $renderer = new EmailPlaceholderRenderer;

        try {
            $renderer->renderBody($template, ['platform_name' => 'Temp Mail']);
            $this->fail('Missing placeholder did not throw.');
        } catch (InvalidEmailPlaceholderException $exception) {
            $this->assertStringContainsString('display_name', $exception->getMessage());
        }

        $this->expectException(InvalidEmailPlaceholderException::class);

        $renderer->renderBody($template, [
            'platform_name' => 'Temp Mail',
            'display_name' => 'Alex',
            'extra' => 'nope',
        ]);
    }

    public function test_header_injection_is_prevented(): void
    {
        $template = $this->registry()->active('support_update_preparation', 'en');

        $this->expectException(EmailHeaderInjectionException::class);

        (new EmailPlaceholderRenderer)->renderSubject($template, [
            'display_name' => 'Alex',
            'reference' => "ABC\r\nBcc: attacker@example.test",
            'update_summary' => 'Ready',
            'action_url' => 'https://example.test/support',
        ]);
    }

    public function test_multiline_body_placeholder_does_not_trigger_subject_header_injection(): void
    {
        $template = $this->registry()->active('system_notification_preparation', 'en');

        $result = app(EmailTemplateResolver::class)->render('system_notification_preparation', 'en', [
            'platform_name' => 'Temp Mail',
            'message_title' => 'Status update',
            'message_body' => "Line one\nLine two",
        ]);

        $this->assertSame('Temp Mail notification', $result->subject);
        $this->assertStringContainsString('Line one', $result->body);
        $this->assertStringContainsString('Line two', $result->body);
    }

    public function test_rendered_diagnostics_do_not_expose_secrets_or_full_email_bodies(): void
    {
        $result = app(EmailTemplateResolver::class)->render('password_reset_preparation', 'en', [
            'platform_name' => 'Temp Mail',
            'display_name' => 'Alex',
            'reset_url' => 'https://example.test/reset?token=secret-token',
        ]);

        $diagnostics = json_encode($result->diagnostics, JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('placeholder_names', $diagnostics);
        $this->assertStringNotContainsString('secret-token', $diagnostics);
        $this->assertStringNotContainsString($result->body, $diagnostics);
        $this->assertStringNotContainsString($result->subject, $diagnostics);
    }

    private function registry(): EmailTemplateRegistry
    {
        $registry = new EmailTemplateRegistry;

        foreach ((new EmailTemplateDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        return $registry;
    }
}
