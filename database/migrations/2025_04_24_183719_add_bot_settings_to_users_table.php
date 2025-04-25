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
        Schema::table('users', function (Blueprint $table) {
            $table->string('openai_api_key')->nullable()->after('remember_token');
            $table->string('telegram_bot_token')->nullable()->after('openai_api_key');
            $table->string('telegram_bot_name')->nullable()->after('telegram_bot_token');
            $table->string('telegram_bot_username')->nullable()->after('telegram_bot_name');
            $table->text('telegram_bot_description')->nullable()->after('telegram_bot_username');
            $table->string('telegram_bot_link')->nullable()->after('telegram_bot_description');
            $table->boolean('is_admin')->default(false)->after('telegram_bot_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'openai_api_key',
                'telegram_bot_token',
                'telegram_bot_name',
                'telegram_bot_username',
                'telegram_bot_description',
                'telegram_bot_link',
                'is_admin',
            ]);
        });
    }
};
