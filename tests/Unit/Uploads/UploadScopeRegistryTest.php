<?php

namespace Tests\Unit\Uploads;

use App\Modules\Uploads\DTOs\UploadScopeDefinition;
use App\Modules\Uploads\Exceptions\DuplicateUploadScopeException;
use App\Modules\Uploads\Exceptions\UnknownUploadScopeException;
use App\Modules\Uploads\Services\UploadScopeDefinitionProvider;
use App\Modules\Uploads\Services\UploadScopeRegistry;
use PHPUnit\Framework\TestCase;

class UploadScopeRegistryTest extends TestCase
{
    public function test_initial_scopes_register_without_removed_systems(): void
    {
        $registry = new UploadScopeRegistry;

        foreach ((new UploadScopeDefinitionProvider)->definitions() as $definition) {
            $registry->register($definition);
        }

        $this->assertSame([
            'avatar',
            'media',
            'contact_attachment',
            'knowledge_attachment',
            'blog_media',
            'message_attachment_metadata',
        ], array_keys($registry->all()));
        $this->assertArrayNotHasKey('community', $registry->all());
        $this->assertArrayNotHasKey('marketplace', $registry->all());
    }

    public function test_unknown_and_duplicate_scopes_fail(): void
    {
        $registry = new UploadScopeRegistry;
        $definition = new UploadScopeDefinition('avatar', 'local', 'uploads/avatar', 'private', ['png'], ['image/png'], 1024);
        $registry->register($definition);

        $this->expectException(DuplicateUploadScopeException::class);
        $registry->register($definition);
    }

    public function test_unknown_scope_fails(): void
    {
        $this->expectException(UnknownUploadScopeException::class);

        (new UploadScopeRegistry)->get('unknown');
    }
}
