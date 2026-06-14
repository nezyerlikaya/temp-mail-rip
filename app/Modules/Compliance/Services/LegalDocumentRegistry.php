<?php

namespace App\Modules\Compliance\Services;

use App\Modules\Compliance\DTOs\LegalDocumentDefinition;
use App\Modules\Compliance\Enums\LegalDocumentType;
use App\Modules\Compliance\Exceptions\DuplicateLegalDocumentDefinitionException;
use App\Modules\Compliance\Exceptions\UnknownLegalDocumentException;

class LegalDocumentRegistry
{
    /**
     * @var array<string, LegalDocumentDefinition>
     */
    private array $definitions = [];

    public function register(LegalDocumentDefinition $definition): void
    {
        $key = $definition->type->value;

        if (isset($this->definitions[$key])) {
            throw DuplicateLegalDocumentDefinitionException::forType($key);
        }

        $this->definitions[$key] = $definition;
    }

    public function get(LegalDocumentType|string $type): LegalDocumentDefinition
    {
        $key = $type instanceof LegalDocumentType ? $type->value : $type;

        return $this->definitions[$key] ?? throw UnknownLegalDocumentException::forType($key);
    }

    public function forRoute(string $routeName): LegalDocumentDefinition
    {
        foreach ($this->definitions as $definition) {
            if ($definition->routeName === $routeName) {
                return $definition;
            }
        }

        throw UnknownLegalDocumentException::forType($routeName);
    }

    /**
     * @return list<LegalDocumentDefinition>
     */
    public function requiredForV1(): array
    {
        return array_values(array_filter(
            $this->definitions,
            fn (LegalDocumentDefinition $definition): bool => $definition->requiredForV1,
        ));
    }
}
