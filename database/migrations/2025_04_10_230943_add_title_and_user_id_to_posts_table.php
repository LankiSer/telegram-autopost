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
            // Add title column if it doesn't exist
            if (!Schema::hasColumn('posts', 'title')) {
                $table->string('title')->nullable();
            }
            
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('posts', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Only drop the columns if they exist
            if (Schema::hasColumn('posts', 'title')) {
                $table->dropColumn('title');
            }
            
            if (Schema::hasColumn('posts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
