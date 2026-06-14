<?php

namespace App\Modules\Security\Services;

use UnitEnum;

class SafeDiagnosticsFormatter
{
    /**
     * @var list<string>
     */
    private array $omittedKeyFragments = [
        'body',
        'content',
        'email',
        'message_html',
        'message_text',
        'payload',
        'raw',
        'request',
    ];

    public function __construct(
        private readonly SecretMasker $secretMasker,
        private readonly PathAnonymizer $pathAnonymizer,
    ) {}

    /**
     * @param  array<mixed>  $context
     * @return array<mixed>
     */
    public function format(array $context): array
    {
        return $this->formatArray($context, 0);
    }

    private function formatValue(mixed $value, ?string $key, int $depth): mixed
    {
        if ($key !== null && $this->shouldOmitKey($key)) {
            return '[omitted]';
        }

        $value = $this->secretMasker->mask($value, $key);

        if (is_array($value)) {
            return $this->formatArray($value, $depth + 1);
        }

        if (is_string($value)) {
            return $this->limitString($this->pathAnonymizer->anonymizeString($value));
        }

        if (is_null($value) || is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        return '[object '.str_replace('\\', '.', $value::class).']';
    }

    /**
     * @param  array<mixed>  $items
     * @return array<mixed>
     */
    private function formatArray(array $items, int $depth): array
    {
        $maxDepth = (int) config('security.diagnostics.max_depth', 4);

        if ($depth >= $maxDepth) {
            return ['__truncated' => 'max_depth'];
        }

        $maxItems = (int) config('security.diagnostics.max_items', 25);
        $formatted = [];
        $index = 0;

        foreach ($items as $key => $value) {
            if ($index >= $maxItems) {
                $formatted['__truncated'] = count($items) - $maxItems;
                break;
            }

            $formatted[$key] = $this->formatValue($value, is_string($key) ? $key : null, $depth);
            $index++;
        }

        return $formatted;
    }

    private function limitString(string $value): string
    {
        $maxLength = (int) config('security.diagnostics.max_string_length', 240);

        if (strlen($value) <= $maxLength) {
            return $value;
        }

        return substr($value, 0, $maxLength).'...[truncated]';
    }

    private function shouldOmitKey(string $key): bool
    {
        $normalized = strtolower(str_replace(['-', '.', ' '], '_', $key));

        foreach ($this->omittedKeyFragments as $fragment) {
            if (str_contains($normalized, $fragment)) {
                return true;
            }
        }

        return false;
    }
}
