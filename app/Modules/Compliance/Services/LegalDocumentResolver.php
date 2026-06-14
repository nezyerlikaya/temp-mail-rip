<?php

namespace App\Modules\Compliance\Services;

use App\Models\LegalDocument;
use App\Modules\Compliance\DTOs\PublishedLegalDocument;
use App\Modules\Compliance\Enums\LegalDocumentType;
use App\Modules\Compliance\Repositories\LegalDocumentRepository;
use App\Modules\Settings\Services\SettingsResolver;
use DateTimeImmutable;
use Throwable;

class LegalDocumentResolver
{
    public function __construct(
        private readonly LegalDocumentRepository $documents,
        private readonly LegalContentSanitizer $sanitizer,
        private readonly SettingsResolver $settings,
    ) {}

    public function published(LegalDocumentType $type, string $locale): ?PublishedLegalDocument
    {
        $document = $this->documents->currentPublished($type, $locale);
        $fallbackUsed = false;

        if ($document === null && $this->fallbackMode() === 'default_locale') {
            $fallbackLocale = $this->defaultLocale();

            if ($fallbackLocale !== $locale) {
                $document = $this->documents->currentPublished($type, $fallbackLocale);
                $fallbackUsed = $document !== null;
            }
        }

        return $document ? $this->fromModel($document, $fallbackUsed) : null;
    }

    public function publishedBySlug(string $slug, string $locale): ?PublishedLegalDocument
    {
        $document = $this->documents->currentPublishedBySlug($slug, $locale);
        $fallbackUsed = false;

        if ($document === null && $this->fallbackMode() === 'default_locale') {
            $fallbackLocale = $this->defaultLocale();

            if ($fallbackLocale !== $locale) {
                $document = $this->documents->currentPublishedBySlug($slug, $fallbackLocale);
                $fallbackUsed = $document !== null;
            }
        }

        return $document ? $this->fromModel($document, $fallbackUsed) : null;
    }

    private function fromModel(LegalDocument $document, bool $fallbackUsed): PublishedLegalDocument
    {
        return new PublishedLegalDocument(
            type: $document->document_type,
            locale: $document->locale_code,
            slug: $document->slug,
            version: (int) $document->version,
            title: $document->title,
            content: $document->content,
            safeHtml: $this->sanitizer->toSafeHtml($document->content),
            publishedAt: $document->published_at?->toDateTimeImmutable() ?? new DateTimeImmutable,
            effectiveAt: $document->effective_at?->toDateTimeImmutable(),
            fallbackUsed: $fallbackUsed,
        );
    }

    private function fallbackMode(): string
    {
        try {
            return (string) $this->settings->get('legal.fallback_mode');
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
