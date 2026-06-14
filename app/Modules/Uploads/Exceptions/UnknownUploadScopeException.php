<?php

namespace App\Modules\Uploads\Exceptions;

class UnknownUploadScopeException extends UploadException
{
    public static function forScope(string $scope): self
    {
        return new self("Unknown upload scope [{$scope}].");
    }
}
