<?php

namespace App\Modules\Installer\Services;

class InstallationLock
{
    public function path(): string
    {
        return storage_path('app/installer/installed.lock');
    }

    public function locked(): bool
    {
        return is_file($this->path());
    }

    public function create(): void
    {
        $directory = dirname($this->path());

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $handle = fopen($this->path(), 'x');

        if ($handle === false) {
            return;
        }

        fwrite($handle, json_encode([
            'locked_at' => now()->toIso8601String(),
            'app' => config('app.name'),
        ], JSON_THROW_ON_ERROR));
        fclose($handle);
    }
}
