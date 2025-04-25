<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use a different table name to avoid conflicts with the first migration
        if (!Schema::hasTable('gigachat_credentials')) {
            Schema::create('gigachat_credentials', function (Blueprint $table) {
                $table->id();
                $table->string('client_id');
                $table->string('client_secret');
                $table->string('auth_url')->default('https://gigachat.devices.sberbank.ru/api/v1/oauth');
                $table->string('api_url')->default('https://gigachat.devices.sberbank.ru/api/v1/chat/completions');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gigachat_credentials');
    }
}; 