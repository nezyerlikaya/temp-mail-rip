<?php

namespace App\Modules\Uploads\DTOs;

use InvalidArgumentException;

readonly class UploadScopeDefinition
{
    /**
     * @param  list<string>  $allowedExtensions
     * @param  list<string>  $allowedMimeTypes
     */
    public function __construct(
        public string $scope,
        public string $disk,
        public string $directory,
        public string $visibility,
        public array $allowedExtensions,
        public array $allowedMimeTypes,
        public int $defaultMaxBytes,
        public ?int $defaultMaxWidth = null,
        public ?int $defaultMaxHeight = null,
        public ?string $maxBytesSettingKey = null,
    ) {
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $this->scope)) {
            throw new InvalidArgumentException('Upload scopes must be stable snake case identifiers.');
        }

        if (! preg_match('/^[a-z0-9\/_-]+$/', $this->directory) || str_contains($this->directory, '..')) {
            throw new InvalidArgumentException('Upload directories must be safe relative paths.');
        }

        if (! in_array($this->visibility, ['private', 'public'], true)) {
            throw new InvalidArgumentException('Upload visibility must be private or public.');
        }
    }
}
