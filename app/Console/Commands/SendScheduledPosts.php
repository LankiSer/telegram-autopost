<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendScheduledPosts extends Command
{
    protected $signature = 'telegram:send-scheduled-posts';
    protected $description = 'Send scheduled posts to Telegram channels';

    public function handle(TelegramService $telegram)
    {
        $this->info('Starting to send scheduled posts...');

        $posts = Post::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->get();

        $this->info("Found {$posts->count()} posts to send");

        foreach ($posts as $post) {
            try {
                $this->info("Sending post {$post->id} to channel {$post->channel->name}");
                
                // Логируем данные поста
                \Log::info('Attempting to send post', [
                    'post_id' => $post->id,
                    'channel_id' => $post->channel->telegram_channel_id,
                    'content_length' => strlen($post->content)
                ]);

                $result = $telegram->sendMessage(
                    $post->channel->telegram_channel_id,
                    $post->content
                );

                \Log::info('Telegram API response', [
                    'post_id' => $post->id,
                    'result' => $result
                ]);

                if ($result['success']) {
                    $post->update([
                        'status' => 'published',
                        'published_at' => now(),
                    ]);
                    $this->info("Successfully sent post {$post->id}");
                } else {
                    $post->update([
                        'status' => 'failed',
                        'error_message' => $result['error'] ?? 'Unknown error'
                    ]);
                    $this->error("Failed to send post {$post->id}: {$result['error']}");
                    \Log::error('Failed to send post', [
                        'post_id' => $post->id,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }
            } catch (\Exception $e) {
                $post->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                $this->error("Error sending post {$post->id}: {$e->getMessage()}");
                \Log::error('Error sending post', [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }
} 