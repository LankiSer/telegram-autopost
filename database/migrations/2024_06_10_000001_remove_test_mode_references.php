<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Удаляем тестовый режим из config
        if (Schema::hasTable('configs')) {
            DB::table('configs')
                ->where('key', 'gigachat_test_mode')
                ->delete();
        }

        // Удаляем все кешированные данные тестового режима
        if (Schema::hasTable('cache')) {
            DB::table('cache')
                ->where('key', 'LIKE', '%gigachat_test%')
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Эта миграция не может быть отменена
    }
}; 