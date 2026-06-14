<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Uploads\DTOs\UploadScopeDefinition;
use App\Modules\Uploads\Exceptions\DuplicateUploadScopeException;
use App\Modules\Uploads\Exceptions\UnknownUploadScopeException;

class UploadScopeRegistry
{
    /**
     * @var array<string, UploadScopeDefinition>
     */
    private array $scopes = [];

    public function register(UploadScopeDefinition $definition): void
    {
        if (isset($this->scopes[$definition->scope])) {
            throw DuplicateUploadScopeException::forScope($definition->scope);
        }

        $this->scopes[$definition->scope] = $definition;
    }

    public function get(string $scope): UploadScopeDefinition
    {
        return $this->scopes[$scope] ?? throw UnknownUploadScopeException::forScope($scope);
    }

    /**
     * @return array<string, UploadScopeDefinition>
     */
    public function all(): array
    {
        return $this->scopes;
    }
}
