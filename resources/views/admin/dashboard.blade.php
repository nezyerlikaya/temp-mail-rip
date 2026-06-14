<x-layouts.admin :locale="$locale" :theme="$theme" :navigation="$navigation" :breadcrumbs="$breadcrumbs" :title="$title">
    <p class="mb-4 text-sm text-[var(--color-muted)]">
        {{ app(\App\Modules\Translation\Services\TranslationResolver::class)->get('admin.dashboard.foundation_status', $locale->code) }}
    </p>

    <x-admin.empty-state
        :title="app(\App\Modules\Translation\Services\TranslationResolver::class)->get('admin.empty.title', $locale->code)"
        :description="app(\App\Modules\Translation\Services\TranslationResolver::class)->get('admin.empty.description', $locale->code)"
    />
</x-layouts.admin>
