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
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('promoted_from_channel_id')->nullable()->references('id')->on('channels')->onDelete('set null');
            $table->boolean('is_cross_promotion')->default(false);
            $table->json('cross_promotion_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['promoted_from_channel_id']);
            $table->dropColumn('promoted_from_channel_id');
            $table->dropColumn('is_cross_promotion');
            $table->dropColumn('cross_promotion_data');
        });
    }
};
