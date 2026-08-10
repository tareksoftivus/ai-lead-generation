<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('review');
            $table->string('source_type')->default('all');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedInteger('daily_limit')->default(40);
            $table->unsignedInteger('recipients_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('opened_count')->default(0);
            $table->unsignedInteger('replied_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('lead_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_campaign_id')->constrained('lead_campaigns')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->unique(['lead_campaign_id', 'lead_id']);
            $table->index(['lead_campaign_id', 'status']);
        });

        Schema::create('lead_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('filename');
            $table->string('format')->default('csv');
            $table->string('source_type')->default('all');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_label');
            $table->json('columns');
            $table->json('selected_lead_ids')->nullable();
            $table->boolean('require_email')->default(false);
            $table->unsignedInteger('rows_count')->default(0);
            $table->unsignedInteger('columns_count')->default(0);
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_exports');
        Schema::dropIfExists('lead_campaign_recipients');
        Schema::dropIfExists('lead_campaigns');
    }
};
