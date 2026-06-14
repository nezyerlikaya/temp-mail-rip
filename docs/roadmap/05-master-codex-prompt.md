# Temp Mail SaaS v1 - Master Codex Prompt

Use this as the header for every implementation prompt.

```text
You are Codex working on Temp Mail SaaS v1.

Target stack:
- Laravel 13
- PHP 8.5.7
- Blade layouts and Blade components
- Tailwind CSS
- Alpine.js
- Vite
- MySQL/MariaDB-compatible migrations
- Laravel scheduler with shared-hosting cron
- Laravel validation, policies/gates, events, jobs, and tests

Required docs before implementation:
- docs/roadmap/v1-scope-cleanup-rules.md
- docs/roadmap/00-executive-summary.md
- docs/roadmap/02-corrected-master-roadmap.md
- docs/roadmap/03-ownership-matrix.md
- docs/roadmap/04-prompt-list.md
- Any prompt-specific constitution, architecture, checklist, or runbook document referenced by the current prompt.

Rules:
- Docs-first. Read the required docs before editing.
- Constitution, roadmap, and ownership rules override the prompt if there is a conflict.
- If required docs are missing, stop and report the missing docs. Do not invent architecture.
- Do not build raw standalone HTML pages. Use Blade layouts, Blade components, Tailwind CSS, Alpine.js, Vite, named routes, localization keys, navigation resolution, SEO resolution where needed, and theme tokens.
- Keep the application modular. Do not place unrelated responsibilities in one controller, model, service, or file.
- One owner per concept. Consume other modules through their services/resolvers/contracts; do not duplicate their data or logic.
- No placeholder modules, future tables, future DTOs, future routes, future jobs, or compatibility layers.
- v1 out of scope: Marketplace, Community, SDK, AI translation, semantic/AI search, and advanced analytics.
- Shared hosting is mandatory. No daemon-only design, no mandatory long-running workers, no Docker/VPS/Redis-only assumption, and no heavy synchronous public request processing.
- Jobs must be cron-compatible, bounded, idempotent, resumable, and lock-aware.
- Protect PII, secrets, tokens, private files, paths, provider payloads, and audit details.
- Do not audit low-value high-volume events. Audit privileged, security, compliance, and materially important lifecycle events.
- Do not connect payment status to trust or reputation. Premium users do not become more trusted merely by paying.
- Cache must include user, role, locale, plan, visibility, privacy, and route context when those affect output.
- Every prompt must finish with evidence: tests, command output, generated report, or a concrete verification note.

Implementation behavior:
- Inspect the existing project first.
- Prefer existing Laravel conventions and local patterns.
- Keep edits scoped to the current prompt.
- Do not rewrite unrelated files.
- Add tests proportional to risk.
- Use current Laravel 13 conventions; do not assume older Laravel structures unless they exist in the project.
```

## Prompt Template

```text
Prompt XXX - [Name]

Use the Temp Mail SaaS v1 Master Codex Prompt.

Prompt-specific docs:
- docs/roadmap/v1-scope-cleanup-rules.md
- docs/roadmap/00-executive-summary.md
- docs/roadmap/02-corrected-master-roadmap.md
- docs/roadmap/03-ownership-matrix.md
- [add specific docs here]

Goal:
[one bounded goal]

Scope:
- [what to implement]

Forbidden:
- [what not to implement]
- Marketplace, Community, SDK, AI translation, semantic/AI search, advanced analytics
- Raw standalone HTML
- Placeholder modules

Acceptance evidence:
- [tests or verification commands]
- [manual verification if required]
```
