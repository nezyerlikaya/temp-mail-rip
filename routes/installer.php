<?php

use App\Modules\Installer\Http\Middleware\BlockLockedInstaller;
use App\Modules\Installer\Services\InstallationLock;
use App\Modules\Installer\Services\InstallationStateDetector;
use App\Modules\Installer\Services\PreflightChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('install')
    ->name('installer.')
    ->middleware([BlockLockedInstaller::class])
    ->group(function (): void {
        Route::get('/', function (PreflightChecker $preflight, InstallationStateDetector $state) {
            $checks = $preflight->run();

            return view('installer.preflight', [
                'checks' => $checks,
                'state' => $state->detect(),
            ]);
        })->name('preflight');

        Route::post('/lock', function (InstallationLock $lock, PreflightChecker $preflight): RedirectResponse {
            foreach ($preflight->run() as $check) {
                if ($check->blocksInstallation()) {
                    return redirect()
                        ->route('installer.preflight')
                        ->withErrors(['installer' => 'Installation cannot be locked while blocker checks remain.']);
                }
            }

            $lock->create();

            return redirect()->route('installer.locked');
        })->name('lock');
    });

Route::get('/install/locked', fn () => response(view('installer.locked'), 423))->name('installer.locked');
