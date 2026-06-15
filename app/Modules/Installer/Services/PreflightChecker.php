<?php

namespace App\Modules\Installer\Services;

use App\Modules\Installer\DTOs\PreflightCheckResult;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use Illuminate\Support\Facades\DB;
use Throwable;

class PreflightChecker
{
    public function __construct(
        private readonly InstallationLock $lock,
        private readonly SafeDiagnosticsFormatter $diagnostics,
    ) {}

    /**
     * @return list<PreflightCheckResult>
     */
    public function run(): array
    {
        return [
            $this->environmentFile(),
            $this->phpVersion(),
            $this->extensions(),
            $this->appKey(),
            $this->debugMode(),
            $this->database(),
            $this->migrations(),
            $this->writable('storage', storage_path()),
            $this->writable('cache', base_path('bootstrap/cache')),
            $this->writable('logs', storage_path('logs')),
            $this->viteAssets(),
            $this->scheduler(),
            $this->lockState(),
        ];
    }

    private function environmentFile(): PreflightCheckResult
    {
        return $this->lock->environmentFileExists()
            ? $this->ok('environment.file', 'Environment file', '.env file is present.')
            : $this->blocker('environment.file', 'Environment file', '.env file is missing. Create it from .env.example before completing installation.');
    }

    private function phpVersion(): PreflightCheckResult
    {
        return version_compare(PHP_VERSION, '8.5.7', '>=')
            ? $this->ok('php.version', 'PHP version', 'PHP runtime is compatible.')
            : $this->blocker('php.version', 'PHP version', 'PHP 8.5.7 or newer within the approved 8.5 line is required.');
    }

    private function extensions(): PreflightCheckResult
    {
        $missing = array_values(array_filter(['pdo', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json'], fn (string $extension): bool => ! extension_loaded($extension)));

        return $missing === []
            ? $this->ok('php.extensions', 'PHP extensions', 'Required PHP extensions are available.')
            : $this->blocker('php.extensions', 'PHP extensions', 'Missing required PHP extensions: '.implode(', ', $missing).'.');
    }

    private function appKey(): PreflightCheckResult
    {
        return filled(config('app.key'))
            ? $this->ok('app.key', 'Application key', 'APP_KEY is present.')
            : $this->blocker('app.key', 'Application key', 'APP_KEY is missing. Generate it with Laravel before completing installation.');
    }

    private function debugMode(): PreflightCheckResult
    {
        if (app()->environment('production') && config('app.debug')) {
            return $this->blocker('app.debug', 'Debug mode', 'APP_DEBUG must be false in production.');
        }

        return config('app.debug')
            ? $this->warning('app.debug', 'Debug mode', 'Debug mode is enabled. Disable it before production launch.')
            : $this->ok('app.debug', 'Debug mode', 'Debug mode is disabled.');
    }

    private function database(): PreflightCheckResult
    {
        try {
            DB::connection()->getPdo();

            return $this->ok('database.connection', 'Database connection', 'Database connection is reachable.');
        } catch (Throwable $exception) {
            return $this->blocker('database.connection', 'Database connection', 'Database is not reachable: '.$this->safeException($exception));
        }
    }

    private function migrations(): PreflightCheckResult
    {
        try {
            $ran = DB::table('migrations')->count();

            return $ran > 0
                ? $this->ok('database.migrations', 'Migrations', 'Migration table is present.')
                : $this->warning('database.migrations', 'Migrations', 'Migration table exists but no migrations are recorded.');
        } catch (Throwable $exception) {
            return $this->warning('database.migrations', 'Migrations', 'Migration status could not be verified: '.$this->safeException($exception));
        }
    }

    private function writable(string $key, string $path): PreflightCheckResult
    {
        return is_dir($path) && is_writable($path)
            ? $this->ok('filesystem.'.$key, ucfirst($key).' path', 'Required application path is writable.')
            : $this->blocker('filesystem.'.$key, ucfirst($key).' path', 'Required application path is not writable or missing.');
    }

    private function viteAssets(): PreflightCheckResult
    {
        return is_file(public_path('build/manifest.json'))
            ? $this->ok('assets.vite', 'Frontend assets', 'Vite build manifest exists.')
            : $this->warning('assets.vite', 'Frontend assets', 'Vite build manifest is missing. Build assets during deployment.');
    }

    private function scheduler(): PreflightCheckResult
    {
        return $this->warning('scheduler.cron', 'Scheduler cron', 'Configure shared-hosting cron to run Laravel scheduler once per minute when scheduled tasks are introduced.');
    }

    private function lockState(): PreflightCheckResult
    {
        if ($this->lock->lockFileExists() && ! $this->lock->environmentFileExists()) {
            return $this->warning('installer.lock', 'Installer lock', 'Installer lock exists, but .env is missing. Installation is treated as incomplete.');
        }

        return $this->lock->locked()
            ? $this->ok('installer.lock', 'Installer lock', 'Installer is locked.')
            : $this->warning('installer.lock', 'Installer lock', 'Installer is not locked yet.');
    }

    private function safeException(Throwable $exception): string
    {
        $formatted = $this->diagnostics->format([
            'message' => $exception->getMessage(),
        ]);

        return (string) ($formatted['message'] ?? 'unavailable');
    }

    private function ok(string $key, string $label, string $message): PreflightCheckResult
    {
        return new PreflightCheckResult($key, $label, 'ok', $message);
    }

    private function warning(string $key, string $label, string $message): PreflightCheckResult
    {
        return new PreflightCheckResult($key, $label, 'warning', $message);
    }

    private function blocker(string $key, string $label, string $message): PreflightCheckResult
    {
        return new PreflightCheckResult($key, $label, 'blocker', $message);
    }
}
