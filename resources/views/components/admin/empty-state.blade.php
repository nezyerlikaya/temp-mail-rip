@props(['title', 'description'])

<section class="rounded-md border border-dashed border-[var(--color-border)] p-6">
    <h2 class="text-base font-semibold">{{ $title }}</h2>
    <p class="mt-2 text-sm text-[var(--color-muted)]">{{ $description }}</p>
</section>
