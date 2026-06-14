<?php

namespace Tests\Unit\Security;

use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use stdClass;
use Tests\TestCase;

class SafeDiagnosticsFormatterTest extends TestCase
{
    public function test_diagnostics_mask_secrets_anonymize_paths_and_bound_output(): void
    {
        $formatter = new SafeDiagnosticsFormatter(new SecretMasker, new PathAnonymizer);

        $formatted = $formatter->format([
            'token' => 'raw-token',
            'path' => storage_path('logs/laravel.log'),
            'request_body' => ['password' => 'secret'],
            'long' => str_repeat('a', 300),
            'items' => range(1, 30),
            'object' => new stdClass,
            'safe' => 'useful',
        ]);

        $encoded = json_encode($formatted, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('raw-token', $encoded);
        $this->assertStringNotContainsString(storage_path(), $encoded);
        $this->assertSame('[MASKED]', $formatted['token']);
        $this->assertSame('[omitted]', $formatted['request_body']);
        $this->assertStringContainsString('[truncated]', $formatted['long']);
        $this->assertArrayHasKey('__truncated', $formatted['items']);
        $this->assertSame('[object stdClass]', $formatted['object']);
        $this->assertSame('useful', $formatted['safe']);
    }
}
