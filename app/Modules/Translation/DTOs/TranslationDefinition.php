<?php

namespace App\Modules\Translation\DTOs;

use InvalidArgumentException;

readonly class TranslationDefinition
{
    /**
     * @param  list<string>  $placeholders
     */
    public function __construct(
        public string $namespace,
        public string $key,
        public array $placeholders = [],
    ) {
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $this->namespace)) {
            throw new InvalidArgumentException('Translation namespaces must use lowercase snake case.');
        }

        if (! preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)*$/', $this->key)) {
            throw new InvalidArgumentException('Translation keys must use lowercase dot notation.');
        }
    }

    public function canonicalKey(): string
    {
        return $this->namespace.'.'.$this->key;
    }
}
