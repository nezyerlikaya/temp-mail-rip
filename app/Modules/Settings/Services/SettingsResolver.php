<?php

namespace App\Modules\Settings\Services;

use App\Modules\Security\Services\SecretMasker;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Exceptions\InvalidSettingValueException;
use App\Modules\Settings\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Cache;

class SettingsResolver
{
    public function __construct(
        private readonly SettingsRegistry $registry,
        private readonly SettingsRepository $repository,
        private readonly SettingValueValidator $validator,
        private readonly SecretMasker $secretMasker,
    ) {}

    public function get(string $key): mixed
    {
        $definition = $this->registry->get($key);

        return Cache::remember($this->cacheKey($definition), now()->addMinutes(5), fn (): mixed => $this->resolveUncached($definition));
    }

    public function put(string $key, mixed $value): mixed
    {
        $definition = $this->registry->get($key);
        $validated = $this->validator->validate($definition, $value);

        $this->repository->put($definition, $validated);
        Cache::forget($this->cacheKey($definition));

        return $validated;
    }

    public function forget(string $key): void
    {
        $definition = $this->registry->get($key);

        $this->repository->delete($definition);
        Cache::forget($this->cacheKey($definition));
    }

    /**
     * @return array<string, mixed>
     */
    public function publicValues(): array
    {
        $values = [];

        foreach ($this->registry->publicDefinitions() as $definition) {
            $values[$definition->key] = $this->get($definition->key);
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    public function diagnostics(string $key): array
    {
        $definition = $this->registry->get($key);
        $value = $this->get($key);

        return [
            'key' => $definition->key,
            'type' => $definition->type->value,
            'is_sensitive' => $definition->isSensitive,
            'is_public' => $definition->isPublic,
            'value' => $definition->isSensitive ? $this->secretMasker->mask($value, 'secret_value') : $value,
        ];
    }

    private function resolveUncached(SettingDefinition $definition): mixed
    {
        $row = $this->repository->find($definition);

        if ($row === null) {
            return $this->validator->validate($definition, $definition->default);
        }

        if ($row['type'] !== $definition->type->value || $row['is_sensitive'] !== $definition->isSensitive) {
            throw InvalidSettingValueException::forKey($definition->key);
        }

        return $this->validator->validate($definition, $row['value']);
    }

    private function cacheKey(SettingDefinition $definition): string
    {
        return 'settings.resolved.'.$definition->key;
    }
}
