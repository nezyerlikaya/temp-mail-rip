# Feature Flags And Feature Gates

Feature Flags own operational availability. Feature Gates will later own actor-specific access. A flag answers, "Is this capability operationally available?" A gate answers, "May this actor use this available capability?"

STEP004 implements Feature Flags only. It does not implement plans, subscriptions, entitlements, authorization, user-specific gates, commercial access, UI, or public APIs.

## Ownership

Feature Flags own:

- Operational enabled, disabled, beta, and deprecated states
- Emergency kill switches for optional capabilities
- Deterministic rollout preparation
- Environment-safe defaults
- Runtime flag resolution

Security safeguards, authorization, CSRF, validation, output escaping, rate limits, payload signature verification, audit integrity, secret masking, retention, and legal protections must not be controlled by flags.

## Resolution Order

Resolution is deterministic:

1. Registered flag definition
2. Emergency kill-switch interpretation
3. Valid runtime state override from Settings
4. Deterministic rollout evaluation when the flag is in `beta`
5. Registered safe default
6. Explicit failure for unknown flags

Controllers and Blade views must not reproduce this logic.

## States

- `enabled`: operationally available for non-kill-switch flags.
- `disabled`: operationally unavailable for non-kill-switch flags.
- `beta`: available only through deterministic rollout.
- `deprecated`: operationally unavailable.

For kill switches, `enabled` means the switch is active and removes availability. Kill switches can only remove access; they never grant authorization, plan entitlement, or actor access.

## Settings Integration

Feature definitions remain owned by the Feature Flags module. Runtime overrides are represented as `featureflags.*` Settings definitions owned by Feature Flags and registered into the central Settings Registry. Unknown settings do not become flags.

No feature flag database table is created in STEP004. Settings provides bounded persistence and validation for runtime state where needed.

## Rollout Rules

Rollout uses a stable hash of flag key, flag-specific salt, and a non-PII subject key. Email addresses, raw IP addresses, and user-agent-like strings are rejected. Percentages are bounded from 0 to 100.

Rollout does not implement plans, subscriptions, reputation, groups, marketplace cohorts, A/B testing dashboards, or analytics.

## Cache Strategy

Resolved results are cached per flag and per rollout-subject hash for a short duration. There is no global flag blob. Runtime state updates use Settings cache invalidation; authorized mutation services may also forget a flag resolution key when changing flag state.

Cache failure uses safe behavior. Fail-closed flags become unavailable. Explicit fail-open decisions must be documented on the flag definition, such as keeping the existing public application shell available if cache resolution fails.

## Feature Gates Boundary

Later Feature Gates must combine operational availability with actor-specific access. Feature Gates will not inspect raw plan names directly. Authorization remains owned by Authorization, entitlements by Plans, and active plan state by Subscriptions.

Feature Flags must never replace policies, gates, validation, or authorization checks.
