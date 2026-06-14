<?php

namespace App\Modules\Security\Services;

class PathAnonymizer
{
    public function anonymize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->anonymize($item), $value);
        }

        if (! is_string($value)) {
            return $value;
        }

        return $this->anonymizeString($value);
    }

    public function anonymizeString(string $value): string
    {
        foreach ($this->knownPathLabels() as $path => $label) {
            if ($path === '') {
                continue;
            }

            $value = str_replace($path, $label, $value);
            $value = str_replace(str_replace('\\', '/', $path), $label, $value);
        }

        $value = preg_replace('/[A-Z]:[\\\\\/](?:Users|inetpub|xampp|laragon|tmp|Temp)[\\\\\/][^\s,"\']+/i', '[path]', $value) ?? $value;
        $value = preg_replace('/\/(?:home|var|tmp|srv)\/[^\s,"\']+/i', '[path]', $value) ?? $value;

        return $value;
    }

    /**
     * @return array<string, string>
     */
    private function knownPathLabels(): array
    {
        return [
            base_path('vendor') => '[vendor]',
            storage_path() => '[storage]',
            base_path() => '[app]',
            sys_get_temp_dir() => '[temp]',
        ];
    }
}
