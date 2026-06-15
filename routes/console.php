<?php

use App\Modules\DomainHealth\Services\DomainHealthBatchChecker;
use App\Modules\SystemHealth\Services\HealthSummaryResolver;
use App\Modules\SystemHealth\Services\SchedulerHeartbeat;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('health:check {--json : Emit sanitized JSON output}', function (HealthSummaryResolver $health): int {
    $summary = $health->summarize();

    if ($this->option('json')) {
        $this->line(json_encode($summary->toInternalArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    } else {
        $this->line('System health: '.$summary->status->value);

        foreach ($summary->results as $result) {
            $this->line(sprintf(
                '[%s] %s - %s (%dms)',
                $result->status->value,
                $result->key,
                $result->message,
                $result->durationMs,
            ));
        }
    }

    return $summary->hasProductionBlocker() ? 1 : 0;
})->purpose('Run sanitized internal system health checks');

Artisan::command('health:scheduler-heartbeat', function (SchedulerHeartbeat $heartbeat): int {
    if (! $heartbeat->record()) {
        $this->error('Scheduler heartbeat could not be recorded safely.');

        return 1;
    }

    $this->info('Scheduler heartbeat recorded.');

    return 0;
})->purpose('Record a scheduler heartbeat for shared-hosting cron readiness');

Artisan::command('domain-health:check {--limit= : Maximum domains to check in this bounded run}', function (DomainHealthBatchChecker $checker): int {
    $limit = $this->option('limit') !== null ? max(1, (int) $this->option('limit')) : null;
    $snapshots = $checker->run($limit);

    $this->info('Domain health checked: '.count($snapshots));

    return 0;
})->purpose('Run a bounded, cron-compatible domain health batch');
