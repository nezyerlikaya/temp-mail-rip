<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('template_key', 120);
            $table->string('locale_code', 16);
            $table->unsignedInteger('version');
            $table->string('status', 32);
            $table->string('subject', 220);
            $table->mediumText('body');
            $table->string('format', 16);
            $table->json('placeholder_schema');
            $table->string('purpose', 240);
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->unique(['template_key', 'locale_code', 'version'], 'email_templates_key_locale_version_unique');
            $table->index(['template_key', 'locale_code', 'status'], 'email_templates_runtime_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
