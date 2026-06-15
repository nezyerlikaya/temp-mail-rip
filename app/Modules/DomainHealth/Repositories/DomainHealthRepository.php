<?php

namespace App\Modules\DomainHealth\Repositories;

use App\Models\DomainHealthSnapshot;
use App\Models\DomainHealthSummary;
use App\Modules\DomainHealth\DTOs\DomainHealthSnapshotData;
use App\Modules\DomainHealth\DTOs\DomainHealthSummaryData;
use App\Modules\DomainHealth\Enums\DnsErrorCode;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Throwable;

class DomainHealthRepository
{
    public function __construct(
        private readonly CacheRepository $cache,
    ) {}

    public function record(DomainHealthSnapshotData $data): DomainHealthSnapshotData
    {
        DomainHealthSnapshot::query()->create([
            'domain_id' => $data->domainId,
            'health_status' => $data->status->value,
            'health_score' => $data->score,
            'formula_version' => $data->formulaVersion,
            'mx_present' => $data->mxPresent,
            'dns_visible' => $data->dnsVisible,
            'error_code' => $data->errorCode?->value,
            'checked_at' => $data->checkedAt,
            'created_at' => now(),
        ]);

        DomainHealthSummary::query()->updateOrCreate(
            ['domain_id' => $data->domainId],
            [
                'current_status' => $data->status->value,
                'current_score' => $data->score,
                'last_checked_at' => $data->checkedAt,
                'last_success_at' => $data->errorCode === null ? $data->checkedAt : null,
                'last_error_code' => $data->errorCode?->value,
                'updated_at' => now(),
            ],
        );

        $this->forgetSummary($data->domainId);

        return $data;
    }

    public function summary(int|string $domainId): ?DomainHealthSummaryData
    {
        try {
            $cached = $this->cache->get($this->summaryCacheKey($domainId));

            if ($cached instanceof DomainHealthSummaryData) {
                return $cached;
            }
        } catch (Throwable) {
            //
        }

        $summary = DomainHealthSummary::query()->where('domain_id', $domainId)->first();

        if (! $summary) {
            return null;
        }

        $data = new DomainHealthSummaryData(
            domainId: $summary->domain_id,
            status: $summary->current_status,
            score: (int) $summary->current_score,
            lastCheckedAt: $summary->last_checked_at?->toDateTimeImmutable(),
            lastSuccessAt: $summary->last_success_at?->toDateTimeImmutable(),
            lastErrorCode: $summary->last_error_code instanceof DnsErrorCode ? $summary->last_error_code : null,
        );

        try {
            $this->cache->put($this->summaryCacheKey($domainId), $data, now()->addMinutes(5));
        } catch (Throwable) {
            //
        }

        return $data;
    }

    public function pruneSnapshotsOlderThan(int $retentionDays): int
    {
        return DomainHealthSnapshot::query()
            ->where('checked_at', '<', now()->subDays($retentionDays))
            ->delete();
    }

    public function forgetSummary(int|string $domainId): void
    {
        try {
            $this->cache->forget($this->summaryCacheKey($domainId));
        } catch (Throwable) {
            //
        }
    }

    private function summaryCacheKey(int|string $domainId): string
    {
        return 'domain_health:summary:'.$domainId;
    }
}
