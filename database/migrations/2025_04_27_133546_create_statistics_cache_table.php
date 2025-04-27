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
        Schema::create('statistics_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('type')->index(); // 'global', 'post_analytics', 'channel_detail'
            $table->unsignedBigInteger('entity_id')->nullable()->index(); // channel_id или group_id, если требуется
            $table->json('data'); // JSON с данными статистики
            $table->timestamp('generated_at'); // Время генерации статистики
            $table->timestamps();
            
            // Индекс для быстрого поиска статистики определенного типа для пользователя
            $table->index(['user_id', 'type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics_cache');
    }
};
