<?php

namespace App\Modules\DomainHealth\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class DomainHealthSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition(
                key: 'domainhealth.batch_size',
                type: SettingType::Integer,
                default: 25,
                validationRules: ['value' => ['required', 'integer', 'min:1', 'max:250']],
                description: 'Maximum domains checked per scheduled domain-health batch.',
                group: 'domainhealth',
            ),
            new SettingDefinition(
                key: 'domainhealth.timeout_seconds',
                type: SettingType::Integer,
                default: 5,
                validationRules: ['value' => ['required', 'integer', 'min:1', 'max:30']],
                description: 'Expected DNS lookup timeout budget in seconds for operators and future runners.',
                group: 'domainhealth',
            ),
            new SettingDefinition(
                key: 'domainhealth.snapshot_retention_days',
                type: SettingType::Integer,
                default: 30,
                validationRules: ['value' => ['required', 'integer', 'min:1', 'max:365']],
                description: 'Retention window for domain health snapshots.',
                group: 'domainhealth',
            ),
            new SettingDefinition(
                key: 'domainhealth.warning_threshold',
                type: SettingType::Integer,
                default: 65,
                validationRules: ['value' => ['required', 'integer', 'min:0', 'max:100']],
                description: 'Documented score threshold for warning status in formula dns-mx-v1.',
                group: 'domainhealth',
            ),
            new SettingDefinition(
                key: 'domainhealth.degraded_threshold',
                type: SettingType::Integer,
                default: 30,
                validationRules: ['value' => ['required', 'integer', 'min:0', 'max:100']],
                description: 'Documented score threshold for degraded status in formula dns-mx-v1.',
                group: 'domainhealth',
            ),
            new SettingDefinition(
                key: 'domainhealth.check_interval_minutes',
                type: SettingType::Integer,
                default: 15,
                validationRules: ['value' => ['required', 'integer', 'min:1', 'max:1440']],
                description: 'Recommended scheduled domain-health check interval in minutes.',
                group: 'domainhealth',
            ),
        ];
    }
}
