# Settings Registry

The Settings module owns runtime-adjustable application settings. It does not own environment/bootstrap secrets, feature access decisions, user profile preferences, notification preferences, plan entitlements, translations, or business data.

## Definition Registration

Every setting must be registered with `SettingsRegistry` before use. Definitions are provided by `SettingsDefinitionProvider` and include:

- Unique dot-notation key
- Declared type
- Default value
- Validation rules
- Sensitive classification
- Public classification
- Optional group and description metadata

Unknown keys fail with `UnknownSettingException`. Duplicate registrations fail with `DuplicateSettingException`.

## Supported Types

Supported types are defined by `SettingType`:

- `string`
- `integer`
- `boolean`
- `decimal`
- `array`
- `duration_seconds`

Durations are stored and returned as integer seconds. Array settings must use JSON-compatible arrays and validation rules for the expected schema. PHP serialization is not used.

## Resolution Order

Resolution is deterministic:

1. Registered definition
2. Stored value when type, sensitivity, casting, and validation are valid
3. Registered default when no stored value exists
4. Explicit failure for unknown, malformed, mismatched, or invalid values

Application code must use `SettingsResolver`; direct table access outside the Settings module is prohibited.

## Sensitive And Public Settings

Sensitive settings are never public. Diagnostic output masks sensitive values through the STEP002 `SecretMasker`.

Public settings are exposed only through the explicit `isPublic` allow-list on each definition. STEP003 does not create a public endpoint, UI, controller, or route.

## Validation

Values are cast by declared type before validation. Booleans accept only explicit boolean values, `true`, `false`, `1`, or `0`. Integers and durations reject loose non-integer strings. Arrays reject malformed JSON and oversized JSON strings.

## Cache Strategy

Resolved values are cached per key with a bounded cache key. There is no global settings blob. Writes and deletes invalidate only the affected key immediately. Cache failures must not make bootstrap-critical secrets depend on database or cache storage.

## Failure Behavior

Unknown keys, invalid stored values, type mismatches, sensitivity mismatches, duplicate registration, and invalid writes fail explicitly. Bootstrap secrets such as `APP_KEY`, database credentials, payment credentials, provider secrets, storage secrets, and encryption keys remain in environment/config storage.

## Naming

Keys use lowercase module-owned dot notation, such as `platform.display_name` or `security.diagnostics_retention_seconds`.
