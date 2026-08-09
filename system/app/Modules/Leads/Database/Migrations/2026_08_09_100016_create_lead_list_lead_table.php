<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};
