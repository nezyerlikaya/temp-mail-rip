<?php

namespace App\Modules\Mail\Services;

use App\Modules\Mail\DTOs\RenderedEmailTemplate;
use App\Modules\Mail\Exceptions\UnknownEmailTemplateException;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Settings\Services\SettingsResolver;
use Throwable;

class EmailTemplateResolver
{
    public function __construct(
        private readonly EmailTemplateRegistry $registry,
        private readonly EmailPlaceholderRenderer $renderer,
        private readonly SettingsResolver $settings,
        private readonly SafeDiagnosticsFormatter $diagnostics,
    ) {}

    /**
     * @param  array<string, mixed>  $placeholders
     */
    public function render(string $key, string $locale, array $placeholders, bool $strict = true): RenderedEmailTemplate
    {
        $fallbackUsed = false;

        try {
            $template = $this->registry->active($key, $locale);
        } catch (UnknownEmailTemplateException $exception) {
            if ($this->fallbackMode() !== 'default_locale') {
                throw $exception;
            }

            $fallbackLocale = $this->defaultLocale();

            if ($fallbackLocale === $locale) {
                throw $exception;
            }

            $template = $this->registry->active($key, $fallbackLocale);
            $fallbackUsed = true;
        }

        $subject = $this->renderer->renderSubject($template, $placeholders, $strict);
        $body = $this->renderer->renderBody($template, $placeholders, $strict);

        return new RenderedEmailTemplate(
            key: $template->key,
            locale: $template->locale,
            version: $template->version,
            subject: $subject,
            body: $body,
            fallbackUsed: $fallbackUsed,
            diagnostics: $this->safeDiagnostics($template->key, $template->locale, $template->version, $placeholders, $fallbackUsed),
        );
    }

    /**
     * @param  array<string, mixed>  $placeholders
     * @return array<string, mixed>
     */
    private function safeDiagnostics(string $key, string $locale, int $version, array $placeholders, bool $fallbackUsed): array
    {
        return $this->diagnostics->format([
            'template_key' => $key,
            'locale' => $locale,
            'version' => $version,
            'fallback_used' => $fallbackUsed,
            'placeholder_names' => array_values(array_map('strval', array_keys($placeholders))),
        ]);
    }

    private function fallbackMode(): string
    {
        try {
            return (string) $this->settings->get('mail.template_fallback_mode');
        } catch (Throwable) {
            return 'default_locale';
        }
    }

    private function defaultLocale(): string
    {
        try {
            return (string) $this->settings->get('localization.default_locale');
        } catch (Throwable) {
            return 'en';
        }
    }
}
