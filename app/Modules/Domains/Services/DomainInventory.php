<?php

namespace App\Modules\Domains\Services;

use App\Modules\Domains\DTOs\RegisterDomainData;
use App\Modules\Domains\DTOs\SafeDomainRecord;
use App\Modules\Domains\Enums\DomainStatus;
use App\Modules\Domains\Enums\DomainType;
use App\Modules\Domains\Exceptions\DuplicateDomainException;
use App\Modules\Domains\Repositories\DomainRepository;
use App\Modules\Settings\Services\SettingsResolver;
use Throwable;

class DomainInventory
{
    public function __construct(
        private readonly DomainNormalizer $normalizer,
        private readonly DomainNotesPolicy $notes,
        private readonly DomainRepository $domains,
        private readonly SettingsResolver $settings,
    ) {}

    public function register(RegisterDomainData $data): SafeDomainRecord
    {
        $canonical = $this->normalizer->normalize($data->domain, $this->idnAllowed());

        if ($this->domains->exists($canonical)) {
            throw DuplicateDomainException::forDomain($canonical);
        }

        return $this->domains->create(
            new RegisterDomainData(
                domain: $canonical,
                status: $data->status,
                type: $data->type,
                supportsCatchAll: $data->supportsCatchAll,
                notes: $this->notes->validate($data->notes),
            ),
            $canonical,
            $this->normalizer->displayDomain($canonical),
        );
    }

    public function resolve(string $domain): ?SafeDomainRecord
    {
        return $this->domains->find($this->normalizer->normalize($domain, $this->idnAllowed()));
    }

    public function resolveUsable(string $domain): ?SafeDomainRecord
    {
        $record = $this->resolve($domain);

        return $record?->usable() ? $record : null;
    }

    public function updateStatus(string $domain, DomainStatus $status): SafeDomainRecord
    {
        return $this->domains->updateStatus($this->normalizer->normalize($domain, $this->idnAllowed()), $status);
    }

    public function updateType(string $domain, DomainType $type): SafeDomainRecord
    {
        return $this->domains->updateType($this->normalizer->normalize($domain, $this->idnAllowed()), $type);
    }

    public function retire(string $domain): SafeDomainRecord
    {
        return $this->updateStatus($domain, DomainStatus::Retired);
    }

    /**
     * @return list<SafeDomainRecord>
     */
    public function list(?DomainStatus $status = null, ?DomainType $type = null, ?int $limit = null): array
    {
        return $this->domains->list($status, $type, min($limit ?? $this->defaultLimit(), $this->defaultLimit()));
    }

    private function idnAllowed(): bool
    {
        try {
            return (bool) $this->settings->get('domains.allow_idn');
        } catch (Throwable) {
            return true;
        }
    }

    private function defaultLimit(): int
    {
        try {
            return (int) $this->settings->get('domains.max_list_limit');
        } catch (Throwable) {
            return 50;
        }
    }
}
