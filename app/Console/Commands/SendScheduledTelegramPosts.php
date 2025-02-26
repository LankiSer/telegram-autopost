<?php

namespace App\Console\Commands;

use App\Models\TelegramPost;
use Illuminate\Console\Command;
use App\Http\Controllers\TelegramController;
use Carbon\Carbon;

class SendScheduledTelegramPosts extends Command
{
    protected $signature = 'telegram:send-scheduled-posts';
    protected $description = 'Send scheduled Telegram posts';

    public function handle()
    {
        $posts = TelegramPost::where('status', 'scheduled')
            ->where('scheduled_at', '<=', Carbon::now())
            ->get();

        $telegramController = new TelegramController();
        $count = 0;

        foreach ($posts as $post) {
            $result = $telegramController->sendScheduledPost($post);
            
            if ($result) {
                $count++;
            }
        }

        $this->info("Sent {$count} scheduled posts.");
        
        return 0;
    }
} 