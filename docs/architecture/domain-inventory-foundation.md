# Domain Inventory Foundation

STEP012 introduces the Domains module as the source of truth for domains that may later be used by the Temp Mail engine. It owns domain records, canonical normalization, uniqueness, status, type classification, catch-all metadata, notes policy, and safe lookup.

It does not implement DNS health, MX health, domain reputation, pool selection, provider verification, mailbox generation, public domain browsing, plan entitlement logic, billing, abuse/quarantine decisions, or admin UI.

## Storage

The `domains` table stores one canonical `domain` value with a database unique constraint. It also stores `display_domain`, `status`, `domain_type`, `supports_catch_all`, optional internal `notes`, and timestamps.

The table intentionally does not store DNS records, MX results, health scores, reputation scores, pool weights, provider credentials, registrar secrets, or mailbox generation data.

## Normalization

`DomainNormalizer` applies deterministic normalization before storage and lookup:

- Trim whitespace.
- Remove trailing dots.
- Reject URLs, email addresses, ports, paths, queries, fragments, wildcards, IP addresses, localhost-like names, and internal hostnames.
- Convert supported IDN input to ASCII/Punycode using PHP `intl` when available.
- Lowercase canonical ASCII labels.
- Enforce maximum full-domain and label lengths.
- Reject empty labels, invalid label characters, numeric-only TLDs, and too-short TLDs.

IDN support depends on PHP `idn_to_ascii`. If PHP `intl` is unavailable and non-ASCII input is provided, normalization fails explicitly. This prevents Unicode lookalikes from silently bypassing authorization or uniqueness checks later. The canonical stored value is always ASCII.

## Status

Statuses are represented by `DomainStatus`:

- `active`: may later be considered by Domain Pool, but is not pool-eligible by itself.
- `disabled`: exists but must not be used.
- `pending`: exists but is not ready for use.
- `retired`: should not be used and remains reserved historically.

There is no duplicate `is_active` truth source. Disabled and retired domains do not resolve as usable.

## Type

Types are represented by `DomainType`:

- `system`
- `disposable`
- `premium`

Type is inventory metadata only. It does not grant plan access, entitlement, authorization, reputation, or mailbox access. Plans, Subscriptions, and Feature Gates own access decisions.

## Catch-All Metadata

`supports_catch_all` is a metadata hint only. It does not prove DNS/provider configuration, bypass later provider verification, or make a domain pool-eligible.

## Notes Safety

Notes are internal-only. `DomainNotesPolicy` bounds notes and rejects common credential patterns such as passwords, tokens, API keys, secrets, authorization values, and registrar login details. `SafeDomainRecord` deliberately omits notes from public/safe output.

## Registry And Resolver

`DomainInventory` is the module boundary for create, resolve, bounded list, status update, type update, and retire operations. Other modules should not query the `domains` table directly.

`resolve()` returns a safe record for existing domains. `resolveUsable()` returns only active domains. Neither method performs DNS checks, provider checks, reputation scoring, pool eligibility, or mailbox generation.

## Cache

`DomainRepository` caches individual safe domain lookups by canonical domain for a short duration. Notes are not cached in public-safe records. Updates invalidate the individual canonical lookup key. Cache failure falls back to authoritative database lookup and does not assume Redis.

Bounded list queries are index-backed and limited by `domains.max_list_limit`.

## Settings

The Domains module registers safe settings:

- `domains.max_list_limit`
- `domains.allow_idn`

Settings must not store DNS provider credentials, registrar secrets, API keys, or private operational notes.

## Boundaries

Domain Health owns DNS/MX/reputation measurements. Domain Pool owns eligibility and selection. Mail Providers own provider verification/routing. Mailboxes owns address generation. Plans/Subscriptions/Feature Gates own access decisions. Abuse/Quarantine owns abuse decisions.
