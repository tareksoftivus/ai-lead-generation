<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->unique()->constrained('places')->cascadeOnDelete();
            $table->string('google_place_id')->unique();
            $table->string('name');
            $table->string('formatted_address')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_type_normalized')->nullable();
            $table->string('location')->nullable();
            $table->string('location_normalized')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('google_category')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->unsignedInteger('review_count')->nullable();
            $table->text('searchable_text_normalized')->nullable();
            $table->text('location_text_normalized')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index('business_type_normalized');
            $table->index('location_normalized');
            $table->index('review_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads_bank');
    }
};
