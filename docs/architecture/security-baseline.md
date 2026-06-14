# Security Baseline

The Security module owns shared safety helpers used across Temp Mail SaaS v1. It does not own authentication, authorization, rate limiting, audit center, monitoring, uploads, webhook signature verification, API credentials, or business security rules.

## Secret Classification

Secrets include passwords, tokens, API keys, authorization headers, cookies, session identifiers, database credentials, mail credentials, storage credentials, provider secrets, webhook secrets, and encryption keys.

Sensitive values must not appear in logs, diagnostics, reports, public exceptions, test snapshots, HTML, Alpine state, JavaScript bundles, or data attributes.

## Masking Rules

`App\Modules\Security\Services\SecretMasker` masks sensitive keys case-insensitively and recursively through arrays. It also masks common inline secret forms such as authorization headers and `token=` or `password=` fragments in strings.

Arbitrary objects are not serialized. Object values are represented by a class label only.

## Path Protection

`PathAnonymizer` is for diagnostic output only. It replaces application, storage, vendor, temporary, and common absolute hosting paths with safe labels. It must not be used for real filesystem operations.

## Diagnostic Safety

`SafeDiagnosticsFormatter` centralizes safe diagnostic context. It masks secrets, anonymizes paths, bounds string length, bounds collection size and depth, omits request bodies, provider payloads, raw content, and email-like content, and avoids unsafe object dumps.

## Public Exception Behavior

`SecurityExceptionMapper` maps production failures to safe public messages. Production responses must not expose stack traces, SQL, absolute paths, secrets, internal hostnames, provider credentials, or raw exception messages. Laravel-native validation, authentication, and authorization behavior is left to their owning systems.

## Security Headers

`SecurityHeaders` adds a conservative baseline:

- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `X-Frame-Options: SAMEORIGIN`
- `Permissions-Policy` denying camera, microphone, geolocation, and payment

HSTS is opt-in with `SECURITY_HSTS_ENABLED=true`, requires HTTPS, and is only emitted in production.

## Logging Restrictions

Laravel log channels use a Monolog processor that sanitizes messages, context, and extra values. Do not log request bodies, email bodies, support messages, credentials, tokens, cookies, provider payloads, or raw exceptions.

## Frontend Security

Future Blade work must use escaped output by default. Raw HTML output is prohibited unless explicitly sanitized by a scoped feature. State-changing forms must use CSRF protection and named routes. Secrets must not be emitted into HTML, Alpine state, JavaScript bundles, or data attributes.
