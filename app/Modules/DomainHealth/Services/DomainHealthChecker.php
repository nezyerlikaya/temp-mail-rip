<?php

namespace App\Modules\DomainHealth\Services;

use App\Modules\DomainHealth\DTOs\DomainHealthSnapshotData;
use App\Modules\DomainHealth\Exceptions\UnknownHealthDomainException;
use App\Modules\DomainHealth\Repositories\DomainHealthRepository;
use App\Modules\Domains\Services\DomainInventory;
use DateTimeImmutable;

class DomainHealthChecker
{
    public function __construct(
        private readonly DomainInventory $domains,
        private readonly DnsReadinessResolver $dns,
        private readonly DomainHealthStatusCalculator $calculator,
        private readonly DomainHealthRepository $health,
    ) {}

    public function check(string $domain): DomainHealthSnapshotData
    {
        $record = $this->domains->resolve($domain);

        if ($record === null || $record->id === null) {
            throw UnknownHealthDomainException::forDomain($domain);
        }

        $result = $this->dns->lookup($record->domain);

        return $this->health->record(new DomainHealthSnapshotData(
            domainId: $record->id,
            domain: $record->domain,
            status: $this->calculator->status($result),
            score: $this->calculator->score($result),
            formulaVersion: DomainHealthStatusCalculator::FORMULA_VERSION,
            mxPresent: $result->mxPresent,
            dnsVisible: $result->dnsVisible,
            errorCode: $result->errorCode,
            checkedAt: new DateTimeImmutable,
        ));
    }
}
