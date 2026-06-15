<?php

namespace App\Modules\Domains\Repositories;

use App\Models\Domain;
use App\Modules\Domains\DTOs\RegisterDomainData;
use App\Modules\Domains\DTOs\SafeDomainRecord;
use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;
use App\Modules\Domains\Exceptions\DuplicateDomainException;
use App\Modules\Domains\Exceptions\UnknownDomainException;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\QueryException;
use Throwable;

class DomainRepository
{
    public function __construct(
        private readonly CacheRepository $cache,
    ) {}

    public function create(RegisterDomainData $data, string $canonical, string $displayDomain): SafeDomainRecord
    {
        try {
            $domain = Domain::query()->create([
                'domain' => $canonical,
                'display_domain' => $displayDomain,
                'status' => $data->status->value,
                'domain_type' => $data->type->value,
                'supports_catch_all' => $data->supportsCatchAll,
                'notes' => $data->notes,
            ]);
        } catch (QueryException $exception) {
            throw DuplicateDomainException::forDomain($canonical);
        }

        $record = $this->toSafeRecord($domain);
        $this->forget($canonical);

        return $record;
    }

    public function find(string $canonical): ?SafeDomainRecord
    {
        try {
            $cached = $this->cache->get($this->cacheKey($canonical));

            if ($cached instanceof SafeDomainRecord) {
                return $cached;
            }
        } catch (Throwable) {
            //
        }

        $domain = Domain::query()->where('domain', $canonical)->first();

        if (! $domain) {
            return null;
        }

        $record = $this->toSafeRecord($domain);

        try {
            $this->cache->put($this->cacheKey($canonical), $record, now()->addMinutes(5));
        } catch (Throwable) {
            //
        }

        return $record;
    }

    public function updateStatus(string $canonical, DomainStatus $status): SafeDomainRecord
    {
        $domain = Domain::query()->where('domain', $canonical)->first();

        if (! $domain) {
            throw UnknownDomainException::forDomain($canonical);
        }

        $domain->status = $status;
        $domain->save();
        $this->forget($canonical);

        return $this->toSafeRecord($domain);
    }

    public function updateType(string $canonical, DomainType $type): SafeDomainRecord
    {
        $domain = Domain::query()->where('domain', $canonical)->first();

        if (! $domain) {
            throw UnknownDomainException::forDomain($canonical);
        }

        $domain->domain_type = $type;
        $domain->save();
        $this->forget($canonical);

        return $this->toSafeRecord($domain);
    }

    /**
     * @return list<SafeDomainRecord>
     */
    public function list(?DomainStatus $status = null, ?DomainType $type = null, int $limit = 50): array
    {
        $query = Domain::query()->orderBy('domain')->limit($limit);

        if ($status !== null) {
            $query->where('status', $status->value);
        }

        if ($type !== null) {
            $query->where('domain_type', $type->value);
        }

        return $query->get()->map(fn (Domain $domain): SafeDomainRecord => $this->toSafeRecord($domain))->all();
    }

    public function exists(string $canonical): bool
    {
        return Domain::query()->where('domain', $canonical)->exists();
    }

    public function forget(string $canonical): void
    {
        try {
            $this->cache->forget($this->cacheKey($canonical));
        } catch (Throwable) {
            //
        }
    }

    protected function toSafeRecord(Domain $domain): SafeDomainRecord
    {
        return new SafeDomainRecord(
            id: $domain->getKey(),
            domain: $domain->domain,
            displayDomain: $domain->display_domain,
            status: $domain->status,
            type: $domain->domain_type,
            supportsCatchAll: (bool) $domain->supports_catch_all,
        );
    }

    private function cacheKey(string $canonical): string
    {
        return 'domains:lookup:'.$canonical;
    }
}
