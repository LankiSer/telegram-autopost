<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            // Добавляем колонки по одной, проверяя их существование
            if (!Schema::hasColumn('channels', 'type')) {
                $table->string('type')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('channels', 'description') && Schema::hasColumn('channels', 'type')) {
                $table->text('description')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('channels', 'settings')) {
                $table->json('settings')->nullable()->after(Schema::hasColumn('channels', 'description') ? 'description' : 'type');
            }
            
            if (!Schema::hasColumn('channels', 'telegram_username')) {
                $table->string('telegram_username')->nullable()->after(Schema::hasColumn('channels', 'settings') ? 'settings' : 'type');
            }
            
            if (!Schema::hasColumn('channels', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->after(Schema::hasColumn('channels', 'telegram_username') ? 'telegram_username' : 'type');
            }
            
            if (!Schema::hasColumn('channels', 'content_prompt')) {
                $table->text('content_prompt')->nullable()->after(Schema::hasColumn('channels', 'telegram_chat_id') ? 'telegram_chat_id' : 'type');
            }
        });
    }

    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            // Удаляем только те колонки, которые существуют
            $columns = ['type', 'settings', 'telegram_username', 'telegram_chat_id', 'content_prompt'];
            
            // Не удаляем description, так как она, возможно, уже существовала до этой миграции
            foreach ($columns as $column) {
                if (Schema::hasColumn('channels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 