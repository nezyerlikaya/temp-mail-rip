<?php

namespace App\Modules\Compliance\Services;

use App\Modules\Compliance\Exceptions\UnsafeLegalContentException;

class LegalContentSanitizer
{
    public function toSafeHtml(string $content): string
    {
        $this->assertSafeSource($content);

        $escaped = e($content);
        $escaped = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $escaped) ?? $escaped;
        $escaped = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $escaped) ?? $escaped;
        $escaped = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            function (array $match): string {
                $url = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if (! $this->safeUrl($url)) {
                    throw UnsafeLegalContentException::forReason('unsafe link URL');
                }

                return '<a href="'.e($url).'" rel="noopener noreferrer">'.$match[1].'</a>';
            },
            $escaped,
        ) ?? $escaped;

        $blocks = preg_split('/\R{2,}/', trim($escaped)) ?: [];

        return collect($blocks)
            ->map(function (string $block): string {
                if (preg_match('/^<h[1-3]>/', $block) === 1) {
                    return $block;
                }

                return '<p>'.nl2br($block, false).'</p>';
            })
            ->implode("\n");
    }

    public function assertSafeSource(string $content): void
    {
        if ($content === '' || mb_strlen($content) > 200000) {
            throw UnsafeLegalContentException::forReason('content length is outside allowed bounds');
        }

        $patterns = [
            '/<\s*script/i' => 'script tags are not allowed',
            '/<\s*iframe/i' => 'iframes are not allowed',
            '/<\s*object/i' => 'object tags are not allowed',
            '/<\s*embed/i' => 'embed tags are not allowed',
            '/\son[a-z]+\s*=/i' => 'event attributes are not allowed',
            '/javascript\s*:/i' => 'javascript URLs are not allowed',
            '/<\?(?:php)?/i' => 'PHP execution markers are not allowed',
            '/@\s*php/i' => 'Blade PHP directives are not allowed',
            '/\{!!/i' => 'raw Blade output is not allowed',
        ];

        foreach ($patterns as $pattern => $reason) {
            if (preg_match($pattern, $content) === 1) {
                throw UnsafeLegalContentException::forReason($reason);
            }
        }
    }

    private function safeUrl(string $url): bool
    {
        if (strlen($url) > 2048 || preg_match('/[\r\n]/', $url) === 1) {
            return false;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return $scheme === null || in_array(strtolower((string) $scheme), ['http', 'https', 'mailto'], true);
    }
}
