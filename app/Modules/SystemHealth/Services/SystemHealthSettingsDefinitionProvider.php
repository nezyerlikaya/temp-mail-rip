<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class SystemHealthSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'systemhealth.warning_timeout_ms',
                type: SettingType::Integer,
                default: 750,
                validationRules: ['value' => ['required', 'integer', 'min:50', 'max:10000']],
                description: 'Duration in milliseconds after which a lightweight health check should warn.',
                group: 'system_health',
            ),
            new SettingDefinition(
                key: 'systemhealth.critical_timeout_ms',
                type: SettingType::Integer,
                default: 3000,
                validationRules: ['value' => ['required', 'integer', 'min:100', 'max:10000']],
                description: 'Duration in milliseconds after which a lightweight health check should become degraded.',
                group: 'system_health',
            ),
            new SettingDefinition(
                key: 'systemhealth.scheduler_heartbeat_max_age_seconds',
                type: SettingType::DurationSeconds,
                default: 300,
                validationRules: ['value' => ['required', 'integer', 'min:60', 'max:86400']],
                description: 'Allowed scheduler heartbeat age for cron readiness checks.',
                group: 'system_health',
            ),
            new SettingDefinition(
                key: 'systemhealth.public_endpoint_enabled',
                type: SettingType::Boolean,
                default: false,
                validationRules: ['value' => ['required', 'boolean']],
                description: 'Reserved switch for future public aggregate health exposure. STEP011 does not create a detailed public endpoint.',
                group: 'system_health',
            ),
        ];
    }
}
