# Localization And Translation

Localization owns locale context and resolution. Translation owns key-based UI and system text. Long-form content translations for Blog, Knowledge Base, Documentation, legal documents, SEO, search, and sitemap remain outside STEP005 and belong to their content owner modules.

## Locale Standard

Locale identifiers are normalized as BCP 47-compatible values such as `en`, `tr`, or `en-US`. Initial supported locales are `en`, `tr`, `de`, `fr`, and `es`.

Exactly one default locale is registered. Default locale and fallback locale are separate concepts. Runtime choices such as default locale, fallback locale, cookie lifetime, and default-locale URL prefix policy are Settings-owned values registered by Localization.

## Locale Status

- `active`: selectable and resolvable.
- `hidden`: resolvable but excluded from public selectors.
- `disabled`: not selectable for new requests.
- `deprecated`: must define fallback behavior.

Existing unavailable preferences safely fall back through the resolver.

## Resolution Priority

Locale resolution uses:

1. Validated route locale
2. Authenticated user preference supplied by Auth/Profile integration when available
3. Validated locale cookie
4. Bounded `Accept-Language` parsing
5. System default locale

Unsupported and malformed input is ignored safely.

## Routing Policy

Locale route parameters are validated centrally by `LocaleResolver::validateRouteLocale`. Route names are not localized. The default locale does not require a URL prefix by default; that policy is controlled by `localization.default_locale_prefix`.

## Translation Rules

Translations use canonical keys such as `auth.login` and `mailboxes.create.success`. The namespace is the first segment and ownership is registered in `TranslationNamespaceRegistry`.

Stable application strings use registry/file-style providers in STEP005. Database-backed translation tables are introduced only when runtime management is genuinely required. If database storage is approved, file/provider values must remain the lower-precedence stable source unless documented otherwise.

## Fallback

Runtime lookup order is:

1. Requested locale value
2. Configured locale fallback value
3. Visible missing-key marker appropriate to environment

Fallback never switches namespace silently.

## Placeholders And Safety

Registered placeholder names must match translation placeholders and supplied replacements exactly. Replacement values are escaped. Raw HTML translations are prohibited in this foundation. Translation values must not contain secrets or internal diagnostics.

## Cache

Locale definitions are bounded registry values. Translation values cache per locale and namespace; there is no global translation blob. Invalidation is per locale/namespace through `TranslationResolver::forget`.
