# Product Quality Checklist

Use this checklist when reviewing Temp Mail SaaS v1 features, admin screens, public pages, and launch readiness.

## UX

- Users can create or view a temporary mailbox quickly.
- Primary actions are obvious: copy, refresh, change, delete, history.
- Destructive actions require confirmation.
- Empty, loading, success, and error states are clear.
- Admin flows follow setup dependency order instead of random menu order.

## UI

- Screens use Blade layouts and reusable components.
- Tailwind CSS and theme tokens are used consistently.
- Public pages do not rely on raw standalone HTML.
- Admin pages do not use static HTML screens.
- Layout remains clean with long text, missing content, and small screens.

## Accessibility

- Keyboard navigation works for forms, menus, modals, and mailbox controls.
- Focus states are visible.
- Icon-only buttons have accessible labels.
- Color contrast is readable.
- Loading states are announced where practical.
- Motion respects `prefers-reduced-motion`.

## SEO

- Public pages have localized title and description.
- Canonical URLs are correct.
- Hreflang is emitted only for active valid locales.
- Inactive locales are not exposed publicly.
- Sitemap includes only public, indexable content.
- Empty sections do not emit misleading schema.

## Performance

- No heavy frontend framework is required.
- Public pages use bounded queries.
- Teaser sections use limits.
- Large images and blocking scripts are avoided.
- Ads are contained and do not break rendering.
- Cache keys include locale, theme, visibility, and published config version where needed.

## Security

- CSRF protection is used on state-changing forms.
- Inputs are validated with Laravel validation.
- Output is escaped in Blade.
- Secrets are masked.
- Raw exceptions and raw request arrays are not logged.
- Admin routes use policies/gates.
- Business logic is not placed in Blade.

## Privacy

- Mailbox history is never scoped by raw IP alone.
- Session/device/user ownership is respected.
- Deleted, expired, quarantined, or retention-cleaned mailbox data is not exposed.
- Public pages do not reveal provider, DNS, domain health, or admin internals.

## Localization

- UI text uses localization keys.
- Active languages appear publicly.
- Passive languages do not appear publicly.
- Missing translations use documented fallback behavior.
- Content ownership remains with the relevant content module.

## RTL / LTR

- Locale direction renders through `dir="ltr"` or `dir="rtl"`.
- Layout uses start/end logical alignment where practical.
- Header, footer, forms, mailbox controls, FAQ, and ads work in RTL.
- Font presets support LTR and RTL separately.

## Content

- Legal, FAQ, documentation, blog, and support content render only when public and published.
- Empty blog, FAQ, plans, docs, or KB sections do not show empty shells.
- Content modules own their own localized content.

## Conversion

- Plan and CTA sections are visible only when configured and backed by valid public data.
- Premium messaging does not hide the core temp-mail tool.
- Trust/security sections explain retention, privacy, and abuse boundaries.

## Mobile Responsiveness

- Mailbox generator works on mobile.
- Inbox preview remains usable on mobile.
- Modals fit small screens.
- Ads do not overflow.
- Header/footer remain readable.

## Error, Empty, And Loading States

- Inbox empty state is clear.
- Refresh/checking state is visible.
- Provider unavailable state is safe and non-technical.
- Rate limit state explains when to retry.
- Validation errors are user-facing and safe.

## Admin Usability

- Admin menu follows clear ownership boundaries.
- Setup screens explain what is complete and what remains.
- Appearance changes support preview/publish where practical.
- Dangerous changes have confirmation.
- Admin screens avoid debug-like presentation.

## Mailbox Experience

- Refresh checks messages only and does not change the address.
- Change validates username and active public domain.
- Delete confirms, expires/deletes current mailbox, and creates a replacement mailbox.
- History supports search, select, select all, delete selected, and current action.
- Current can switch only to eligible active mailboxes.

## Trust And Safety

- Abuse prevention exists for suspicious behavior.
- Rate limits protect refresh, change, delete, and history actions.
- Quarantine rules prevent unsafe message exposure.
- Audit records meaningful security/admin events without noisy spam.

## Data Lifecycle

- Retention rules are documented and enforced.
- Cleanup is bounded and idempotent.
- Backup, export, and deletion relationships are documented.
- Deleted/retention-cleaned data is not presented as recoverable.

## Compliance

- Privacy, terms, cookie/legal, abuse, and contact pages exist where required.
- Admin access control is documented.
- Audit evidence expectations are documented.
- Backup handling is documented.

## Monitoring And Operations

- Health checks are bounded and shared-hosting compatible.
- Scheduler/cron expectations are documented.
- Backup freshness is visible where implemented.
- Public status does not leak internals.

## Shared Hosting Compatibility

- No Redis, Laravel Horizon, Supervisor, Reverb, or daemon requirement.
- Jobs are bounded and cron-compatible.
- Installer does not require shell-only destructive browser actions.
- File paths stay outside public exposure.

## Maintainability

- Each concept has one owner.
- Modules consume other owners through contracts/services/query objects.
- No placeholder future modules are created.
- DTOs/interfaces are used only where they reduce real complexity.

## Testability

- New behavior has focused tests.
- Security and permission boundaries are tested.
- Localization and RTL/LTR behavior are tested where practical.
- Empty/data-aware section behavior is tested.

## Documentation

- Architecture decisions are documented.
- User-facing behavior is documented.
- Admin setup and runbooks are documented.
- Completion reports include self-audit summaries.

## Launch Readiness

- Installer state is complete and locked.
- Health checks pass.
- Mailbox flow is tested.
- Legal pages are present.
- SEO/sitemap/localization are verified.
- Backup/restore runbook exists.
- Production hardening checklist is complete.
