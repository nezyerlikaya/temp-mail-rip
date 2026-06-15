# Temp Mail SaaS v1 - Scope Cleanup Rules

This document is the cleanup filter for the corrected v1 roadmap and prompt list.

## Target Stack

- Laravel 13
- PHP 8.5.7
- Blade layouts and Blade components
- Tailwind CSS
- Alpine.js
- Vite
- MySQL/MariaDB-compatible migrations
- Laravel scheduler with shared-hosting cron
- Laravel validation, policies/gates, events, jobs, and tests

Do not build raw standalone HTML pages. UI must use Blade layouts, reusable components, named routes, localization keys, navigation resolution, SEO resolution where needed, and theme tokens. Do not build page builders, arbitrary HTML editors, arbitrary CSS editors, arbitrary JavaScript editors, or static admin screens.

## Out Of Scope For v1

The following systems are removed from the v1 roadmap and prompt list:

- Marketplace
- Community
- SDK
- AI translation
- Semantic or AI search
- Advanced analytics

Do not create modules, tables, DTOs, services, jobs, routes, admin preparations, compatibility layers, placeholder abstractions, or future hooks for these systems in v1.

## Cleanup Rules

Remove future-compatibility clutter from prompts. A prompt must implement only its own current scope.

Avoid unnecessary provider lists. For v1, prefer a contract plus one real driver/provider where needed. Do not require S3, R2, B2, MinIO, Stripe, Paddle, and LemonSqueezy all at once.

Do not audit low-value high-volume events such as query executed, check executed, documentation viewed, or every successful delivery. Audit privileged, security, compliance, and materially important lifecycle events.

Do not force DTOs and interfaces for every tiny class. Use contracts, DTOs, enums, services, and repositories only where they reduce real complexity or protect a module boundary.

Remove any link between payment status and trust. Premium, paid, or subscribed users must not receive reputation or trust bonuses merely for paying.

Do not use global public cache blobs for user, locale, role, plan, visibility, or privacy-dependent output. Cache keys must include the required context or avoid caching sensitive output.

## Added Standards

### Prompt Header Requirement

Every execution prompt must explicitly include:

- Target stack: Laravel 13, PHP 8.5.7, Blade, Tailwind CSS, Alpine.js, Vite, MySQL/MariaDB-compatible migrations, Laravel scheduler/cron-compatible jobs, Laravel policies/gates, validation, and tests.
- Required `docs/` files to read before implementation.
- The relevant constitution, architecture, checklist, and prompt documents.
- A reminder that constitution rules override prompt instructions.
- A reminder to stop and report if required docs are missing instead of inventing architecture.

### Scope Discipline

Each prompt must do one bounded job. It must not implement another module's responsibility.

### One Owner Per Concept

Every major concept must have exactly one owning module. Other modules may consume it through a service, resolver, contract, or DTO, but must not recreate it.

### No Placeholder Modules

Do not create empty modules, tables, routes, services, jobs, DTOs, enums, or admin screens for future features.

### Evidence Required

Every implementation prompt must finish with evidence: tests, command output, generated report, or a concrete verification note.

### Shared Hosting Runtime Contract

- No daemon-only design.
- No mandatory long-running workers.
- Jobs must be cron-compatible.
- Batch work must be bounded, idempotent, resumable, and lock-aware.
- Heavy work must not run synchronously during public requests.

### Security Baseline

Every relevant prompt must preserve:

- CSRF protection
- Rate limiting
- Server-side validation
- Authorization through policies/gates where needed
- Output escaping
- Secret masking
- Path protection
- Signed URL rules where private files are served
- Sanitized logging

### Data Lifecycle Baseline

Every data-owning module must define its relationship to:

- Retention
- Cleanup
- Backup
- Export
- Deletion
- Audit
- Privacy and PII minimization

### Laravel 13 Baseline

Do not assume older Laravel structures unless they exist in the project. Use current Laravel conventions for routing, middleware, service container, config, migrations, scheduling, validation, policies/gates, Blade, and tests.

### Public UI Quality Baseline

Public and admin screens must be modern, component-driven, accessible, responsive, and theme-token aware. Foundation screens such as the installer may be simple, but they must not look or behave like disconnected legacy HTML pages. Use the product quality checklist before declaring UI work complete.

## Final Principle

Not fewer features for their own sake. Clean features, clear ownership, and no architectural residue.
