@props(['items' => []])

<nav class="mb-4 text-sm text-[var(--color-muted)]" aria-label="Breadcrumb">
    <ol class="flex flex-wrap gap-2">
        @foreach ($items as $item)
            <li>
                <a class="rounded-sm focus:outline focus:outline-2 focus:outline-[var(--color-focus)]" href="{{ $item['url'] }}">
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ol>
</nav>
