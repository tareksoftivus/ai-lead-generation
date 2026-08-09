<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // grant | spend | refund | purchase
            $table->integer('amount'); // signed: positive for grant/refund/purchase, negative for spend
            $table->integer('balance_after');
            $table->string('reason')->nullable();
            $table->nullableMorphs('reference');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
