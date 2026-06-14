# Modular Structure

Temp Mail SaaS v1 uses a lightweight Laravel modular monolith. Module folders live under `app/Modules`, while Laravel-native infrastructure remains in the standard framework directories.

## Module Location

The module namespace is `App\Modules\`, mapped to `app/Modules/` in Composer autoloading. STEP001 prepares only these foundation modules:

- `Security`
- `Settings`
- `FeatureFlags`
- `Localization`
- `Translation`
- `Navigation`
- `Theme`
- `Admin`
- `Installer`
- `Uploads`
- `SystemHealth`

No marketplace, community, SDK, AI translation, semantic search, advanced analytics, mailbox, message, provider, authentication, or admin-screen modules are prepared in STEP001.

## Ownership

Each module owns exactly the concept assigned to it by `docs/roadmap/03-ownership-matrix.md`. Other modules must consume that concept through focused services, resolvers, contracts, events, or query objects only when that indirection protects a real boundary.

Do not duplicate another module's data, business rules, lifecycle decisions, validation policy, or presentation ownership.

## Dependency Direction

Modules may depend on Laravel framework services and shared application infrastructure. Cross-module dependencies must point toward the module that owns the concept being consumed.

Avoid circular dependencies. If two modules need to coordinate, use a focused contract, event, resolver, or application service owned by the concept owner.

## Cross-Module Communication

Use the simplest boundary that preserves ownership:

- Direct Laravel services for local, low-risk coordination.
- Contracts when multiple implementations or boundary protection are needed.
- Events for lifecycle notifications.
- Query objects or resolvers for read-only projections.

Do not force DTOs, interfaces, repositories, managers, or events onto trivial operations.

## Shared Infrastructure

Keep framework concerns in Laravel-native locations:

- HTTP controllers, requests, middleware, and Blade entry points stay in `app/Http` and `resources`.
- Eloquent models stay in `app/Models` unless a later prompt explicitly changes that convention.
- Providers stay in `app/Providers` and `bootstrap/providers.php`.
- Routes stay in `routes`.
- Migrations, factories, and seeders stay in `database`.
- Tests stay in `tests`.

Frontend architecture is Blade layouts, reusable Blade components, Tailwind CSS, Alpine.js, Vite, named routes, localization keys, navigation registry integration, and theme tokens. Raw standalone HTML pages and SPA frameworks are outside this foundation.

## Duplicate Implementation Prevention

Before adding a new class, table, route, job, service, policy, registry, or Blade component, identify the owning module from the ownership matrix. If an owner exists, extend or consume that owner instead of creating a parallel implementation.

Removed v1 systems must leave no placeholders, compatibility hooks, tables, routes, DTOs, services, jobs, or admin preparations.

## Responsibility Limits

Files should have one clear responsibility. Do not place unrelated responsibilities in one controller, model, service, provider, helper, Blade view, or module file.

Add internal module areas such as `Contracts`, `DTOs`, `Enums`, `Services`, `Repositories`, `Actions`, `Events`, `Jobs`, or `Policies` only when a later scoped prompt needs them.
