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
        Schema::create('channel_channel_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique channel-group combinations
            $table->unique(['channel_id', 'channel_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_channel_group');
    }
};
