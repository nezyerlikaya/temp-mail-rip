<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table): void {
            $table->id();
            $table->string('domain', 253)->unique();
            $table->string('display_domain', 253)->nullable();
            $table->string('status', 32);
            $table->string('domain_type', 32);
            $table->boolean('supports_catch_all')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'domain_type'], 'domains_status_type_index');
            $table->index('domain_type', 'domains_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
