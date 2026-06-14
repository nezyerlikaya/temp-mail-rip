@props(['locale' => 'en', 'direction' => 'ltr', 'theme' => 'light'])

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}" data-theme="{{ $theme }}">
    <body {{ $attributes->merge(['class' => 'bg-[var(--color-bg)] text-[var(--color-fg)]']) }}>
        {{ $slot }}
    </body>
</html>
