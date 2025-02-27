<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Добавьте новые поля или измените существующие
            // Например:
            // $table->string('new_field')->after('description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Откатите изменения
            // $table->dropColumn('new_field');
        });
    }
}; 