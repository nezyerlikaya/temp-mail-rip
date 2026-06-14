@props([
    'locale',
    'theme',
    'navigation' => [],
    'breadcrumbs' => [],
    'title' => '',
])

<!DOCTYPE html>
<html lang="{{ $locale->code }}" dir="{{ $locale->direction->value }}" data-theme="{{ $theme->key }}">
    <body class="min-h-screen bg-[var(--color-bg)] text-[var(--color-fg)] antialiased">
        <a href="#admin-main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-[var(--color-bg)] focus:px-3 focus:py-2 focus:text-[var(--color-fg)] focus:outline focus:outline-2 focus:outline-[var(--color-focus)]">
            {{ app(\App\Modules\Translation\Services\TranslationResolver::class)->get('admin.shell.skip_to_content', $locale->code) }}
        </a>

        <div class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]">
            <aside class="border-e border-[var(--color-border)] p-4" aria-label="{{ app(\App\Modules\Translation\Services\TranslationResolver::class)->get('admin.shell.primary_navigation', $locale->code) }}">
                <div class="mb-6 font-semibold">{{ $title }}</div>
                <x-navigation.menu :items="$navigation" />
            </aside>

            <div>
                <header class="border-b border-[var(--color-border)] px-6 py-4">
                    <x-admin.flash :message="app(\App\Modules\Translation\Services\TranslationResolver::class)->get('admin.shell.temporary_access_notice', $locale->code)" />
                </header>

                <main id="admin-main" class="p-6" tabindex="-1">
                    <x-admin.breadcrumbs :items="$breadcrumbs" />
                    <x-admin.page-header :title="$title" />
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
