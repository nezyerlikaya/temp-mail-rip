<?php

namespace Tests\Unit\DomainHealth;

use App\Modules\DomainHealth\Repositories\DomainHealthRepository;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;

class DomainHealthRepositoryCacheTest extends TestCase
{
    public function test_summary_cache_invalidates_by_domain_id(): void
    {
        $cache = new Repository(new ArrayStore);
        $repository = new DomainHealthRepository($cache);

        $cache->put('domain_health:summary:42', 'cached', 60);
        $repository->forgetSummary(42);

        $this->assertNull($cache->get('domain_health:summary:42'));
    }
}
