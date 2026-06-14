<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;

class UploadSettingsDefinitionProvider
{
    /**
     * @return list<SettingDefinition>
     */
    public function definitions(): array
    {
        return [
            new SettingDefinition('uploads.avatar_max_size_bytes', SettingType::Integer, 2 * 1024 * 1024, ['value' => ['required', 'integer', 'min:1024', 'max:5242880']], description: 'Avatar upload size limit in bytes.', group: 'uploads'),
            new SettingDefinition('uploads.media_max_size_bytes', SettingType::Integer, 5 * 1024 * 1024, ['value' => ['required', 'integer', 'min:1024', 'max:10485760']], description: 'Media upload size limit in bytes.', group: 'uploads'),
            new SettingDefinition('uploads.contact_attachment_max_size_bytes', SettingType::Integer, 4 * 1024 * 1024, ['value' => ['required', 'integer', 'min:1024', 'max:10485760']], description: 'Contact attachment upload size limit in bytes.', group: 'uploads'),
            new SettingDefinition('uploads.max_image_width', SettingType::Integer, 4096, ['value' => ['required', 'integer', 'min:64', 'max:8192']], description: 'Global image width limit in pixels.', group: 'uploads'),
            new SettingDefinition('uploads.max_image_height', SettingType::Integer, 4096, ['value' => ['required', 'integer', 'min:64', 'max:8192']], description: 'Global image height limit in pixels.', group: 'uploads'),
            new SettingDefinition('uploads.temp_retention_minutes', SettingType::Integer, 60, ['value' => ['required', 'integer', 'min:5', 'max:1440']], description: 'Temporary upload retention in minutes.', group: 'uploads'),
        ];
    }
}
