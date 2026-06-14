<?php

namespace Tests\Feature\Legal;

use App\Modules\Compliance\DTOs\PublishedLegalDocument;
use App\Modules\Compliance\Enums\LegalDocumentType;
use App\Modules\Compliance\Services\LegalDocumentResolver;
use DateTimeImmutable;
use Mockery;
use Tests\TestCase;

class LegalPageRouteTest extends TestCase
{
    public function test_public_legal_route_renders_only_resolved_published_document(): void
    {
        $resolver = Mockery::mock(LegalDocumentResolver::class);
        $resolver->shouldReceive('published')
            ->once()
            ->with(LegalDocumentType::PrivacyPolicy, 'en')
            ->andReturn(new PublishedLegalDocument(
                type: LegalDocumentType::PrivacyPolicy,
                locale: 'en',
                slug: 'privacy-policy',
                version: 2,
                title: 'Privacy Policy',
                content: 'source',
                safeHtml: '<p>Published safe content</p>',
                publishedAt: new DateTimeImmutable('2026-06-14 00:00:00'),
                effectiveAt: new DateTimeImmutable('2026-06-14 00:00:00'),
            ));

        $this->app->instance(LegalDocumentResolver::class, $resolver);

        $response = $this->get(route('legal.privacy_policy'));

        $response->assertOk();
        $response->assertSee('Privacy Policy');
        $response->assertSee('Published safe content', false);
        $response->assertSee('data-theme="system"', false);
        $response->assertDontSee('draft');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_missing_or_unpublished_legal_document_returns_404(): void
    {
        $resolver = Mockery::mock(LegalDocumentResolver::class);
        $resolver->shouldReceive('published')
            ->once()
            ->with(LegalDocumentType::CookiePolicy, 'en')
            ->andReturnNull();

        $this->app->instance(LegalDocumentResolver::class, $resolver);

        $this->get(route('legal.cookie_policy'))->assertNotFound();
    }
}
