<?php

namespace App\Modules\DomainHealth\Services;

use App\Modules\DomainHealth\DTOs\DomainHealthSnapshotData;
use App\Modules\Domains\DTOs\SafeDomainRecord;
use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Services\DomainInventory;
use App\Modules\Settings\Services\SettingsResolver;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Throwable;

class DomainHealthBatchChecker
{
    private const LOCK_KEY = 'domain_health:batch_check:lock';

    public function __construct(
        private readonly DomainInventory $domains,
        private readonly DomainHealthChecker $checker,
        private readonly SettingsResolver $settings,
        private readonly CacheRepository $cache,
    ) {}

    /**
     * @return list<DomainHealthSnapshotData>
     */
    public function run(?int $limit = null): array
    {
        $limit = min($limit ?? $this->batchSize(), $this->batchSize());

        if (! $this->acquireLock()) {
            return [];
        }

        try {
            $snapshots = [];

            foreach ($this->candidates($limit) as $domain) {
                if (count($snapshots) >= $limit) {
                    break;
                }

                try {
                    $snapshots[] = $this->checker->check($domain->domain);
                } catch (Throwable) {
                    //
                }
            }

            return $snapshots;
        } finally {
            $this->releaseLock();
        }
    }

    /**
     * @return list<SafeDomainRecord>
     */
    private function candidates(int $limit): array
    {
        $records = [];

        foreach ([DomainStatus::Active, DomainStatus::Pending, DomainStatus::Disabled] as $status) {
            if (count($records) >= $limit) {
                break;
            }

            $records = [
                ...$records,
                ...$this->domains->list($status, limit: $limit - count($records)),
            ];
        }

        return $records;
    }

    private function batchSize(): int
    {
        try {
            return (int) $this->settings->get('domainhealth.batch_size');
        } catch (Throwable) {
            return 25;
        }
    }

    private function acquireLock(): bool
    {
        try {
            return $this->cache->add(self::LOCK_KEY, '1', now()->addMinutes(10));
        } catch (Throwable) {
            return false;
        }
    }

    private function releaseLock(): void
    {
        try {
            $this->cache->forget(self::LOCK_KEY);
        } catch (Throwable) {
            //
        }
    }
}
