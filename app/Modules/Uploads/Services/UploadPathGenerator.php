<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Uploads\DTOs\UploadScopeDefinition;
use App\Modules\Uploads\Exceptions\UploadValidationException;

class UploadPathGenerator
{
    public function relativePath(UploadScopeDefinition $scope, string $generatedFilename): string
    {
        if (! preg_match('/^[a-f0-9]{40}\.[a-z0-9]+$/', $generatedFilename)) {
            throw UploadValidationException::forReason('Generated filename is invalid.');
        }

        $datePath = now()->format('Y/m/d');
        $path = trim($scope->directory, '/').'/'.$datePath.'/'.$generatedFilename;

        if (str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            throw UploadValidationException::forReason('Generated upload path is invalid.');
        }

        return $path;
    }
}
