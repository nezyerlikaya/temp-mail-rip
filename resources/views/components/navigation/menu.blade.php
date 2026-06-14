@props(['items'])

<nav aria-label="Primary">
    <ul>
        @foreach ($items as $item)
            <li>
                <a href="{{ $item->url }}" @class(['font-semibold' => $item->active])>
                    {{ $item->label }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
