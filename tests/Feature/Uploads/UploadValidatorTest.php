<?php

namespace Tests\Feature\Uploads;

use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\Uploads\Exceptions\UploadValidationException;
use App\Modules\Uploads\Services\FilenameNormalizer;
use App\Modules\Uploads\Services\UploadPathGenerator;
use App\Modules\Uploads\Services\UploadScopeDefinitionProvider;
use App\Modules\Uploads\Services\UploadScopeRegistry;
use App\Modules\Uploads\Services\UploadValidator;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadValidatorTest extends TestCase
{
    private UploadValidator $validator;

    private FakeUploadSettingsResolver $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $registry = new UploadScopeRegistry;

        foreach ((new UploadScopeDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        $this->settings = new FakeUploadSettingsResolver;
        $this->validator = new UploadValidator($registry, new FilenameNormalizer, new UploadPathGenerator, $this->settings);
    }

    public function test_allowed_image_validates_and_metadata_is_safe(): void
    {
        $file = $this->uploadedFile('avatar.png', $this->pngBytes(), 'text/plain');
        $metadata = $this->validator->validate('avatar', $file);

        $this->assertSame('avatar', $metadata->scope);
        $this->assertSame('local', $metadata->disk);
        $this->assertSame('png', $metadata->extension);
        $this->assertSame('image/png', $metadata->mimeType);
        $this->assertSame(1, $metadata->width);
        $this->assertSame(1, $metadata->height);
        $this->assertSame(hash_file('sha256', $file->getRealPath()), $metadata->sha256);
        $this->assertStringStartsWith('uploads/avatar/', $metadata->relativePath);
        $this->assertStringNotContainsString(base_path(), json_encode($metadata, JSON_THROW_ON_ERROR));
    }

    public function test_client_mime_alone_is_not_trusted(): void
    {
        $this->expectException(UploadValidationException::class);

        $this->validator->validate('avatar', $this->uploadedFile('avatar.png', 'not an image', 'image/png'));
    }

    public function test_disallowed_and_dangerous_types_fail(): void
    {
        $this->expectException(UploadValidationException::class);

        $this->validator->validate('avatar', $this->uploadedFile('avatar.svg', '<svg></svg>', 'image/svg+xml'));
    }

    public function test_size_and_dimension_limits_apply_from_settings(): void
    {
        $this->settings->values['uploads.avatar_max_size_bytes'] = 10;

        $this->expectException(UploadValidationException::class);

        $this->validator->validate('avatar', $this->uploadedFile('avatar.png', $this->pngBytes(), 'image/png'));
    }

    public function test_invalid_settings_fail_safely(): void
    {
        $this->settings->values['uploads.max_image_width'] = 0;

        $this->expectException(UploadValidationException::class);

        $this->validator->validate('avatar', $this->uploadedFile('avatar.png', $this->pngBytes(), 'image/png'));
    }

    public function test_raw_uploads_are_private_by_default(): void
    {
        $metadata = $this->validator->validate('media', $this->uploadedFile('media.png', $this->pngBytes(), 'image/png'));

        $this->assertSame('local', $metadata->disk);
        $this->assertStringStartsWith('uploads/media/', $metadata->relativePath);
        $this->assertStringNotContainsString('public', $metadata->relativePath);
    }

    private function uploadedFile(string $name, string $contents, string $clientMime): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upload-test-');
        file_put_contents($path, $contents);

        return new UploadedFile($path, $name, $clientMime, null, true);
    }

    private function pngBytes(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=', true);
    }
}

class FakeUploadSettingsResolver extends SettingsResolver
{
    /**
     * @var array<string, mixed>
     */
    public array $values = [
        'uploads.avatar_max_size_bytes' => 2097152,
        'uploads.media_max_size_bytes' => 5242880,
        'uploads.contact_attachment_max_size_bytes' => 4194304,
        'uploads.max_image_width' => 4096,
        'uploads.max_image_height' => 4096,
    ];

    public function __construct() {}

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? throw new \RuntimeException('missing setting');
    }
}
