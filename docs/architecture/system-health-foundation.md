# System Health Foundation

STEP011 introduces a lightweight System Health module for internal readiness checks and safe degraded-state awareness. It does not implement Monitoring, Public Status, alerting, incident tracking, Analytics, Audit Center, Backup, or Disaster Recovery.

## Ownership

System Health owns local health check registration, lightweight readiness checks, degraded-state detection, safe result formatting, scheduler/cron readiness, and internal health summaries.

Monitoring consumes sanitized results and owns alert rules and delivery. Public Status owns public-safe component output. Audit Center owns audit records. Analytics is not used for health in v1.

## Registry

Health checks are registered in `HealthCheckRegistry` through `HealthCheckDefinitionProvider`. Each check has a stable dot-notation key, label, bounded timeout expectation, and production-blocking classification. Duplicate keys fail. Unknown keys fail.

Initial checks are `app.boot`, `database.connection`, `cache.availability`, `filesystem.storage`, `filesystem.logs`, `session.driver`, `queue.scheduler`, `mail.configuration`, `assets.vite`, and `installer.lock`.

Checks are intentionally local and bounded. They do not perform port scans, provider uptime polling, heavy filesystem scans, destructive writes, or business health scoring.

## Statuses

Statuses are represented by `HealthStatus`: `healthy`, `warning`, `degraded`, `critical`, and `unknown`.

`degraded` and `critical` block production readiness when the check is production-blocking. Warnings are visible but do not automatically disable application behavior.

## Safe Output

`HealthResultFactory` routes messages and context through the Security module's `SafeDiagnosticsFormatter`. Health results must not expose credentials, raw exception messages, stack traces, SQL strings, absolute paths, hostnames, provider payloads, or `.env` contents.

Internal summaries include sanitized check-by-check output. Public aggregate output, if enabled in a later step, must expose only aggregate status. STEP011 does not create a new detailed public endpoint; the existing Laravel `/up` route remains minimal framework health behavior.

## Internal Commands

`php artisan health:check` runs the sanitized internal check set. It returns a non-zero exit code when production-blocking degraded or critical checks exist.

`php artisan health:check --json` emits sanitized JSON only.

`php artisan health:scheduler-heartbeat` records a heartbeat for cron readiness. It is small enough to be called by shared-hosting cron and does not require a daemon, Supervisor, Redis, or long-running worker.

Recommended shared-hosting cron:

```text
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
*/5 * * * * cd /path/to/app && php artisan health:scheduler-heartbeat >> /dev/null 2>&1
```

Scheduled jobs may call `SchedulerHeartbeat::record()` from the scheduler itself once real scheduled tasks exist.

## Settings

System Health registers only safe thresholds:

- `systemhealth.warning_timeout_ms`
- `systemhealth.critical_timeout_ms`
- `systemhealth.scheduler_heartbeat_max_age_seconds`
- `systemhealth.public_endpoint_enabled`

Settings do not store credentials, provider secrets, API keys, SMTP passwords, or host-private diagnostics.

## Cache Strategy

The cache availability check performs a short-lived write/read/delete probe with a random bounded key. Cache failure returns degraded status; it does not falsely report healthy. Detailed diagnostics are not cached. Scheduler heartbeat uses a single bounded cache key and treats cache read failure as missing heartbeat.

## Shared Hosting

The foundation requires no daemon, Redis, external monitoring SaaS, process control, shell access from public requests, or network scans. Checks are intended for internal commands and internal consumers, not repeated heavy public request work.
