<?php

namespace App\Modules\Compliance\Repositories;

use App\Models\LegalDocument;
use App\Modules\Compliance\Enums\LegalDocumentStatus;
use App\Modules\Compliance\Enums\LegalDocumentType;
use App\Modules\Compliance\Exceptions\ImmutablePublishedLegalDocumentException;
use Illuminate\Support\Facades\DB;

class LegalDocumentRepository
{
    public function currentPublished(LegalDocumentType $type, string $locale): ?LegalDocument
    {
        return LegalDocument::query()
            ->where('document_type', $type->value)
            ->where('locale_code', $locale)
            ->where('status', LegalDocumentStatus::Published->value)
            ->whereNotNull('published_at')
            ->where(function ($query): void {
                $query->whereNull('effective_at')->orWhere('effective_at', '<=', now());
            })
            ->orderByDesc('effective_at')
            ->orderByDesc('published_at')
            ->first();
    }

    public function currentPublishedBySlug(string $slug, string $locale): ?LegalDocument
    {
        return LegalDocument::query()
            ->where('slug', $slug)
            ->where('locale_code', $locale)
            ->where('status', LegalDocumentStatus::Published->value)
            ->whereNotNull('published_at')
            ->where(function ($query): void {
                $query->whereNull('effective_at')->orWhere('effective_at', '<=', now());
            })
            ->orderByDesc('effective_at')
            ->orderByDesc('published_at')
            ->first();
    }

    public function assertPublishedIsImmutable(LegalDocument $document): void
    {
        if (! $document->exists || $document->status !== LegalDocumentStatus::Published || ! $document->isDirty()) {
            return;
        }

        $immutable = array_diff(array_keys($document->getDirty()), ['updated_at']);

        if ($immutable !== []) {
            throw ImmutablePublishedLegalDocumentException::forDocument($document->getKey() ?? 'unsaved');
        }
    }

    public function publish(LegalDocument $document): LegalDocument
    {
        return DB::transaction(function () use ($document): LegalDocument {
            $document->status = LegalDocumentStatus::Published;
            $document->published_at ??= now();
            $document->effective_at ??= $document->published_at;

            LegalDocument::query()
                ->where('document_type', $document->document_type->value)
                ->where('locale_code', $document->locale_code)
                ->where('status', LegalDocumentStatus::Published->value)
                ->whereKeyNot($document->getKey())
                ->update(['status' => LegalDocumentStatus::Archived->value]);

            $document->save();

            return $document;
        });
    }
}
