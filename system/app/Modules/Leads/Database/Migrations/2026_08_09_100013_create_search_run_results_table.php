<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};
