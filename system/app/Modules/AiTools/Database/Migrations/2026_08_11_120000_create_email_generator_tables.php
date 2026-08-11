<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_generator_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('lead_list_id')->nullable()->constrained('lead_lists')->nullOnDelete();
            $table->foreignId('lead_campaign_id')->nullable()->constrained('lead_campaigns')->nullOnDelete();
            $table->string('scope_type')->default('one');
            $table->string('tone')->default('direct');
            $table->string('length')->default('medium');
            $table->string('opening')->default('gap');
            $table->string('subject');
            $table->text('body');
            $table->string('gap')->nullable();
            $table->string('status')->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'lead_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('email_generator_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('gap_key')->default('any');
            $table->string('tone')->default('direct');
            $table->string('length')->default('medium');
            $table->string('opening')->default('gap');
            $table->string('subject');
            $table->text('body');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'gap_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_generator_templates');
        Schema::dropIfExists('email_generator_drafts');
    }
};
