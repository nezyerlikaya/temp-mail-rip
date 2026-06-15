<?php

namespace Tests\Unit\Domains;

use App\Modules\Domains\Exceptions\InvalidDomainException;
use App\Modules\Domains\Services\DomainNormalizer;
use PHPUnit\Framework\TestCase;

class DomainNormalizerTest extends TestCase
{
    public function test_domains_normalize_consistently(): void
    {
        $normalizer = new DomainNormalizer;

        $this->assertSame('example.com', $normalizer->normalize(' Example.COM. '));
        $this->assertSame('mail.example.co.uk', $normalizer->normalize('MAIL.Example.Co.UK'));
    }

    public function test_urls_emails_ips_wildcards_and_internal_names_are_rejected(): void
    {
        $normalizer = new DomainNormalizer;

        foreach ([
            'https://example.com',
            'user@example.com',
            'example.com:25',
            'example.com/path',
            '*.example.com',
            '127.0.0.1',
            'localhost',
            'mail.local',
            'example.123',
        ] as $input) {
            try {
                $normalizer->normalize($input);
                $this->fail("Invalid domain [{$input}] was accepted.");
            } catch (InvalidDomainException) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_invalid_labels_are_rejected(): void
    {
        $this->expectException(InvalidDomainException::class);

        (new DomainNormalizer)->normalize('bad..example.com');
    }

    public function test_idn_domains_convert_to_ascii_when_runtime_supports_it(): void
    {
        $normalizer = new DomainNormalizer;

        if (! function_exists('idn_to_ascii')) {
            $this->expectException(InvalidDomainException::class);
            $normalizer->normalize('bücher.example');

            return;
        }

        $this->assertSame('xn--bcher-kva.example', $normalizer->normalize('bücher.example'));
    }

    public function test_idn_domains_can_be_disabled(): void
    {
        $this->expectException(InvalidDomainException::class);

        (new DomainNormalizer)->normalize('bücher.example', allowIdn: false);
    }
}
