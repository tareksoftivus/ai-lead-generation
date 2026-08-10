<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->foreignId('search_run_id')->nullable()->constrained('search_runs')->nullOnDelete();
            $table->string('status')->default('new');
            $table->string('email')->nullable();
            $table->timestamp('enriched_at')->nullable();
            $table->boolean('enrichment_credit_spent')->default(false);
            $table->unsignedTinyInteger('score')->nullable();
            $table->json('score_signals')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'place_id']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('lead_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('source')->default('manual');
            $table->text('note')->nullable();
            $table->foreignId('search_run_id')->nullable()->constrained('search_runs')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('lead_list_lead', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_list_id')->constrained('lead_lists')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['lead_list_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_list_lead');
        Schema::dropIfExists('lead_lists');
        Schema::dropIfExists('leads');
    }
};
