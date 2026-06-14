<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Uploads\DTOs\UploadScopeDefinition;

class UploadScopeDefinitionProvider
{
    /**
     * @return list<UploadScopeDefinition>
     */
    public function definitions(): array
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $imageMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

        return [
            new UploadScopeDefinition('avatar', 'local', 'uploads/avatar', 'private', $imageExtensions, $imageMimeTypes, 2 * 1024 * 1024, 2048, 2048, 'uploads.avatar_max_size_bytes'),
            new UploadScopeDefinition('media', 'local', 'uploads/media', 'private', $imageExtensions, $imageMimeTypes, 5 * 1024 * 1024, 4096, 4096, 'uploads.media_max_size_bytes'),
            new UploadScopeDefinition('contact_attachment', 'local', 'uploads/contact-attachments', 'private', $imageExtensions, $imageMimeTypes, 4 * 1024 * 1024, 4096, 4096, 'uploads.contact_attachment_max_size_bytes'),
            new UploadScopeDefinition('knowledge_attachment', 'local', 'uploads/knowledge-attachments', 'private', $imageExtensions, $imageMimeTypes, 4 * 1024 * 1024, 4096, 4096),
            new UploadScopeDefinition('blog_media', 'local', 'uploads/blog-media', 'private', $imageExtensions, $imageMimeTypes, 5 * 1024 * 1024, 4096, 4096),
            new UploadScopeDefinition('message_attachment_metadata', 'local', 'uploads/message-attachment-metadata', 'private', $imageExtensions, $imageMimeTypes, 4 * 1024 * 1024, 4096, 4096),
        ];
    }
}
