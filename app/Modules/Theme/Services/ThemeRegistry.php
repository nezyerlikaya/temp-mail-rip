<?php

namespace App\Modules\Theme\Services;

use App\Modules\Theme\DTOs\ThemeDefinition;
use App\Modules\Theme\Exceptions\DuplicateThemeException;
use App\Modules\Theme\Exceptions\UnknownThemeException;

class ThemeRegistry
{
    /**
     * @var array<string, ThemeDefinition>
     */
    private array $themes = [];

    public function register(ThemeDefinition $theme): void
    {
        if (isset($this->themes[$theme->key])) {
            throw DuplicateThemeException::forKey($theme->key);
        }

        $this->themes[$theme->key] = $theme;
    }

    public function get(string $key): ThemeDefinition
    {
        return $this->themes[$key] ?? throw UnknownThemeException::forKey($key);
    }

    public function default(): ThemeDefinition
    {
        foreach ($this->themes as $theme) {
            if ($theme->isDefault) {
                return $theme;
            }
        }

        return $this->get('system');
    }
}
