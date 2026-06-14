<?php

use App\Modules\Admin\Http\Middleware\EnsureAdminShellAccessible;
use App\Modules\Localization\Services\LocaleResolver;
use App\Modules\Navigation\Services\NavigationResolver;
use App\Modules\Theme\Services\ThemeResolver;
use App\Modules\Translation\Services\TranslationResolver;
use Illuminate\Support\Facades\Route;

Route::prefix(config('admin.route_prefix', 'admin'))
    ->name('admin.')
    ->middleware([EnsureAdminShellAccessible::class])
    ->group(function (): void {
        Route::get('/', function (
            LocaleResolver $locales,
            NavigationResolver $navigation,
            ThemeResolver $themes,
            TranslationResolver $translations,
        ) {
            $locale = $locales->resolveFromRequest(request());
            $theme = $themes->resolve(cookiePreference: request()->cookie('theme'), systemPreference: null);

            return view('admin.dashboard', [
                'locale' => $locale,
                'theme' => $theme,
                'navigation' => $navigation->resolve('admin', $locale->code, 'admin.dashboard'),
                'title' => $translations->get('admin.dashboard.title', $locale->code),
                'breadcrumbs' => [
                    ['label' => $translations->get('admin.dashboard.title', $locale->code), 'url' => route('admin.dashboard')],
                ],
            ]);
        })->name('dashboard');
    });
