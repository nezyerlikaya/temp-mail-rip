# Legal Pages And Email Templates

STEP010 introduces two bounded foundations: Compliance owns legal document versions and public legal page resolution, while Mail owns safe transactional email template preparation. Notification delivery, preferences, consent tracking, export/deletion requests, marketing email, billing email, and admin editors are outside this step.

## Legal Ownership

Legal documents are stored in `legal_documents` and resolved through the Compliance module. Supported v1 document types are `privacy_policy`, `terms_of_service`, `cookie_policy`, and `acceptable_use_policy`. `data_processing_addendum` is represented as an optional enum value only and is not registered as a v1 public page.

Document statuses are `draft`, `review`, `published`, and `archived`. Public routes render only current published documents with `published_at` set and an effective date that is either empty or in the past. Draft, review, and archived records must not be exposed publicly.

Published legal documents are immutable. Material text changes require a new `document_type + version + locale_code` record. Because MySQL/MariaDB partial unique indexes are not portable, the repository publishes inside a transaction and archives previous published rows for the same type and locale.

## Legal Localization

Legal content is locale-specific long-form content and is not stored in Translation key-value records. UI labels use Translation keys. `legal.fallback_mode` controls whether public legal pages may explicitly fall back to the default locale. The default is `default_locale`; `none` disables fallback.

Public routes are named and deterministic:

- `legal.privacy_policy`
- `legal.terms_of_service`
- `legal.cookie_policy`
- `legal.acceptable_use_policy`

The route paths are defined only in `routes/legal.php`. Other code references route names, not hardcoded URLs.

## Legal Content Safety

Legal content uses a Markdown-like safe source format. The renderer escapes source content by default and rejects scripts, iframes, embedded objects, event attributes, `javascript:` URLs, PHP markers, and raw Blade output. Links are restricted to safe schemes and rendered with `rel="noopener noreferrer"`.

The public Blade page receives sanitized HTML only from `LegalContentSanitizer`. Missing documents return an explicit 404 instead of leaking draft or archived content.

## Email Template Ownership

Email templates are owned by the Mail module. STEP010 prepares system template definitions for early platform flows:

- `account_welcome_preparation`
- `email_verification_preparation`
- `password_reset_preparation`
- `system_notification_preparation`
- `contact_confirmation_preparation`
- `support_update_preparation`

Statuses are `draft`, `review`, `active`, and `archived`. Only active templates are runtime-eligible. Template delivery, queued sending, provider credentials, notification preferences, newsletter systems, and billing emails are not implemented.

The `email_templates` table is prepared for future runtime storage with unique `template_key + locale_code + version`. Active-template uniqueness per key and locale must be enforced by application logic when storage-backed activation is introduced.

## Placeholder Contracts

Each template defines an explicit placeholder list. Rendering rejects missing placeholders and rejects unexpected placeholders when strict mode is enabled. Placeholder names are normalized to lowercase snake case.

Subject values are bounded and CRLF-protected to prevent header injection. Body output is bounded. URL placeholders ending in `_url` must use `http` or `https`. Placeholder values are escaped before interpolation; database template content is not executed as Blade or PHP.

## Diagnostics And Logging

`EmailTemplateResolver` returns diagnostics containing only template key, locale, version, fallback status, and placeholder names. Rendered subjects, bodies, tokens, reset links, signed URLs, and raw placeholder values are not included by default. Diagnostics pass through the Security module's safe formatter.

## Settings Boundary

STEP010 registers only public-safe and non-secret settings:

- `legal.fallback_mode`
- `legal.company_display_name`
- `mail.template_fallback_mode`
- `mail.support_display_name`

SMTP credentials, provider API keys, tokens, reset secrets, and signed URL secrets remain outside Settings.

## Future Boundaries

Future Compliance workflows consume legal document versions from this foundation for consent, export/deletion, and regulatory workflows. Future Notifications consume active approved templates from this foundation for delivery orchestration and preferences. Audit Center may later consume material lifecycle events such as legal document published/archived and email template activated/archived/placeholder-contract changed.
