# Domain Health Foundation

STEP013 introduces the Domain Health module for lightweight DNS/MX readiness evaluation of domains owned by Domain Inventory. It records snapshots and current summaries. It does not implement Domain Pool selection, mailbox generation, provider routing, SMTP transaction tests, reputation intelligence, Monitoring alerts, Public Status, Abuse/Quarantine integration, plan entitlement logic, or admin UI.

## Ownership

Domain Health owns DNS/MX readiness checks, health snapshots, deterministic health status/score calculation, retention preparation, cron-compatible batch checking, and safe DNS diagnostics.

Domain Inventory remains the source of truth for domain existence, canonical domain names, status, type, and catch-all metadata. Domain Health consumes Domain Inventory and never mutates inventory status automatically.

## Check Types

The foundation supports bounded local DNS checks through PHP DNS functions:

- DNS visibility, defined as MX, A, or AAAA presence.
- MX presence.
- MX format sanity at the level of safe function result classification.
- DNS lookup error classification.
- Last checked and last successful timestamps through summary storage.

It does not run DNS scans, SMTP connections, mail delivery tests, provider verification, or reputation checks.

## Statuses

`DomainHealthStatus` values:

- `unknown`: no reliable check result is available yet or the resolver environment is unavailable.
- `healthy`: DNS is visible and MX records are present.
- `warning`: DNS is visible but MX is missing.
- `degraded`: no DNS records were found.
- `failing`: invalid/unknown DNS response conditions.

Health status is separate from Domain Inventory status. Disabled and pending inventory domains may be checked by policy, but retired domains are skipped by the batch checker.

## Score Formula

Current formula version: `dns-mx-v1`.

Score range is 0-100:

- 100: DNS visible and MX present.
- 65: DNS visible but MX absent.
- 30: no DNS records.
- 10: invalid or unknown response.
- 0: unsupported DNS environment, resolver unavailable, or timeout.

The score is readiness-only. It is not reputation intelligence and does not decide pool eligibility.

## Error Classes

`DnsErrorCode` values:

- `timeout`
- `no_records`
- `invalid_response`
- `resolver_unavailable`
- `unsupported_environment`
- `unknown_error`

Raw resolver errors, resolver hostnames, DNS payload dumps, provider credentials, and internal diagnostics are not stored or exposed.

## Storage

`domain_health_snapshots` stores bounded facts for each check: domain ID, status, score, formula version, MX presence, DNS visibility, safe error code, and checked timestamp.

`domain_health_summaries` stores the current status, current score, last checked timestamp, last successful check timestamp, and last error code for one domain.

Snapshot retention is configured by `domainhealth.snapshot_retention_days`. The repository exposes pruning logic, but STEP013 does not schedule destructive cleanup automatically.

## Scheduled Batch Model

`domain-health:check` runs a bounded batch through `DomainHealthBatchChecker`. The checker uses a short cache lock to avoid overlap and consumes non-retired inventory domains in deterministic status order: active, pending, then disabled.

Recommended shared-hosting cron example:

```text
*/15 * * * * cd /path/to/app && php artisan domain-health:check >> /dev/null 2>&1
```

The command does not require Redis, Supervisor, daemons, privileged network tools, shelling out to `dig`/`nslookup`, or SMTP checks.

## Settings

Registered settings:

- `domainhealth.batch_size`
- `domainhealth.timeout_seconds`
- `domainhealth.snapshot_retention_days`
- `domainhealth.warning_threshold`
- `domainhealth.degraded_threshold`
- `domainhealth.check_interval_minutes`

Settings use explicit units and safe defaults. They do not store provider secrets, resolver credentials, registrar credentials, or private diagnostics.

## Cache

Current summaries may be cached by domain ID for a short duration. Snapshot history is not cached as a blob. Summary cache is invalidated after successful recording. Cache failure does not mark a domain healthy.

## Boundaries

Domain Pool later consumes Domain Health summary plus Domain Inventory, but owns eligibility and selection. Monitoring owns alerts. Public Status owns public output. Reputation Intelligence owns reputation. Mail Providers own provider verification and routing. Mailboxes owns mailbox generation.
