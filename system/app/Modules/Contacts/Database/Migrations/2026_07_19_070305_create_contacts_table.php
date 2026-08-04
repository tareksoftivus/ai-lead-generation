<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('email');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
