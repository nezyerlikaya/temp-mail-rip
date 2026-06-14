<?php

namespace App\Modules\Mail\Services;

use App\Modules\Mail\DTOs\EmailTemplateDefinition;
use App\Modules\Mail\Enums\EmailTemplateStatus;
use App\Modules\Mail\Exceptions\DuplicateEmailTemplateException;
use App\Modules\Mail\Exceptions\UnknownEmailTemplateException;

class EmailTemplateRegistry
{
    /**
     * @var array<string, EmailTemplateDefinition>
     */
    private array $templates = [];

    public function register(EmailTemplateDefinition $template): void
    {
        $id = $this->id($template->key, $template->locale, $template->version);

        if (isset($this->templates[$id])) {
            throw DuplicateEmailTemplateException::forTemplate($template->key, $template->locale, $template->version);
        }

        $this->templates[$id] = $template;
    }

    public function active(string $key, string $locale): EmailTemplateDefinition
    {
        $candidates = array_filter(
            $this->templates,
            fn (EmailTemplateDefinition $template): bool => $template->key === $key
                && $template->locale === $locale
                && $template->status === EmailTemplateStatus::Active,
        );

        usort($candidates, fn (EmailTemplateDefinition $a, EmailTemplateDefinition $b): int => $b->version <=> $a->version);

        return $candidates[0] ?? throw UnknownEmailTemplateException::forTemplate($key, $locale);
    }

    /**
     * @return list<EmailTemplateDefinition>
     */
    public function all(): array
    {
        return array_values($this->templates);
    }

    private function id(string $key, string $locale, int $version): string
    {
        return "{$key}:{$locale}:{$version}";
    }
}
