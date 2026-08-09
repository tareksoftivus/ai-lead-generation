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
            $table->string('status')->default('new'); // new | contacted | replied | qualified | lost
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
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
