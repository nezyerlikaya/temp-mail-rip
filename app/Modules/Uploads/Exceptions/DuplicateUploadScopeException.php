<?php

namespace App\Modules\Uploads\Exceptions;

class DuplicateUploadScopeException extends UploadException
{
    public static function forScope(string $scope): self
    {
        return new self("Duplicate upload scope [{$scope}].");
    }
}
