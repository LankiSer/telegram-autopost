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
            if (!Schema::hasColumn('posts', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable();
            }
            if (!Schema::hasColumn('posts', 'status')) {
                $table->enum('status', ['draft', 'pending', 'scheduled', 'published', 'failed'])->default('draft');
            }
            if (!Schema::hasColumn('posts', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
            if (!Schema::hasColumn('posts', 'error_message')) {
                $table->text('error_message')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'scheduled_at',
                'status',
                'published_at',
                'error_message'
            ]);
        });
    }
};
