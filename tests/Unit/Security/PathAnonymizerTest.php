<?php

namespace Tests\Unit\Security;

use App\Modules\Security\Services\PathAnonymizer;
use Tests\TestCase;

class PathAnonymizerTest extends TestCase
{
    public function test_application_paths_are_anonymized_for_output(): void
    {
        $path = base_path('vendor/package/file.php');
        $anonymized = (new PathAnonymizer)->anonymizeString("Failed at {$path}");

        $this->assertStringContainsString('[vendor]', $anonymized);
        $this->assertStringNotContainsString(base_path(), $anonymized);
    }
}
