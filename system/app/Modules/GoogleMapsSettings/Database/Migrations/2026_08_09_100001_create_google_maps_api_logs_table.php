<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_maps_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('method', 10);
            $table->string('url');
            $table->json('request_payload')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->boolean('successful')->default(false);
            $table->unsignedSmallInteger('attempts')->default(1);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index(['successful', 'created_at']);
            $table->index('status_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_maps_api_logs');
    }
};
