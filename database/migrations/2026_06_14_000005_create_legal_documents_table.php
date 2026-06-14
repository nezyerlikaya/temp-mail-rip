<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $table): void {
            $table->id();
            $table->string('document_type', 80);
            $table->string('slug', 160);
            $table->string('status', 32);
            $table->unsignedInteger('version');
            $table->string('locale_code', 16);
            $table->string('title', 180);
            $table->mediumText('content');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('effective_at')->nullable();
            $table->timestamps();

            $table->unique(['document_type', 'version', 'locale_code'], 'legal_documents_type_version_locale_unique');
            $table->unique(['locale_code', 'slug'], 'legal_documents_locale_slug_unique');
            $table->index(['document_type', 'locale_code', 'status', 'effective_at'], 'legal_documents_public_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
