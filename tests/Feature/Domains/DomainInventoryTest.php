<?php

namespace Tests\Feature\Domains;

use App\Modules\Domains\DTOs\RegisterDomainData;
use App\Modules\Domains\DTOs\SafeDomainRecord;
use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;
use App\Modules\Domains\Exceptions\DuplicateDomainException;
use App\Modules\Domains\Exceptions\InvalidDomainException;
use App\Modules\Domains\Repositories\DomainRepository;
use App\Modules\Domains\Services\DomainInventory;
use App\Modules\Domains\Services\DomainNormalizer;
use App\Modules\Domains\Services\DomainNotesPolicy;
use App\Modules\Settings\Services\SettingsResolver;
use Mockery;
use Tests\TestCase;

class DomainInventoryTest extends TestCase
{
    public function test_register_and_resolve_domain_with_canonical_uniqueness(): void
    {
        $inventory = $this->inventory($repo = new InMemoryDomainRepository);

        $record = $inventory->register(new RegisterDomainData(
            domain: 'Example.COM.',
            status: DomainStatus::Active,
            type: DomainType::Disposable,
            supportsCatchAll: true,
            notes: 'Internal owner approved.',
        ));

        $this->assertSame('example.com', $record->domain);
        $this->assertTrue($record->supportsCatchAll);
        $this->assertSame($record, $inventory->resolve(' example.com '));
        $this->assertCount(1, $repo->records);

        $this->expectException(DuplicateDomainException::class);

        $inventory->register(new RegisterDomainData('EXAMPLE.com'));
    }

    public function test_disabled_and_retired_domains_do_not_resolve_as_usable(): void
    {
        $inventory = $this->inventory(new InMemoryDomainRepository);
        $inventory->register(new RegisterDomainData('disabled.example', DomainStatus::Disabled));
        $inventory->register(new RegisterDomainData('retired.example', DomainStatus::Retired));
        $inventory->register(new RegisterDomainData('active.example', DomainStatus::Active));

        $this->assertNull($inventory->resolveUsable('disabled.example'));
        $this->assertNull($inventory->resolveUsable('retired.example'));
        $this->assertSame('active.example', $inventory->resolveUsable('active.example')?->domain);
    }

    public function test_status_type_updates_and_retire_are_safe(): void
    {
        $inventory = $this->inventory(new InMemoryDomainRepository);
        $inventory->register(new RegisterDomainData('pending.example'));

        $this->assertSame(DomainStatus::Active, $inventory->updateStatus('pending.example', DomainStatus::Active)->status);
        $this->assertSame(DomainType::Premium, $inventory->updateType('pending.example', DomainType::Premium)->type);
        $this->assertSame(DomainStatus::Retired, $inventory->retire('pending.example')->status);
        $this->assertNull($inventory->resolveUsable('pending.example'));
    }

    public function test_notes_are_rejected_when_they_look_like_credentials_and_omitted_from_safe_output(): void
    {
        $inventory = $this->inventory(new InMemoryDomainRepository);

        try {
            $inventory->register(new RegisterDomainData('secret.example', notes: 'password=super-secret'));
            $this->fail('Credential-like notes were accepted.');
        } catch (InvalidDomainException) {
            $this->assertTrue(true);
        }

        $record = $inventory->register(new RegisterDomainData('safe.example', notes: 'Safe internal note.'));
        $encoded = json_encode($record->toPublicArray(), JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('Safe internal note', $encoded);
        $this->assertArrayNotHasKey('notes', $record->toPublicArray());
    }

    public function test_bounded_listing_uses_settings_limit(): void
    {
        $inventory = $this->inventory(new InMemoryDomainRepository, maxListLimit: 2);

        $inventory->register(new RegisterDomainData('one.example', DomainStatus::Active));
        $inventory->register(new RegisterDomainData('two.example', DomainStatus::Active));
        $inventory->register(new RegisterDomainData('three.example', DomainStatus::Active));

        $this->assertCount(2, $inventory->list(DomainStatus::Active, limit: 99));
    }

    private function inventory(InMemoryDomainRepository $repository, int $maxListLimit = 50): DomainInventory
    {
        $settings = Mockery::mock(SettingsResolver::class);
        $settings->shouldReceive('get')->with('domains.allow_idn')->andReturn(true)->byDefault();
        $settings->shouldReceive('get')->with('domains.max_list_limit')->andReturn($maxListLimit)->byDefault();

        return new DomainInventory(
            new DomainNormalizer,
            new DomainNotesPolicy,
            $repository,
            $settings,
        );
    }
}

class InMemoryDomainRepository extends DomainRepository
{
    /**
     * @var array<string, SafeDomainRecord>
     */
    public array $records = [];

    public function __construct() {}

    public function create(RegisterDomainData $data, string $canonical, string $displayDomain): SafeDomainRecord
    {
        if (isset($this->records[$canonical])) {
            throw DuplicateDomainException::forDomain($canonical);
        }

        return $this->records[$canonical] = new SafeDomainRecord(
            id: count($this->records) + 1,
            domain: $canonical,
            displayDomain: $displayDomain,
            status: $data->status,
            type: $data->type,
            supportsCatchAll: $data->supportsCatchAll,
        );
    }

    public function find(string $canonical): ?SafeDomainRecord
    {
        return $this->records[$canonical] ?? null;
    }

    public function updateStatus(string $canonical, DomainStatus $status): SafeDomainRecord
    {
        $record = $this->records[$canonical];

        return $this->records[$canonical] = new SafeDomainRecord(
            $record->id,
            $record->domain,
            $record->displayDomain,
            $status,
            $record->type,
            $record->supportsCatchAll,
        );
    }

    public function updateType(string $canonical, DomainType $type): SafeDomainRecord
    {
        $record = $this->records[$canonical];

        return $this->records[$canonical] = new SafeDomainRecord(
            $record->id,
            $record->domain,
            $record->displayDomain,
            $record->status,
            $type,
            $record->supportsCatchAll,
        );
    }

    public function list(?DomainStatus $status = null, ?DomainType $type = null, int $limit = 50): array
    {
        $records = array_values(array_filter(
            $this->records,
            fn (SafeDomainRecord $record): bool => ($status === null || $record->status === $status)
                && ($type === null || $record->type === $type),
        ));

        usort($records, fn (SafeDomainRecord $a, SafeDomainRecord $b): int => $a->domain <=> $b->domain);

        return array_slice($records, 0, $limit);
    }

    public function exists(string $canonical): bool
    {
        return isset($this->records[$canonical]);
    }
}
