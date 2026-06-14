<?php

namespace Tests\Feature\Theme;

use App\Modules\Theme\Services\ThemeResolver;
use Tests\TestCase;

class ThemeResolverTest extends TestCase
{
    public function test_theme_preference_resolution_is_deterministic(): void
    {
        $resolver = app(ThemeResolver::class);

        $this->assertSame('dark', $resolver->resolve(userPreference: 'dark')->key);
        $this->assertSame('light', $resolver->resolve(cookiePreference: 'light')->key);
        $this->assertSame('dark', $resolver->resolve(cookiePreference: 'system', systemPreference: 'dark')->key);
    }

    public function test_theme_tokens_render_as_safe_css_variables(): void
    {
        $resolver = app(ThemeResolver::class);
        $tokens = $resolver->cssVariables($resolver->resolve(cookiePreference: 'dark'));

        $this->assertSame('#111827', $tokens['color-bg']);
        $this->assertArrayHasKey('radius-md', $tokens);
    }

    public function test_public_layout_escapes_context_attributes(): void
    {
        $this->blade(
            '<x-layouts.public locale="en&quot; onmouseover=&quot;bad" direction="ltr" theme="dark"><span>Ok</span></x-layouts.public>',
        )
            ->assertSee('lang="en&amp;quot; onmouseover=&amp;quot;bad"', false)
            ->assertDontSee('onmouseover="bad"', false);
    }
}
