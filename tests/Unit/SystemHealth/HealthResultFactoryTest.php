<?php

namespace Tests\Unit\SystemHealth;

use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use App\Modules\SystemHealth\Enums\HealthStatus;
use App\Modules\SystemHealth\Services\HealthResultFactory;
use Exception;
use Tests\TestCase;

class HealthResultFactoryTest extends TestCase
{
    public function test_health_context_masks_secrets_and_anonymizes_paths(): void
    {
        $factory = new HealthResultFactory(new SafeDiagnosticsFormatter(new SecretMasker, new PathAnonymizer));

        $result = $factory->result(
            key: 'app.boot',
            status: HealthStatus::Warning,
            message: 'Path '.dirname(__DIR__, 3).' token=super-secret',
            context: [
                'path' => dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'storage',
                'password' => 'super-secret',
            ],
        );

        $encoded = json_encode($result->toSafeArray(), JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString(dirname(__DIR__, 3), $encoded);
        $this->assertStringNotContainsString('super-secret', $encoded);
        $this->assertStringContainsString('[MASKED]', $encoded);
    }

    public function test_exceptions_are_sanitized_without_stack_trace(): void
    {
        $factory = new HealthResultFactory(new SafeDiagnosticsFormatter(new SecretMasker, new PathAnonymizer));

        $result = $factory->exception('database.connection', new Exception('SQLSTATE path '.dirname(__DIR__, 3).' password=secret'));

        $encoded = json_encode($result->toSafeArray(), JSON_THROW_ON_ERROR);

        $this->assertSame(HealthStatus::Degraded, $result->status);
        $this->assertStringNotContainsString(dirname(__DIR__, 3), $encoded);
        $this->assertStringNotContainsString('password=secret', $encoded);
        $this->assertStringNotContainsString('SQLSTATE', $encoded);
        $this->assertStringNotContainsString('#0', $encoded);
    }
}
