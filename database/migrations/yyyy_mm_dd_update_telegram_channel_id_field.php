<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            // Если поле существует, сделаем его допускающим NULL значения
            if (Schema::hasColumn('channels', 'telegram_channel_id')) {
                $table->string('telegram_channel_id')->nullable()->change();
            }
            // Если поле не существует, добавим его
            else {
                $table->string('telegram_channel_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            if (Schema::hasColumn('channels', 'telegram_channel_id')) {
                $table->string('telegram_channel_id')->nullable(false)->change();
            }
        });
    }
}; 