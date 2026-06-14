<?php

namespace App\Modules\Translation\Services;

use App\Modules\Translation\DTOs\TranslationDefinition;
use App\Modules\Translation\Exceptions\DuplicateTranslationKeyException;
use App\Modules\Translation\Exceptions\UnknownTranslationKeyException;

class TranslationNamespaceRegistry
{
    /**
     * @var array<string, TranslationDefinition>
     */
    private array $definitions = [];

    public function register(TranslationDefinition $definition): void
    {
        $canonical = $definition->canonicalKey();

        if (isset($this->definitions[$canonical])) {
            throw DuplicateTranslationKeyException::forKey($canonical);
        }

        $this->definitions[$canonical] = $definition;
    }

    public function get(string $canonicalKey): TranslationDefinition
    {
        return $this->definitions[$canonicalKey] ?? throw UnknownTranslationKeyException::forKey($canonicalKey);
    }
}
