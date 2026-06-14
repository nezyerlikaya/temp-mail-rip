<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Settings\Services\SettingsResolver;
use App\Modules\Uploads\DTOs\UploadMetadata;
use App\Modules\Uploads\DTOs\UploadScopeDefinition;
use App\Modules\Uploads\Exceptions\UploadValidationException;
use Illuminate\Http\UploadedFile;
use Throwable;

class UploadValidator
{
    public function __construct(
        private readonly UploadScopeRegistry $scopes,
        private readonly FilenameNormalizer $filenames,
        private readonly UploadPathGenerator $paths,
        private readonly SettingsResolver $settings,
    ) {}

    public function validate(string $scopeName, UploadedFile $file): UploadMetadata
    {
        $scope = $this->scopes->get($scopeName);

        if (! $file->isValid() || ! is_file($file->getRealPath())) {
            throw UploadValidationException::forReason('Uploaded file is not valid.');
        }

        $size = (int) $file->getSize();

        if ($size <= 0) {
            throw UploadValidationException::forReason('Uploaded file is empty.');
        }

        if ($size > $this->maxBytes($scope)) {
            throw UploadValidationException::forReason('Uploaded file exceeds the configured size limit.');
        }

        $originalName = $this->filenames->sanitizeOriginal($file->getClientOriginalName());
        $extension = $this->filenames->extension($originalName);

        if (! in_array($extension, $scope->allowedExtensions, true)) {
            throw UploadValidationException::forReason('Uploaded file extension is not allowed.');
        }

        $mime = $this->detectedMime($file);

        if (! in_array($mime, $scope->allowedMimeTypes, true)) {
            throw UploadValidationException::forReason('Uploaded file MIME type is not allowed.');
        }

        [$width, $height] = $this->imageDimensions($file, $scope);
        $generatedName = $this->filenames->generatedName($extension);

        return new UploadMetadata(
            scope: $scope->scope,
            disk: $scope->disk,
            relativePath: $this->paths->relativePath($scope, $generatedName),
            originalFilename: $originalName,
            generatedFilename: $generatedName,
            sizeBytes: $size,
            mimeType: $mime,
            extension: $extension,
            sha256: hash_file('sha256', $file->getRealPath()),
            width: $width,
            height: $height,
        );
    }

    private function maxBytes(UploadScopeDefinition $scope): int
    {
        if ($scope->maxBytesSettingKey === null) {
            return $scope->defaultMaxBytes;
        }

        try {
            $value = $this->settings->get($scope->maxBytesSettingKey);
        } catch (Throwable) {
            return $scope->defaultMaxBytes;
        }

        if (! is_int($value) || $value <= 0 || $value > 20 * 1024 * 1024) {
            throw UploadValidationException::forReason('Upload size setting is invalid.');
        }

        return $value;
    }

    private function detectedMime(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->getRealPath());

        if (! is_string($mime) || $mime === '') {
            throw UploadValidationException::forReason('Uploaded file type could not be detected.');
        }

        return $mime;
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function imageDimensions(UploadedFile $file, UploadScopeDefinition $scope): array
    {
        if (! str_starts_with($this->detectedMime($file), 'image/')) {
            return [null, null];
        }

        $dimensions = @getimagesize($file->getRealPath());

        if ($dimensions === false) {
            throw UploadValidationException::forReason('Uploaded image dimensions could not be read.');
        }

        [$width, $height] = $dimensions;

        $maxWidth = $this->dimensionLimit('uploads.max_image_width', $scope->defaultMaxWidth);
        $maxHeight = $this->dimensionLimit('uploads.max_image_height', $scope->defaultMaxHeight);

        if (($maxWidth !== null && $width > $maxWidth) || ($maxHeight !== null && $height > $maxHeight)) {
            throw UploadValidationException::forReason('Uploaded image dimensions exceed the configured limit.');
        }

        return [(int) $width, (int) $height];
    }

    private function dimensionLimit(string $settingKey, ?int $fallback): ?int
    {
        try {
            $value = $this->settings->get($settingKey);
        } catch (Throwable) {
            return $fallback;
        }

        if (! is_int($value) || $value < 64 || $value > 8192) {
            throw UploadValidationException::forReason('Upload dimension setting is invalid.');
        }

        return $fallback === null ? $value : min($fallback, $value);
    }
}
