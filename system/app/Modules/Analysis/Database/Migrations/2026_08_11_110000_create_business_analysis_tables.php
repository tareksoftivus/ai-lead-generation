<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_analysis_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lead_list_id')->constrained('lead_lists')->cascadeOnDelete();
            $table->string('focus')->default('gaps');
            $table->boolean('skip_analysed')->default(true);
            $table->string('status')->default('done');
            $table->unsignedInteger('businesses_count')->default(0);
            $table->unsignedInteger('credits_spent')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'lead_list_id']);
        });

        Schema::create('business_analysis_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('business_analysis_run_id')->nullable()->constrained('business_analysis_runs')->nullOnDelete();
            $table->unsignedTinyInteger('score')->default(0);
            $table->text('read');
            $table->string('gap');
            $table->string('fit');
            $table->string('fit_status')->default('maybe');
            $table->json('signals')->nullable();
            $table->timestamp('analysed_at');
            $table->timestamps();

            $table->unique(['user_id', 'lead_id']);
            $table->index(['user_id', 'business_analysis_run_id']);
            $table->index(['user_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_analysis_items');
        Schema::dropIfExists('business_analysis_runs');
    }
};
