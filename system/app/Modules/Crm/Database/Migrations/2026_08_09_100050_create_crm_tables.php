<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_in_pipeline')->default(true)->after('status');
            $table->timestamp('pipeline_entered_at')->nullable()->after('is_in_pipeline');
        });

        Schema::create('lead_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['lead_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_contacts');

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['is_in_pipeline', 'pipeline_entered_at']);
        });
    }
};
