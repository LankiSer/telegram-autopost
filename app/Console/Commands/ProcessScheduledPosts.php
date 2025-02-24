<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class ProcessScheduledPosts extends Command
{
    protected $signature = 'posts:process';
    protected $description = 'Process scheduled posts';

    public function handle(TelegramService $telegram)
    {
        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            try {
                $response = $telegram->sendMessage(
                    $post->channel->telegram_channel_id,
                    $post->content,
                    $post->media
                );

                if ($response->successful()) {
                    $post->update([
                        'status' => 'published',
                        'published_at' => now()
                    ]);
                } else {
                    $post->update([
                        'status' => 'failed',
                        'error_message' => $response->json()['description']
                    ]);
                }
            } catch (\Exception $e) {
                $post->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
        }
    }
} 