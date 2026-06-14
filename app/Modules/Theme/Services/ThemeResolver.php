<?php

namespace App\Modules\Theme\Services;

use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\Theme\DTOs\ThemeDefinition;

class ThemeResolver
{
    public function __construct(
        private readonly ThemeRegistry $registry,
        private readonly SettingsResolver $settings,
    ) {}

    public function resolve(?string $userPreference = null, ?string $cookiePreference = null, ?string $systemPreference = null): ThemeDefinition
    {
        foreach ([$userPreference, $cookiePreference] as $candidate) {
            if ($candidate !== null && in_array($candidate, ['light', 'dark', 'system'], true)) {
                return $candidate === 'system' ? $this->systemTheme($systemPreference) : $this->registry->get($candidate);
            }
        }

        try {
            $default = $this->settings->get('theme.default');
        } catch (\Throwable) {
            return $this->registry->default();
        }

        return $default === 'system' ? $this->systemTheme($systemPreference) : $this->registry->get((string) $default);
    }

    /**
     * @return array<string, string>
     */
    public function cssVariables(ThemeDefinition $theme): array
    {
        return $theme->tokens;
    }

    private function systemTheme(?string $systemPreference): ThemeDefinition
    {
        return $systemPreference === 'dark' ? $this->registry->get('dark') : $this->registry->get('light');
    }
}
