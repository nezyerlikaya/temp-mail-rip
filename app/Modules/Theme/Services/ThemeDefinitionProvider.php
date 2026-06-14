<?php

namespace App\Modules\Theme\Services;

use App\Modules\Theme\DTOs\ThemeDefinition;

class ThemeDefinitionProvider
{
    /**
     * @return list<ThemeDefinition>
     */
    public function definitions(): array
    {
        return [
            new ThemeDefinition('light', 'light', $this->lightTokens()),
            new ThemeDefinition('dark', 'dark', $this->darkTokens()),
            new ThemeDefinition('system', 'system', $this->lightTokens(), isDefault: true),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function lightTokens(): array
    {
        return [
            'color-bg' => '#ffffff',
            'color-fg' => '#111827',
            'color-muted' => '#6b7280',
            'color-border' => '#d1d5db',
            'color-focus' => '#2563eb',
            'radius-md' => '8px',
            'shadow-sm' => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function darkTokens(): array
    {
        return [
            'color-bg' => '#111827',
            'color-fg' => '#f9fafb',
            'color-muted' => '#9ca3af',
            'color-border' => '#374151',
            'color-focus' => '#60a5fa',
            'radius-md' => '8px',
            'shadow-sm' => '0 1px 2px 0 rgb(0 0 0 / 0.4)',
        ];
    }
}
