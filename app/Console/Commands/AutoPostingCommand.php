<?php

namespace App\Console\Commands;

use App\Models\AutoPostingSetting;
use App\Services\GigaChatService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoPostingCommand extends Command
{
    protected $signature = 'auto-posting:run';
    protected $description = 'Run auto-posting for channels';

    public function handle(GigaChatService $gigaChat)
    {
        try {
            $now = Carbon::now();
            
            $this->info("Starting auto-posting check at " . $now->format('Y-m-d H:i:s'));

            $settings = AutoPostingSetting::query()
                ->where('is_active', true)
                ->get();

            $this->info("Found " . $settings->count() . " active auto-posting settings");

            $telegram = app(TelegramService::class);
            $tokenInfo = $telegram->getToken();
            $this->info("Checking Telegram configuration:");
            $this->info("Token exists: " . ($tokenInfo['token_exists'] ? 'Yes' : 'No'));
            $this->info("Settings file exists: " . ($tokenInfo['settings_exists'] ? 'Yes' : 'No'));
            \Log::info('Telegram configuration check', $tokenInfo);

            foreach ($settings as $setting) {
                try {
                    $this->info("Processing channel {$setting->channel->name}");
                    $this->info("Last post at: " . ($setting->last_post_at ?? 'never'));
                    $this->info("Interval: {$setting->interval_value} {$setting->interval_type}");
                    
                    if (!$this->shouldPostNow($setting, $now)) {
                        $this->info("Skipping channel {$setting->channel->name} - not in schedule");
                        continue;
                    }

                    if (!$this->isTimeToPost($setting, $now)) {
                        $this->info("Skipping channel {$setting->channel->name} - too early for next post");
                        continue;
                    }

                    $this->info("Generating post for channel {$setting->channel->name}");

                    $previousTopics = $setting->previous_topics ?? [];
                    $post = $gigaChat->generatePost($setting->prompt_template, $previousTopics);
                    
                    // Обработка текста для корректной кодировки
                    $post = mb_convert_encoding($post, 'UTF-8', 'UTF-8');
                    
                    // Extract topic/summary
                    $topic = mb_substr($post, 0, 100, 'UTF-8');
                    
                    // Add to previous topics
                    $previousTopics[] = $topic;
                    if (count($previousTopics) > 50) {
                        array_shift($previousTopics);
                    }

                    // Создаем пост и сразу отправляем его
                    $createdPost = $setting->channel->posts()->create([
                        'content' => $post,
                        'status' => 'pending',
                        'scheduled_at' => now(),
                    ]);

                    // Сразу отправляем пост
                    $result = $telegram->sendMessage(
                        $setting->channel->telegram_channel_id,
                        $post
                    );

                    if ($result['success']) {
                        $createdPost->update([
                            'status' => 'published',
                            'published_at' => now()
                        ]);
                        $this->info("Post {$createdPost->id} published successfully");
                    } else {
                        $createdPost->update([
                            'status' => 'failed',
                            'error_message' => $result['error'] ?? 'Unknown error'
                        ]);
                        $this->error("Failed to publish post {$createdPost->id}: {$result['error']}");
                    }

                    // Обновляем настройки автопостинга
                    $setting->update([
                        'last_post_at' => now(),
                        'previous_topics' => array_map(function($topic) {
                            return mb_convert_encoding($topic, 'UTF-8', 'UTF-8');
                        }, $previousTopics)
                    ]);

                    $this->info("Successfully processed channel {$setting->channel->name}");

                } catch (\Exception $e) {
                    $this->error("Error processing channel {$setting->channel->name}: {$e->getMessage()}");
                    \Log::error('Auto-posting error', [
                        'channel_id' => $setting->channel_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->error("Fatal error in auto-posting: {$e->getMessage()}");
            \Log::error('Fatal auto-posting error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function shouldPostNow(AutoPostingSetting $setting, Carbon $now): bool
    {
        $schedule = $setting->posting_schedule;
        
        // Проверяем день недели
        if (!empty($schedule['days']) && !in_array($now->dayOfWeek, $schedule['days'])) {
            return false;
        }

        // Проверяем время
        $currentTime = $now->format('H:i');
        if ($currentTime < $schedule['start_time'] || $currentTime > $schedule['end_time']) {
            return false;
        }

        return true;
    }

    private function isTimeToPost(AutoPostingSetting $setting, Carbon $now): bool
    {
        if (!$setting->last_post_at) {
            return true;
        }

        $lastPost = Carbon::parse($setting->last_post_at);
        $interval = $setting->interval_value;

        // Добавим отладочную информацию
        $this->info("Last post: " . $lastPost->format('Y-m-d H:i:s'));
        $this->info("Current time: " . $now->format('Y-m-d H:i:s'));
        $this->info("Interval: {$interval} {$setting->interval_type}");
        
        $diff = match ($setting->interval_type) {
            'minutes' => $lastPost->diffInMinutes($now),
            'hours' => $lastPost->diffInHours($now),
            'days' => $lastPost->diffInDays($now),
            'weeks' => $lastPost->diffInWeeks($now),
            default => 0
        };
        
        $this->info("Time difference: {$diff} {$setting->interval_type}");
        $shouldPost = $diff >= $interval;
        $this->info("Should post: " . ($shouldPost ? 'yes' : 'no'));

        return $shouldPost;
    }
} 