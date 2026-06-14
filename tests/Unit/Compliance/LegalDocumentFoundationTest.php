<?php

namespace Tests\Unit\Compliance;

use App\Models\LegalDocument;
use App\Modules\Compliance\DTOs\LegalDocumentDefinition;
use App\Modules\Compliance\Enums\LegalDocumentStatus;
use App\Modules\Compliance\Enums\LegalDocumentType;
use App\Modules\Compliance\Exceptions\DuplicateLegalDocumentDefinitionException;
use App\Modules\Compliance\Exceptions\ImmutablePublishedLegalDocumentException;
use App\Modules\Compliance\Exceptions\UnsafeLegalContentException;
use App\Modules\Compliance\Repositories\LegalDocumentRepository;
use App\Modules\Compliance\Services\LegalContentSanitizer;
use App\Modules\Compliance\Services\LegalDocumentDefinitionProvider;
use App\Modules\Compliance\Services\LegalDocumentRegistry;
use PHPUnit\Framework\TestCase;

class LegalDocumentFoundationTest extends TestCase
{
    public function test_legal_document_types_register_with_deterministic_slugs_and_routes(): void
    {
        $registry = new LegalDocumentRegistry;

        foreach ((new LegalDocumentDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        $definitions = $registry->requiredForV1();

        $this->assertSame([
            'privacy-policy',
            'terms-of-service',
            'cookie-policy',
            'acceptable-use-policy',
        ], array_map(fn (LegalDocumentDefinition $definition): string => $definition->defaultSlug, $definitions));

        $this->assertSame('legal.privacy_policy', $registry->get(LegalDocumentType::PrivacyPolicy)->routeName);
        $this->assertSame(LegalDocumentType::TermsOfService, $registry->forRoute('legal.terms_of_service')->type);
    }

    public function test_duplicate_legal_document_type_registration_fails(): void
    {
        $definition = new LegalDocumentDefinition(
            type: LegalDocumentType::PrivacyPolicy,
            defaultSlug: 'privacy-policy',
            labelKey: 'legal.navigation.privacy_policy',
            routeName: 'legal.privacy_policy',
        );

        $registry = new LegalDocumentRegistry;
        $registry->register($definition);

        $this->expectException(DuplicateLegalDocumentDefinitionException::class);

        $registry->register($definition);
    }

    public function test_legal_content_is_sanitized_and_external_links_are_safe(): void
    {
        $html = (new LegalContentSanitizer)->toSafeHtml("# Privacy\n\nRead [terms](https://example.com/terms) and **continue**.");

        $this->assertStringContainsString('<h1>Privacy</h1>', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
        $this->assertStringContainsString('<strong>continue</strong>', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_unsafe_legal_html_is_rejected(): void
    {
        $this->expectException(UnsafeLegalContentException::class);

        (new LegalContentSanitizer)->toSafeHtml('Click <a href="javascript:alert(1)" onclick="x()">bad</a>');
    }

    public function test_published_legal_document_content_cannot_be_overwritten(): void
    {
        $document = new LegalDocument;
        $document->exists = true;
        $document->setRawAttributes([
            'id' => 10,
            'document_type' => LegalDocumentType::PrivacyPolicy->value,
            'status' => LegalDocumentStatus::Published->value,
            'content' => 'Published version',
            'updated_at' => now(),
        ], true);
        $document->content = 'Changed version';

        $this->expectException(ImmutablePublishedLegalDocumentException::class);

        (new LegalDocumentRepository)->assertPublishedIsImmutable($document);
    }
}
