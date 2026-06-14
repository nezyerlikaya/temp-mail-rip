@props(['locale' => 'en', 'direction' => 'ltr', 'theme' => 'light', 'title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}" data-theme="{{ $theme }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if ($title)
            <title>{{ $title }}</title>
        @endif
        @if ($description)
            <meta name="description" content="{{ $description }}">
        @endif
    </head>
    <body {{ $attributes->merge(['class' => 'bg-[var(--color-bg)] text-[var(--color-fg)]']) }}>
        {{ $slot }}
    </body>
</html>
