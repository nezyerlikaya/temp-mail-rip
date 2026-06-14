<?php

namespace Tests\Unit\Security;

use App\Modules\Security\Services\SecretMasker;
use PHPUnit\Framework\TestCase;

class SecretMaskerTest extends TestCase
{
    public function test_sensitive_keys_are_masked_recursively(): void
    {
        $masked = (new SecretMasker)->mask([
            'password' => 'plain-secret',
            'nested' => [
                'Api-Key' => 'api-secret',
                'safe' => 'visible',
            ],
        ]);

        $this->assertSame('[MASKED]', $masked['password']);
        $this->assertSame('[MASKED]', $masked['nested']['Api-Key']);
        $this->assertSame('visible', $masked['nested']['safe']);
    }

    public function test_inline_secret_text_is_masked(): void
    {
        $masked = (new SecretMasker)->maskText('Authorization: Bearer abc123 token=secret password=hunter2');

        $this->assertStringNotContainsString('abc123', $masked);
        $this->assertStringNotContainsString('secret', $masked);
        $this->assertStringNotContainsString('hunter2', $masked);
        $this->assertStringContainsString('[MASKED]', $masked);
    }
}
