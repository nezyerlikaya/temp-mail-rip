<?php

namespace Tests\Feature\DomainHealth;

use App\Modules\DomainHealth\DTOs\DnsLookupResult;
use App\Modules\DomainHealth\DTOs\DomainHealthSnapshotData;
use App\Modules\DomainHealth\Enums\DnsErrorCode;
use App\Modules\DomainHealth\Enums\DomainHealthStatus;
use App\Modules\DomainHealth\Exceptions\UnknownHealthDomainException;
use App\Modules\DomainHealth\Repositories\DomainHealthRepository;
use App\Modules\DomainHealth\Services\DnsReadinessResolver;
use App\Modules\DomainHealth\Services\DomainHealthBatchChecker;
use App\Modules\DomainHealth\Services\DomainHealthChecker;
use App\Modules\DomainHealth\Services\DomainHealthStatusCalculator;
use App\Modules\Domains\DTOs\SafeDomainRecord;
use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;
use App\Modules\Domains\Services\DomainInventory;
use App\Modules\Settings\Services\SettingsResolver;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Mockery;
use Tests\TestCase;

class DomainHealthCheckerTest extends TestCase
{
    public function test_checker_records_snapshot_and_does_not_mutate_inventory_status(): void
    {
        $inventory = new FakeDomainInventory([
            'example.com' => new SafeDomainRecord(1, 'example.com', 'example.com', DomainStatus::Active, DomainType::Disposable, false),
        ]);
        $repository = new FakeDomainHealthRepository;

        $checker = new DomainHealthChecker(
            $inventory,
            new FakeDnsReadinessResolver(new DnsLookupResult(true, true)),
            new DomainHealthStatusCalculator,
            $repository,
        );

        $snapshot = $checker->check('example.com');

        $this->assertSame(DomainHealthStatus::Healthy, $snapshot->status);
        $this->assertSame(100, $snapshot->score);
        $this->assertTrue($snapshot->mxPresent);
        $this->assertSame(DomainStatus::Active, $inventory->records['example.com']->status);
        $this->assertCount(1, $repository->snapshots);
    }

    public function test_unknown_domains_fail_safely(): void
    {
        $checker = new DomainHealthChecker(
            new FakeDomainInventory([]),
            new FakeDnsReadinessResolver(new DnsLookupResult(false, false, DnsErrorCode::NoRecords)),
            new DomainHealthStatusCalculator,
            new FakeDomainHealthRepository,
        );

        $this->expectException(UnknownHealthDomainException::class);

        $checker->check('missing.example');
    }

    public function test_batch_checking_is_bounded_lock_aware_and_skips_retired_domains(): void
    {
        $inventory = new FakeDomainInventory([
            'active.example' => new SafeDomainRecord(1, 'active.example', 'active.example', DomainStatus::Active, DomainType::Disposable, false),
            'pending.example' => new SafeDomainRecord(2, 'pending.example', 'pending.example', DomainStatus::Pending, DomainType::Disposable, false),
            'disabled.example' => new SafeDomainRecord(3, 'disabled.example', 'disabled.example', DomainStatus::Disabled, DomainType::Disposable, false),
            'retired.example' => new SafeDomainRecord(4, 'retired.example', 'retired.example', DomainStatus::Retired, DomainType::Disposable, false),
        ]);

        $checker = new DomainHealthChecker(
            $inventory,
            new FakeDnsReadinessResolver(new DnsLookupResult(true, false, DnsErrorCode::NoRecords)),
            new DomainHealthStatusCalculator,
            new FakeDomainHealthRepository,
        );

        $settings = Mockery::mock(SettingsResolver::class);
        $settings->shouldReceive('get')->with('domainhealth.batch_size')->andReturn(2);

        $batch = new DomainHealthBatchChecker($inventory, $checker, $settings, new Repository(new ArrayStore));
        $snapshots = $batch->run(99);

        $this->assertCount(2, $snapshots);
        $this->assertSame(['active.example', 'pending.example'], array_map(
            fn (DomainHealthSnapshotData $snapshot): string => $snapshot->domain,
            $snapshots,
        ));
    }
}

class FakeDnsReadinessResolver extends DnsReadinessResolver
{
    public function __construct(private readonly DnsLookupResult $result) {}

    public function lookup(string $canonicalDomain): DnsLookupResult
    {
        return $this->result;
    }
}

class FakeDomainHealthRepository extends DomainHealthRepository
{
    /**
     * @var list<DomainHealthSnapshotData>
     */
    public array $snapshots = [];

    public function __construct() {}

    public function record(DomainHealthSnapshotData $data): DomainHealthSnapshotData
    {
        $this->snapshots[] = $data;

        return $data;
    }
}

class FakeDomainInventory extends DomainInventory
{
    /**
     * @param  array<string, SafeDomainRecord>  $records
     */
    public function __construct(public array $records) {}

    public function resolve(string $domain): ?SafeDomainRecord
    {
        return $this->records[$domain] ?? null;
    }

    public function list(?DomainStatus $status = null, ?DomainType $type = null, ?int $limit = null): array
    {
        $records = array_values(array_filter(
            $this->records,
            fn (SafeDomainRecord $record): bool => ($status === null || $record->status === $status)
                && ($type === null || $record->type === $type),
        ));

        usort($records, fn (SafeDomainRecord $a, SafeDomainRecord $b): int => $a->domain <=> $b->domain);

        return array_slice($records, 0, $limit ?? count($records));
    }
}
