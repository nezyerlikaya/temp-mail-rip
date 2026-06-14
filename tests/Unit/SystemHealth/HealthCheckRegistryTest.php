<?php

namespace Tests\Unit\SystemHealth;

use App\Modules\SystemHealth\DTOs\HealthCheckDefinition;
use App\Modules\SystemHealth\Enums\HealthStatus;
use App\Modules\SystemHealth\Exceptions\DuplicateHealthCheckException;
use App\Modules\SystemHealth\Exceptions\UnknownHealthCheckException;
use App\Modules\SystemHealth\Services\HealthCheckRegistry;
use App\Modules\SystemHealth\Services\HealthResultFactory;
use PHPUnit\Framework\TestCase;

class HealthCheckRegistryTest extends TestCase
{
    public function test_health_checks_register_and_sort_by_key(): void
    {
        $registry = new HealthCheckRegistry;
        $factory = $this->createMock(HealthResultFactory::class);

        $registry->register(new HealthCheckDefinition('storage.write', 'Storage', fn () => $factory->result('storage.write', HealthStatus::Healthy, 'ok')));
        $registry->register(new HealthCheckDefinition('app.boot', 'Boot', fn () => $factory->result('app.boot', HealthStatus::Healthy, 'ok')));

        $this->assertSame(['app.boot', 'storage.write'], array_map(
            fn (HealthCheckDefinition $definition): string => $definition->key,
            $registry->all(),
        ));
    }

    public function test_duplicate_and_unknown_checks_fail(): void
    {
        $registry = new HealthCheckRegistry;
        $factory = $this->createMock(HealthResultFactory::class);
        $definition = new HealthCheckDefinition('app.boot', 'Boot', fn () => $factory->result('app.boot', HealthStatus::Healthy, 'ok'));

        $registry->register($definition);

        try {
            $registry->register($definition);
            $this->fail('Duplicate health check did not throw.');
        } catch (DuplicateHealthCheckException) {
            $this->assertTrue(true);
        }

        $this->expectException(UnknownHealthCheckException::class);

        $registry->get('missing.check');
    }
}
