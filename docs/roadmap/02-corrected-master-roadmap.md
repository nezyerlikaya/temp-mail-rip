# Temp Mail SaaS v1 - Corrected Master Roadmap

This roadmap replaces the raw 72-step order. It removes out-of-scope systems, merges duplicated foundations, and moves localization, security, ownership, and shared-hosting rules earlier.

## Phase 0 - Governance And Architecture

1. Establish project constitution, stack baseline, docs-first rule, and shared-hosting constraints.
2. Create module ownership matrix and dependency rules.
3. Define security, privacy, audit, cache, upload, retention, and frontend baselines.
4. Prepare master execution prompt and evidence requirements.

## Phase 1 - Core Platform Foundation

1. Project structure and modular boundaries.
2. Security foundation.
3. Settings registry.
4. Feature flags and feature gate baseline.
5. Localization and translation foundation.
6. Navigation registry.
7. Theme foundation.
8. Admin shell and staff-safe admin foundation.
9. Installer foundation.
10. Scoped uploads baseline.
11. Compliance/legal pages foundation.
12. Email templates and transactional notification baseline.
13. System health baseline.

## Phase 2 - Temp Mail Engine

1. Domain inventory.
2. Domain health.
3. Domain pool management.
4. Provider abstraction for inbound mail.
5. Webhook intake.
6. Payload verification.
7. Message normalization.
8. Mailbox generation.
9. Mailbox lifecycle.
10. Message storage.
11. Attachment metadata through safe upload/media rules.
12. Retention engine.
13. Cleanup engine.
14. Abuse foundation.
15. Rate limit foundation.
16. Quarantine foundation.
17. Transactional mail and notification delivery.
18. Domain health intelligence.
19. Concrete mail provider adapter.

## Phase 3 - SaaS Platform

1. Authentication.
2. Authorization.
3. Staff management.
4. Profile foundation.
5. Avatar system.
6. Public identity foundation.
7. Reputation foundation.
8. Plans foundation.
9. Subscription foundation.
10. Feature gates foundation.
11. API access foundation.
12. Developer portal foundation.
13. Outbound webhook API foundation.

## Phase 4 - Website And Content

1. Media foundation.
2. Knowledge base foundation.
3. Contact center foundation.
4. Blog foundation.
5. Documentation foundation.
6. SEO foundation.
7. Basic indexed search foundation.
8. Sitemap foundation.

## Phase 5 - Globalization And Public Homepage Experience

1. Localization verification for all public/content systems.
2. Translation verification for UI/system text.
3. Atlas homepage theme and Appearance foundation.
4. Horizon homepage theme and mailbox experience.
5. Legacy homepage theme and final homepage verification.

## Operations And Launch Readiness

Monitoring, audit center, public status, compliance, backup, disaster recovery, production hardening, launch readiness, and platform certification remain required product-readiness areas. They must be implemented from their owning architecture/checklist documents without conflicting with STEP053-STEP055, which are reserved for the three homepage themes.

## Merged Or Corrected Areas

- Localization: merge raw STEP007, STEP010, STEP065, STEP066 into one coherent early foundation plus later verification.
- SEO and sitemap: merge raw STEP014, STEP059, STEP064 ownership rules.
- Upload/media/avatar/attachments: use one safe upload/storage/processing baseline.
- Monitoring/system health/public status: system health feeds monitoring; monitoring feeds public status through public-safe mapping.
- Audit: all audit-worthy events flow through one audit center; low-value high-volume events are not audit spam.
- Plans/subscriptions/feature gates: plans define entitlements, subscriptions activate plans, feature gates evaluate access, feature flags handle operational rollout.
- Homepage experience: Atlas, Horizon, and Legacy are distinct compositions, not color-only skins. Appearance owns safe tokens, fonts, sections, and ads; mailbox logic remains owned by the mailbox modules.

## Removed From v1

- Community foundation
- SDK foundation
- Integration marketplace foundation
- AI translation
- Semantic or AI search
- Advanced analytics
