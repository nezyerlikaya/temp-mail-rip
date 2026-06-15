<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_health_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->cascadeOnDelete();
            $table->string('health_status', 32);
            $table->unsignedTinyInteger('health_score');
            $table->string('formula_version', 32);
            $table->boolean('mx_present');
            $table->boolean('dns_visible');
            $table->string('error_code', 64)->nullable();
            $table->timestamp('checked_at');
            $table->timestamp('created_at')->nullable();

            $table->index(['domain_id', 'checked_at'], 'domain_health_snapshots_domain_checked_index');
            $table->index('checked_at', 'domain_health_snapshots_checked_index');
        });

        Schema::create('domain_health_summaries', function (Blueprint $table): void {
            $table->foreignId('domain_id')->primary()->constrained('domains')->cascadeOnDelete();
            $table->string('current_status', 32);
            $table->unsignedTinyInteger('current_score');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->string('last_error_code', 64)->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('current_status', 'domain_health_summaries_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_health_summaries');
        Schema::dropIfExists('domain_health_snapshots');
    }
};
