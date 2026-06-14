<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\Installer\Services\InstallationLock;
use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\SystemHealth\DTOs\HealthCheckDefinition;
use App\Modules\SystemHealth\DTOs\HealthCheckResult;
use App\Modules\SystemHealth\Enums\HealthStatus;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheckDefinitionProvider
{
    public function __construct(
        private readonly HealthResultFactory $results,
        private readonly CacheRepository $cache,
        private readonly InstallationLock $installationLock,
        private readonly SchedulerHeartbeat $heartbeat,
        private readonly SettingsResolver $settings,
    ) {}

    /**
     * @return list<HealthCheckDefinition>
     */
    public function definitions(): array
    {
        return [
            new HealthCheckDefinition('app.boot', 'Application boot', fn (): HealthCheckResult => $this->applicationBoot(), blocksProduction: true),
            new HealthCheckDefinition('database.connection', 'Database connection', fn (): HealthCheckResult => $this->database(), timeoutMs: 1500, blocksProduction: true),
            new HealthCheckDefinition('cache.availability', 'Cache availability', fn (): HealthCheckResult => $this->cache(), timeoutMs: 1000, blocksProduction: true),
            new HealthCheckDefinition('filesystem.storage', 'Storage writability', fn (): HealthCheckResult => $this->writable('filesystem.storage', storage_path('framework/cache'), true), blocksProduction: true),
            new HealthCheckDefinition('filesystem.logs', 'Log writability', fn (): HealthCheckResult => $this->writable('filesystem.logs', storage_path('logs'), true), blocksProduction: true),
            new HealthCheckDefinition('session.driver', 'Session driver readiness', fn (): HealthCheckResult => $this->sessionDriver(), blocksProduction: true),
            new HealthCheckDefinition('queue.scheduler', 'Queue and scheduler readiness', fn (): HealthCheckResult => $this->queueScheduler(), blocksProduction: false),
            new HealthCheckDefinition('mail.configuration', 'Mail configuration', fn (): HealthCheckResult => $this->mailConfiguration(), blocksProduction: false),
            new HealthCheckDefinition('assets.vite', 'Vite build assets', fn (): HealthCheckResult => $this->viteAssets(), blocksProduction: false),
            new HealthCheckDefinition('installer.lock', 'Installer lock status', fn (): HealthCheckResult => $this->installerLock(), blocksProduction: true),
        ];
    }

    private function applicationBoot(): HealthCheckResult
    {
        return $this->results->result(
            key: 'app.boot',
            status: filled(config('app.key')) ? HealthStatus::Healthy : HealthStatus::Critical,
            message: filled(config('app.key')) ? 'Application boot prerequisites are present.' : 'Application key is missing.',
            context: [
                'environment' => app()->environment(),
                'debug' => (bool) config('app.debug'),
            ],
        );
    }

    private function database(): HealthCheckResult
    {
        try {
            DB::connection()->getPdo();

            return $this->results->result('database.connection', HealthStatus::Healthy, 'Database connection is reachable.');
        } catch (Throwable $exception) {
            return $this->results->exception('database.connection', $exception);
        }
    }

    private function cache(): HealthCheckResult
    {
        $key = 'system_health:probe:'.bin2hex(random_bytes(6));

        try {
            $this->cache->put($key, 'ok', now()->addMinute());
            $value = $this->cache->get($key);
            $this->cache->forget($key);

            if ($value !== 'ok') {
                return $this->results->result('cache.availability', HealthStatus::Degraded, 'Cache probe did not return the expected value.');
            }

            return $this->results->result('cache.availability', HealthStatus::Healthy, 'Cache store accepts bounded read/write probes.');
        } catch (Throwable $exception) {
            return $this->results->exception('cache.availability', $exception);
        }
    }

    private function writable(string $key, string $path, bool $blocksProduction): HealthCheckResult
    {
        if (! is_dir($path) || ! is_writable($path)) {
            return $this->results->result($key, HealthStatus::Critical, 'Required application path is not writable or missing.', context: [
                'path' => $path,
            ], blocksProduction: $blocksProduction);
        }

        $probe = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'health-'.bin2hex(random_bytes(6)).'.tmp';

        try {
            file_put_contents($probe, 'ok', LOCK_EX);
            $ok = is_file($probe) && trim((string) file_get_contents($probe)) === 'ok';
        } catch (Throwable $exception) {
            return $this->results->exception($key, $exception, blocksProduction: $blocksProduction);
        } finally {
            if (isset($probe) && is_file($probe)) {
                @unlink($probe);
            }
        }

        return $this->results->result(
            $key,
            $ok ? HealthStatus::Healthy : HealthStatus::Degraded,
            $ok ? 'Required application path accepts temporary write probes.' : 'Temporary write probe did not verify successfully.',
            blocksProduction: $blocksProduction,
        );
    }

    private function sessionDriver(): HealthCheckResult
    {
        $driver = (string) config('session.driver');
        $supported = ['file', 'cookie', 'database', 'array'];
        $risky = ['redis', 'memcached', 'dynamodb'];

        if (in_array($driver, $supported, true)) {
            return $this->results->result('session.driver', HealthStatus::Healthy, 'Session driver is shared-hosting compatible.', context: [
                'driver' => $driver,
            ]);
        }

        return $this->results->result(
            'session.driver',
            in_array($driver, $risky, true) ? HealthStatus::Warning : HealthStatus::Unknown,
            'Session driver needs shared-hosting compatibility review.',
            context: ['driver' => $driver],
            blocksProduction: false,
        );
    }

    private function queueScheduler(): HealthCheckResult
    {
        $queue = (string) config('queue.default');
        $age = $this->heartbeat->ageSeconds();
        $maxAge = $this->settingInt('systemhealth.scheduler_heartbeat_max_age_seconds', 300);

        if ($age === null) {
            return $this->results->result(
                'queue.scheduler',
                HealthStatus::Warning,
                'Scheduler heartbeat has not been recorded yet.',
                context: [
                    'queue' => $queue,
                    'cron' => 'php artisan schedule:run',
                ],
                blocksProduction: false,
            );
        }

        return $this->results->result(
            'queue.scheduler',
            $age <= $maxAge ? HealthStatus::Healthy : HealthStatus::Degraded,
            $age <= $maxAge ? 'Scheduler heartbeat is fresh.' : 'Scheduler heartbeat is older than the allowed threshold.',
            context: [
                'queue' => $queue,
                'heartbeat_age_seconds' => $age,
                'max_age_seconds' => $maxAge,
            ],
            blocksProduction: false,
        );
    }

    private function mailConfiguration(): HealthCheckResult
    {
        $mailer = (string) config('mail.default');
        $from = (string) config('mail.from.address');

        if ($mailer === '') {
            return $this->results->result('mail.configuration', HealthStatus::Warning, 'Mail transport is not configured.', blocksProduction: false);
        }

        return $this->results->result(
            'mail.configuration',
            filter_var($from, FILTER_VALIDATE_EMAIL) ? HealthStatus::Healthy : HealthStatus::Warning,
            filter_var($from, FILTER_VALIDATE_EMAIL) ? 'Mail configuration contains public-safe sender metadata.' : 'Mail sender address needs review.',
            context: [
                'mailer' => $mailer,
                'from_configured' => $from !== '',
            ],
            blocksProduction: false,
        );
    }

    private function viteAssets(): HealthCheckResult
    {
        return is_file(public_path('build/manifest.json'))
            ? $this->results->result('assets.vite', HealthStatus::Healthy, 'Vite build manifest exists.', blocksProduction: false)
            : $this->results->result('assets.vite', HealthStatus::Warning, 'Vite build manifest is missing; build assets during deployment.', blocksProduction: false);
    }

    private function installerLock(): HealthCheckResult
    {
        return $this->installationLock->locked()
            ? $this->results->result('installer.lock', HealthStatus::Healthy, 'Installer is locked.')
            : $this->results->result('installer.lock', HealthStatus::Warning, 'Installer is not locked yet.', blocksProduction: true);
    }

    private function settingInt(string $key, int $fallback): int
    {
        try {
            return (int) $this->settings->get($key);
        } catch (Throwable) {
            return $fallback;
        }
    }
}
