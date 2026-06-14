<x-layouts.public
    :locale="$locale->code"
    :direction="$locale->direction->value"
    :theme="$theme->key"
    :title="$meta['title']"
    :description="$meta['description']"
>
    <header class="border-b border-[var(--color-border)] px-6 py-4">
        <x-navigation.menu :items="$navigationItems" />
    </header>

    <main class="mx-auto max-w-3xl px-6 py-10">
        <article>
            <header class="mb-8">
                <p class="text-sm text-[var(--color-muted)]">
                    {{ $document->type->value }} · v{{ $document->version }}
                </p>
                <h1 class="mt-2 text-3xl font-semibold">{{ $document->title }}</h1>
                @if ($document->effectiveAt)
                    <p class="mt-3 text-sm text-[var(--color-muted)]">
                        Effective {{ $document->effectiveAt->format('Y-m-d') }}
                    </p>
                @endif
            </header>

            <div class="space-y-4 leading-7 [&_a]:underline [&_h1]:text-3xl [&_h2]:text-2xl [&_h3]:text-xl">
                {!! $document->safeHtml !!}
            </div>
        </article>
    </main>
</x-layouts.public>
