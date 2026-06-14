<?php

namespace Tests\Unit\Theme;

use App\Modules\Theme\DTOs\ThemeDefinition;
use App\Modules\Theme\Exceptions\DuplicateThemeException;
use App\Modules\Theme\Exceptions\UnknownThemeException;
use App\Modules\Theme\Services\ThemeDefinitionProvider;
use App\Modules\Theme\Services\ThemeRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ThemeRegistryTest extends TestCase
{
    public function test_themes_register_and_default_resolves(): void
    {
        $registry = new ThemeRegistry;

        foreach ((new ThemeDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        $this->assertSame('system', $registry->default()->key);
        $this->assertSame('dark', $registry->get('dark')->mode);
    }

    public function test_duplicate_and_unknown_themes_fail(): void
    {
        $registry = new ThemeRegistry;
        $theme = new ThemeDefinition('light', 'light', ['color-bg' => '#fff']);
        $registry->register($theme);

        $this->expectException(DuplicateThemeException::class);
        $registry->register($theme);
    }

    public function test_unknown_theme_is_rejected(): void
    {
        $this->expectException(UnknownThemeException::class);

        (new ThemeRegistry)->get('missing');
    }

    public function test_arbitrary_css_or_javascript_tokens_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ThemeDefinition('bad', 'light', ['color-bg' => 'url(javascript:alert(1))']);
    }
}
