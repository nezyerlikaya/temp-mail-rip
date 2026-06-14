<?php

namespace Tests\Unit\Uploads;

use App\Modules\Uploads\DTOs\UploadScopeDefinition;
use App\Modules\Uploads\Exceptions\UploadValidationException;
use App\Modules\Uploads\Services\FilenameNormalizer;
use App\Modules\Uploads\Services\UploadPathGenerator;
use PHPUnit\Framework\TestCase;

class FilenameAndPathTest extends TestCase
{
    public function test_filenames_are_normalized_and_bounded(): void
    {
        $normalizer = new FilenameNormalizer;
        $filename = $normalizer->sanitizeOriginal(str_repeat('a', 160).'.png');

        $this->assertLessThanOrEqual(120, strlen($filename));
        $this->assertSame('png', $normalizer->extension('avatar.PNG'));
    }

    public function test_dangerous_double_extensions_and_path_traversal_fail(): void
    {
        $normalizer = new FilenameNormalizer;

        $this->expectException(UploadValidationException::class);
        $normalizer->extension('shell.php.png');
    }

    public function test_path_traversal_filename_fails(): void
    {
        $this->expectException(UploadValidationException::class);

        (new FilenameNormalizer)->sanitizeOriginal('../avatar.png');
    }

    public function test_generated_paths_stay_inside_scope(): void
    {
        $scope = new UploadScopeDefinition('avatar', 'local', 'uploads/avatar', 'private', ['png'], ['image/png'], 1024);
        $path = (new UploadPathGenerator)->relativePath($scope, str_repeat('a', 40).'.png');

        $this->assertStringStartsWith('uploads/avatar/', $path);
        $this->assertStringNotContainsString('..', $path);
        $this->assertStringNotContainsString('\\', $path);
    }
}
