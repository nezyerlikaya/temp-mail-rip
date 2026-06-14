<?php

namespace App\Modules\Mail\Services;

use App\Modules\Mail\DTOs\EmailTemplateDefinition;
use App\Modules\Mail\Enums\EmailTemplateStatus;
use App\Modules\Mail\Exceptions\EmailHeaderInjectionException;
use App\Modules\Mail\Exceptions\InactiveEmailTemplateException;
use App\Modules\Mail\Exceptions\InvalidEmailPlaceholderException;

class EmailPlaceholderRenderer
{
    /**
     * @param  array<string, mixed>  $placeholders
     */
    public function renderSubject(EmailTemplateDefinition $template, array $placeholders, bool $strict = true): string
    {
        $subject = $this->render($template, $template->subject, $placeholders, $strict, true);

        if (preg_match('/[\r\n]/', $subject) === 1 || mb_strlen($subject) > 180) {
            throw EmailHeaderInjectionException::detected();
        }

        return $subject;
    }

    /**
     * @param  array<string, mixed>  $placeholders
     */
    public function renderBody(EmailTemplateDefinition $template, array $placeholders, bool $strict = true): string
    {
        $body = $this->render($template, $template->body, $placeholders, $strict, false);

        if (mb_strlen($body) > 200000) {
            throw new InvalidEmailPlaceholderException('Rendered email body exceeds the allowed size.');
        }

        return $body;
    }

    /**
     * @param  array<string, mixed>  $placeholders
     */
    private function render(EmailTemplateDefinition $template, string $content, array $placeholders, bool $strict, bool $headerContext): string
    {
        if ($template->status !== EmailTemplateStatus::Active) {
            throw InactiveEmailTemplateException::forTemplate($template->key);
        }

        $allowed = array_flip($template->placeholders);
        $usedInContent = array_flip($this->tokens($content));
        $normalized = [];

        foreach ($placeholders as $name => $value) {
            $normalizedName = strtolower(trim((string) $name));

            if (! preg_match('/^[a-z][a-z0-9_]*$/', $normalizedName)) {
                throw InvalidEmailPlaceholderException::unexpected((string) $name);
            }

            if ($strict && ! isset($allowed[$normalizedName])) {
                throw InvalidEmailPlaceholderException::unexpected($normalizedName);
            }

            $normalized[$normalizedName] = $this->safeValue(
                $normalizedName,
                $value,
                $headerContext && isset($usedInContent[$normalizedName]),
            );
        }

        foreach ($template->placeholders as $placeholder) {
            if (! array_key_exists($placeholder, $normalized)) {
                throw InvalidEmailPlaceholderException::missing($placeholder);
            }
        }

        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z][a-zA-Z0-9_]*)\s*\}\}/',
            fn (array $match): string => $normalized[strtolower($match[1])] ?? '',
            $content,
        ) ?? $content;
    }

    /**
     * @return list<string>
     */
    private function tokens(string $content): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z][a-zA-Z0-9_]*)\s*\}\}/', $content, $matches);

        return array_values(array_unique(array_map(
            fn (string $token): string => strtolower($token),
            $matches[1] ?? [],
        )));
    }

    private function safeValue(string $name, mixed $value, bool $headerContext): string
    {
        $string = is_scalar($value) ? (string) $value : '';

        if (mb_strlen($string) > 5000) {
            throw new InvalidEmailPlaceholderException("Email placeholder [{$name}] exceeds the allowed size.");
        }

        if (($headerContext || str_contains($name, 'subject')) && preg_match('/[\r\n]/', $string) === 1) {
            throw EmailHeaderInjectionException::detected();
        }

        if (str_ends_with($name, '_url') && ! $this->safeUrl($string)) {
            throw InvalidEmailPlaceholderException::unexpected($name);
        }

        return e($string);
    }

    private function safeUrl(string $url): bool
    {
        if ($url === '' || strlen($url) > 2048 || preg_match('/[\r\n]/', $url) === 1) {
            return false;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return in_array(strtolower((string) $scheme), ['http', 'https'], true);
    }
}
