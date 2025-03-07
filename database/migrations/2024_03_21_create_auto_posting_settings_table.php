<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_posting_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('prompt_template');
            $table->enum('interval_type', ['minutes', 'hours', 'days', 'weeks'])->default('hours');
            $table->integer('interval_value')->default(1);
            $table->json('posting_schedule')->nullable(); // Для более точного расписания
            $table->boolean('is_active')->default(false);
            $table->json('previous_topics')->nullable();
            $table->timestamp('last_post_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_posting_settings');
    }
}; 