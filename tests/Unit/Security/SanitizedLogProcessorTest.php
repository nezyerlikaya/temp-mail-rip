<?php

namespace Tests\Unit\Security;

use App\Modules\Security\Logging\SanitizedLogProcessor;
use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\TestCase;

class SanitizedLogProcessorTest extends TestCase
{
    public function test_log_records_are_sanitized_without_raw_secrets_or_paths(): void
    {
        $processor = new SanitizedLogProcessor(
            new SecretMasker,
            new PathAnonymizer,
            new SafeDiagnosticsFormatter(new SecretMasker, new PathAnonymizer),
        );

        $record = new LogRecord(
            datetime: new \DateTimeImmutable,
            channel: 'testing',
            level: Level::Error,
            message: 'Failure token=secret at '.base_path('app/file.php'),
            context: ['password' => 'plain', 'path' => storage_path('logs/laravel.log')],
            extra: [],
        );

        $processed = $processor($record);
        $encoded = json_encode([$processed->message, $processed->context], JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('secret', $encoded);
        $this->assertStringNotContainsString('plain', $encoded);
        $this->assertStringNotContainsString(base_path(), $encoded);
        $this->assertStringNotContainsString(storage_path(), $encoded);
        $this->assertStringContainsString('[MASKED]', $encoded);
    }
}
