<?php

namespace Tests\Unit\Installer;

use App\Modules\Installer\DTOs\PreflightCheckResult;
use App\Modules\Installer\Services\InstallationLock;
use App\Modules\Installer\Services\InstallationStateDetector;
use App\Modules\Installer\Services\PreflightChecker;
use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use Tests\TestCase;

class PreflightCheckerTest extends TestCase
{
    public function test_installation_state_detection_combines_lock_and_blockers(): void
    {
        $lock = new FakeInstallationLock(locked: true);
        $preflight = new FakePreflightChecker($lock, [
            new PreflightCheckResult('app.key', 'Application key', 'ok', 'Ready.'),
        ]);

        $state = (new InstallationStateDetector($lock, $preflight))->detect();

        $this->assertTrue($state->locked);
        $this->assertTrue($state->complete);
    }

    public function test_missing_app_key_is_reported_safely(): void
    {
        $checker = new PreflightChecker(new FakeInstallationLock(false), $this->diagnostics());

        $results = $checker->run();
        $appKey = $this->find($results, 'app.key');

        $this->assertNotSame('', $appKey->message);
        $this->assertStringNotContainsString((string) getenv('APP_KEY'), $appKey->message);
    }

    public function test_diagnostics_mask_paths_and_secrets(): void
    {
        $formatted = $this->diagnostics()->format([
            'path' => base_path('storage/logs/laravel.log'),
            'password' => 'super-secret',
            'message' => 'token=abc123 failed at '.base_path('vendor/file.php'),
        ]);

        $encoded = json_encode($formatted, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString(base_path(), $encoded);
        $this->assertStringNotContainsString('super-secret', $encoded);
        $this->assertStringNotContainsString('abc123', $encoded);
    }

    /**
     * @param  list<PreflightCheckResult>  $results
     */
    private function find(array $results, string $key): PreflightCheckResult
    {
        foreach ($results as $result) {
            if ($result->key === $key) {
                return $result;
            }
        }

        $this->fail("Missing result [{$key}].");
    }

    private function diagnostics(): SafeDiagnosticsFormatter
    {
        return new SafeDiagnosticsFormatter(new SecretMasker, new PathAnonymizer);
    }
}

class FakeInstallationLock extends InstallationLock
{
    public function __construct(private readonly bool $locked) {}

    public function locked(): bool
    {
        return $this->locked;
    }
}

class FakePreflightChecker extends PreflightChecker
{
    /**
     * @param  list<PreflightCheckResult>  $results
     */
    public function __construct(InstallationLock $lock, private readonly array $results)
    {
        parent::__construct($lock, new SafeDiagnosticsFormatter(new SecretMasker, new PathAnonymizer));
    }

    public function run(): array
    {
        return $this->results;
    }
}
