<?php

namespace App\Modules\Uploads\DTOs;

readonly class UploadMetadata
{
    public function __construct(
        public string $scope,
        public string $disk,
        public string $relativePath,
        public string $originalFilename,
        public string $generatedFilename,
        public int $sizeBytes,
        public string $mimeType,
        public string $extension,
        public string $sha256,
        public ?int $width = null,
        public ?int $height = null,
    ) {}
}
