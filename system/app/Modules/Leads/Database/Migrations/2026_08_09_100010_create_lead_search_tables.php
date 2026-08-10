<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->json('filters');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('search_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('search_id')->nullable()->constrained('searches')->nullOnDelete();
            $table->json('filters');
            $table->string('status')->default('pending');
            $table->unsignedInteger('results_count')->default(0);
            $table->unsignedInteger('credits_spent')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('google_place_id')->unique();
            $table->string('name');
            $table->string('formatted_address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('google_category')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->unsignedInteger('review_count')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('details_fetched_at')->nullable();
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });

        Schema::create('search_run_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_run_id')->constrained('search_runs')->cascadeOnDelete();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['search_run_id', 'place_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_run_results');
        Schema::dropIfExists('places');
        Schema::dropIfExists('search_runs');
        Schema::dropIfExists('searches');
    }
};
