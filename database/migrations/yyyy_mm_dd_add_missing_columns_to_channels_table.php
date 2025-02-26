<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            // Проверяем и добавляем только те колонки, которых еще нет
            if (!Schema::hasColumn('channels', 'type')) {
                $table->string('type')->nullable();
            }
            
            if (!Schema::hasColumn('channels', 'settings')) {
                $table->json('settings')->nullable();
            }
            
            if (!Schema::hasColumn('channels', 'telegram_username')) {
                $table->string('telegram_username')->nullable();
            }
            
            if (!Schema::hasColumn('channels', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable();
            }
            
            if (!Schema::hasColumn('channels', 'content_prompt')) {
                $table->text('content_prompt')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            $columns = ['type', 'settings', 'telegram_username', 'telegram_chat_id', 'content_prompt'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('channels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 