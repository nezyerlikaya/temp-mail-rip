<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ModuleStructureTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $foundationModules = [
        'Security',
        'Settings',
        'FeatureFlags',
        'Localization',
        'Translation',
        'Navigation',
        'Theme',
        'Admin',
        'Installer',
        'Uploads',
        'SystemHealth',
    ];

    public function test_foundation_modules_are_prepared_without_unowned_business_subdirectories(): void
    {
        $modulesPath = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Modules';
        $allowedSecurityDirectories = ['Http', 'Logging', 'Services'];
        $allowedSettingsDirectories = ['DTOs', 'Enums', 'Exceptions', 'Repositories', 'Services'];
        $allowedFeatureFlagDirectories = ['DTOs', 'Enums', 'Exceptions', 'Services'];

        $this->assertDirectoryExists($modulesPath);

        foreach ($this->foundationModules as $module) {
            $modulePath = $modulesPath.DIRECTORY_SEPARATOR.$module;

            $this->assertDirectoryExists($modulePath);
            $this->assertFileExists($modulePath.DIRECTORY_SEPARATOR.'README.md');

            $children = array_values(array_diff(scandir($modulePath) ?: [], ['.', '..', 'README.md']));

            if ($module === 'Security') {
                $this->assertSame($allowedSecurityDirectories, $children);

                continue;
            }

            if ($module === 'Settings') {
                $this->assertSame($allowedSettingsDirectories, $children);

                continue;
            }

            if ($module === 'FeatureFlags') {
                $this->assertSame($allowedFeatureFlagDirectories, $children);

                continue;
            }

            $this->assertSame([], $children, "The {$module} module should not contain business files before its scoped prompt.");
        }
    }

    public function test_removed_v1_system_modules_are_not_created(): void
    {
        $modulesPath = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Modules';

        foreach (['Marketplace', 'Community', 'SDK', 'AiTranslation', 'SemanticSearch', 'AdvancedAnalytics'] as $module) {
            $this->assertDirectoryDoesNotExist($modulesPath.DIRECTORY_SEPARATOR.$module);
        }
    }

    public function test_composer_declares_module_namespace(): void
    {
        $composerPath = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'composer.json';
        $composer = json_decode((string) file_get_contents($composerPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('app/Modules/', $composer['autoload']['psr-4']['App\\Modules\\'] ?? null);
    }
}
