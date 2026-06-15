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
        <style>
            :root {
                --color-bg: #ffffff;
                --color-fg: #111827;
                --color-muted: #6b7280;
                --color-border: #d1d5db;
                --color-focus: #2563eb;
                --radius-md: 8px;
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                color-scheme: light;
            }

            [data-theme='dark'] {
                --color-bg: #111827;
                --color-fg: #f9fafb;
                --color-muted: #9ca3af;
                --color-border: #374151;
                --color-focus: #60a5fa;
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.4);
                color-scheme: dark;
            }

            html {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                background: var(--color-bg);
                color: var(--color-fg);
            }

            body {
                margin: 0;
                min-height: 100vh;
            }
        </style>
        @if (is_file(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body {{ $attributes->merge(['class' => 'bg-[var(--color-bg)] text-[var(--color-fg)]']) }}>
        {{ $slot }}
    </body>
</html>
