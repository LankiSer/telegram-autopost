<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PostsProcessCommand extends Command
{
    protected $signature = 'posts:process';
    protected $description = 'Process scheduled posts';

    public function handle(TelegramService $telegram)
    {
        $now = Carbon::now('Europe/Moscow');
        $this->info('Начало обработки запланированных постов в ' . $now->format('Y-m-d H:i:s'));
        
        // Логируем информацию в файл
        \Log::channel('scheduler')->info('Начало обработки запланированных постов', [
            'time' => $now->toDateTimeString(),
            'timezone' => config('app.timezone')
        ]);

        try {
            // Получаем посты, которые нужно опубликовать
            $posts = Post::where('status', 'scheduled')
                ->where('scheduled_at', '<=', $now)
                ->get();

            $this->info("Найдено {$posts->count()} постов для публикации");
            \Log::channel('scheduler')->info("Найдено постов для публикации", [
                'count' => $posts->count()
            ]);

            foreach ($posts as $post) {
                try {
                    $this->info("Публикация поста #{$post->id} в канал {$post->channel->name}");
                    
                    // Логируем попытку отправки
                    \Log::channel('scheduler')->info('Попытка отправки поста', [
                        'post_id' => $post->id,
                        'channel_id' => $post->channel_id,
                        'channel_name' => $post->channel->name ?? 'unknown',
                        'scheduled_at' => $post->scheduled_at
                    ]);
                    
                    // Пытаемся отправить сообщение
                    $response = $telegram->sendMessage(
                        $post->channel->telegram_channel_id,
                        $post->content,
                        $post->media
                    );

                    if ($response['success']) {
                        // Обновляем статус поста на "опубликован"
                        $post->update([
                            'status' => 'published',
                            'published_at' => now()
                        ]);
                        
                        $this->info("Успешно опубликован пост #{$post->id}");
                        \Log::channel('scheduler')->info('Пост успешно опубликован', [
                            'post_id' => $post->id,
                            'message_id' => $response['message_id'] ?? null
                        ]);
                    } else {
                        // Обновляем статус поста на "ошибка"
                        $post->update([
                            'status' => 'failed',
                            'error_message' => $response['error'] ?? 'Неизвестная ошибка'
                        ]);
                        
                        $this->error("Ошибка публикации поста #{$post->id}: " . ($response['error'] ?? 'Неизвестная ошибка'));
                        \Log::channel('scheduler')->error('Ошибка публикации поста', [
                            'post_id' => $post->id,
                            'error' => $response['error'] ?? 'Неизвестная ошибка'
                        ]);
                    }
                } catch (\Exception $e) {
                    // Обрабатываем исключение для конкретного поста
                    $post->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                    
                    $this->error("Исключение при публикации поста #{$post->id}: " . $e->getMessage());
                    \Log::channel('scheduler')->error('Исключение при публикации поста', [
                        'post_id' => $post->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $this->info('Обработка запланированных постов завершена');
            \Log::channel('scheduler')->info('Обработка запланированных постов завершена');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Общая ошибка при обработке постов: ' . $e->getMessage());
            \Log::channel('scheduler')->error('Общая ошибка при обработке постов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
} 