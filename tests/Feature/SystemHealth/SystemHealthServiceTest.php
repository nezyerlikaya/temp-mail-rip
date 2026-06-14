<?php

namespace Tests\Feature\SystemHealth;

use App\Modules\SystemHealth\DTOs\HealthCheckDefinition;
use App\Modules\SystemHealth\DTOs\HealthCheckResult;
use App\Modules\SystemHealth\DTOs\HealthSummary;
use App\Modules\SystemHealth\Enums\HealthStatus;
use App\Modules\SystemHealth\Services\DegradedStateResolver;
use App\Modules\SystemHealth\Services\HealthCheckRegistry;
use App\Modules\SystemHealth\Services\HealthCheckRunner;
use App\Modules\SystemHealth\Services\HealthResultFactory;
use App\Modules\SystemHealth\Services\HealthSummaryResolver;
use App\Modules\SystemHealth\Services\SchedulerHeartbeat;
use DateTimeImmutable;
use Exception;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Mockery;
use Tests\TestCase;

class SystemHealthServiceTest extends TestCase
{
    public function test_configured_health_checks_are_registered(): void
    {
        $keys = array_map(
            fn (HealthCheckDefinition $definition): string => $definition->key,
            app(HealthCheckRegistry::class)->all(),
        );

        $this->assertSame([
            'app.boot',
            'assets.vite',
            'cache.availability',
            'database.connection',
            'filesystem.logs',
            'filesystem.storage',
            'installer.lock',
            'mail.configuration',
            'queue.scheduler',
            'session.driver',
        ], $keys);
    }

    public function test_runner_sanitizes_check_exceptions(): void
    {
        $registry = new HealthCheckRegistry;
        $registry->register(new HealthCheckDefinition(
            'database.connection',
            'Database',
            fn () => throw new Exception('SQL error at '.base_path().' password=secret'),
        ));

        $runner = new HealthCheckRunner($registry, app(HealthResultFactory::class));
        $result = $runner->run('database.connection');
        $encoded = json_encode($result->toSafeArray(), JSON_THROW_ON_ERROR);

        $this->assertSame(HealthStatus::Degraded, $result->status);
        $this->assertStringNotContainsString(base_path(), $encoded);
        $this->assertStringNotContainsString('password=secret', $encoded);
        $this->assertStringNotContainsString('SQL error', $encoded);
    }

    public function test_cache_failure_does_not_report_healthy(): void
    {
        $registry = new HealthCheckRegistry;
        $registry->register(new HealthCheckDefinition(
            'cache.availability',
            'Cache',
            fn () => throw new Exception('cache password=secret failed'),
        ));

        $runner = new HealthCheckRunner($registry, app(HealthResultFactory::class));

        $this->assertSame(HealthStatus::Degraded, $runner->run('cache.availability')->status);
    }

    public function test_degraded_state_resolves_deterministically(): void
    {
        $summary = new HealthSummary(
            status: HealthStatus::Degraded,
            results: [
                new HealthCheckResult('database.connection', HealthStatus::Degraded, 'Database failed safely.', 1, new DateTimeImmutable),
                new HealthCheckResult('assets.vite', HealthStatus::Warning, 'Assets missing.', 1, new DateTimeImmutable, blocksProduction: false),
            ],
            checkedAt: new DateTimeImmutable,
        );

        $resolver = new DegradedStateResolver;

        $this->assertTrue($resolver->degraded($summary));
        $this->assertSame(['database.connection'], $resolver->reasons($summary));
        $this->assertSame(['status' => 'degraded'], $summary->toPublicArray());
    }

    public function test_scheduler_heartbeat_records_and_reads_age(): void
    {
        $heartbeat = new SchedulerHeartbeat(new Repository(new ArrayStore));

        $this->assertNull($heartbeat->ageSeconds());

        $this->assertTrue($heartbeat->record());

        $this->assertIsInt($heartbeat->ageSeconds());
        $this->assertLessThanOrEqual(1, $heartbeat->ageSeconds());
    }

    public function test_internal_health_command_returns_non_zero_for_blockers_and_safe_json(): void
    {
        $summary = new HealthSummary(
            status: HealthStatus::Critical,
            results: [
                new HealthCheckResult('database.connection', HealthStatus::Critical, 'Database failed safely.', 1, new DateTimeImmutable, [
                    'path' => '[app]',
                    'password' => '[MASKED]',
                ]),
            ],
            checkedAt: new DateTimeImmutable,
        );

        $resolver = Mockery::mock(HealthSummaryResolver::class);
        $resolver->shouldReceive('summarize')->once()->andReturn($summary);
        $this->app->instance(HealthSummaryResolver::class, $resolver);

        $this->artisan('health:check --json')
            ->expectsOutputToContain('"status": "critical"')
            ->assertExitCode(1);
    }
}
