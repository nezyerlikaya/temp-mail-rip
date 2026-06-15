<?php

namespace Tests\Unit\Domains;

use App\Modules\Domains\DTOs\SafeDomainRecord;
use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;
use App\Modules\Domains\Repositories\DomainRepository;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;

class DomainRepositoryCacheTest extends TestCase
{
    public function test_cache_key_is_invalidated_for_canonical_domain(): void
    {
        $cache = new Repository(new ArrayStore);
        $repository = new DomainRepository($cache);
        $record = new SafeDomainRecord(1, 'example.com', 'example.com', DomainStatus::Active, DomainType::Disposable, false);

        $cache->put('domains:lookup:example.com', $record, 60);

        $repository->forget('example.com');

        $this->assertNull($cache->get('domains:lookup:example.com'));
    }
}
