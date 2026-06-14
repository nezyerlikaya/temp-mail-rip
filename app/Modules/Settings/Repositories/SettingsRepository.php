<?php

namespace App\Modules\Settings\Repositories;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Services\SettingValueCaster;
use Illuminate\Support\Facades\DB;

class SettingsRepository
{
    public function __construct(private readonly SettingValueCaster $caster) {}

    /**
     * @return array{value: string|null, type: string, is_sensitive: bool}|null
     */
    public function find(SettingDefinition $definition): ?array
    {
        $row = DB::table('settings')
            ->where('setting_key', $definition->key)
            ->first(['value', 'type', 'is_sensitive']);

        if ($row === null) {
            return null;
        }

        return [
            'value' => $row->value,
            'type' => $row->type,
            'is_sensitive' => (bool) $row->is_sensitive,
        ];
    }

    public function put(SettingDefinition $definition, mixed $value): void
    {
        $serialized = $this->caster->serializeForStorage($definition, $value);
        $now = now();

        DB::transaction(function () use ($definition, $serialized, $now): void {
            $existing = DB::table('settings')
                ->where('setting_key', $definition->key)
                ->lockForUpdate()
                ->exists();

            if ($existing) {
                DB::table('settings')
                    ->where('setting_key', $definition->key)
                    ->update([
                        'type' => $definition->type->value,
                        'value' => $serialized,
                        'is_sensitive' => $definition->isSensitive,
                        'updated_at' => $now,
                    ]);

                return;
            }

            DB::table('settings')->insert([
                'setting_key' => $definition->key,
                'type' => $definition->type->value,
                'value' => $serialized,
                'is_sensitive' => $definition->isSensitive,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    public function delete(SettingDefinition $definition): void
    {
        DB::transaction(function () use ($definition): void {
            DB::table('settings')
                ->where('setting_key', $definition->key)
                ->delete();
        });
    }
}
