# Installer Foundation

The Installer module owns installation state detection, installation lock, bounded preflight checks, environment readiness validation, database readiness validation, filesystem permission checks, scheduler/cron readiness notes, installer-safe diagnostics, and install completion marking.

It does not own authentication, authorization, admin user creation, secret vaulting, deployment automation, backup/restore, business modules, or hosting control panel automation.

## State Model

Installation state combines the installer lock and sanitized preflight checks. A partial installation is not complete. Recovery from interruption is manual: fix blockers, rerun preflight, and create the lock after the application is ready.

## Lock Behavior

The lock is stored under `storage/app/installer/installed.lock`, outside the public web root. Locked installers return HTTP 423 and do not expose the lock path. Browser unlock is intentionally not implemented.

## Preflight Checks

STEP008 checks PHP version, required extensions, APP_KEY presence, debug mode, database connection, migration table readiness, storage/cache/log writability, Vite manifest presence, scheduler cron expectations, and installer lock state.

Checks are lightweight and sanitized. They do not dump `phpinfo`, raw SQL errors, credentials, absolute paths, hostnames, usernames, stack traces, or `.env` contents.

## Environment Policy

The installer validates environment readiness but does not write `.env`. APP_KEY and credentials must be managed through Laravel-native deployment steps and hosting-safe secret handling. Bootstrap secrets must not be stored in Settings.

## Database Policy

The installer validates database reachability and migration status only. It does not drop, truncate, reset, seed, or run destructive migrations from the browser. If shell access is unavailable, deployment documentation must provide safe manual command guidance.

## Routes And Exposure

Installer routes live under `installer.*` names and `/install` URLs. State-changing lock creation uses POST and CSRF through Laravel web middleware. Routes are blocked after lock. Before authentication exists, public exposure remains a deployment risk and must be mitigated by completing the lock promptly.

## Shared Hosting

No Docker, Redis, daemon, privileged filesystem operation, runtime Node.js process, or VPS-only command is required. Cron/scheduler readiness is documented as a hosting panel configuration task.
