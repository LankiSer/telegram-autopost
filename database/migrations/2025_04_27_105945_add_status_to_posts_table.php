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
        // Update posts table values to maintain consistency
        Schema::table('posts', function (Blueprint $table) {
            // Standardize status values - replace 'published' with 'sent' where needed
            DB::statement("UPDATE posts SET status = 'sent' WHERE status = 'published'");
            
            // Copy data from published_at to sent_at if we were to add that column
            // But instead we'll just use published_at consistently throughout the application
            DB::statement("UPDATE posts SET published_at = created_at WHERE published_at IS NULL AND status = 'sent'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot be easily reverted due to data changes
    }
};
