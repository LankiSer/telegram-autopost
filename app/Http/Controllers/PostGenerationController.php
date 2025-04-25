<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Services\GigaChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostGenerationController extends Controller
{
    protected $gigaChat;

    public function __construct(GigaChatService $gigaChat)
    {
        $this->gigaChat = $gigaChat;
    }

    /**
     * Генерация текста поста по промпту канала
     */
    public function generate(Request $request)
    {
        try {
            Log::info('Post generation started', [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);
            
            $validated = $request->validate([
                'channel_id' => 'required|exists:channels,id',
                'title' => 'nullable|string',
                'custom_prompt' => 'nullable|string',
            ]);

            $channel = Channel::findOrFail($validated['channel_id']);
            
            // Проверяем доступ к каналу
            if ($channel->user_id !== auth()->id()) {
                Log::warning('Unauthorized channel access attempt', [
                    'user_id' => auth()->id(),
                    'channel_id' => $channel->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому каналу'
                ], 403);
            }

            // 1. Базовый промпт канала (основная тематика)
            $channelPrompt = $channel->content_prompt ?? $channel->name;
            $basePrompt = "Создай информативный и увлекательный пост для Telegram канала о {$channelPrompt}. ";
            
            // 2. Уточняющий промпт из заголовка (конкретное блюдо или тема)
            $titlePrompt = '';
            if (!empty($validated['title'])) {
                // Очищаем заголовок от дат и форматирования для выделения сути
                $cleanTitle = preg_replace('/\d{2}\.\d{2}\.\d{4}|\—|\-|__|_/', '', $validated['title']);
                $cleanTitle = trim($cleanTitle);
                
                if (!empty($cleanTitle)) {
                    // Для кулинарных каналов добавляем специфику блюда
                    if (stripos($channelPrompt, 'кухня') !== false || 
                        stripos($channelPrompt, 'рецепт') !== false || 
                        stripos($channelPrompt, 'еда') !== false) {
                        
                        $titlePrompt = "Конкретное блюдо: {$cleanTitle}. Включи историю блюда, ингредиенты и пошаговое приготовление. ";
                    } else {
                        $titlePrompt = "Тема поста: {$cleanTitle}. ";
                    }
                }
            }
            
            // 3. Пользовательский промпт (если задан)
            $customPrompt = '';
            if (!empty($validated['custom_prompt'])) {
                $customPrompt = "Дополнительные указания: {$validated['custom_prompt']} ";
            }
            
            // Получаем предыдущие темы постов для разнообразия контента
            $previousTopics = $channel->posts()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->pluck('title')
                ->toArray();
                
            // Добавляем информацию о предыдущих постах для разнообразия
            $previousPostsPrompt = '';
            if (!empty($previousTopics)) {
                $previousPostsPrompt = "Недавние темы постов, которых следует избегать: " . implode(", ", $previousTopics) . ". ";
            }
            
            // Объединяем в единый промпт с приоритетами
            $combinedPrompt = $basePrompt . $titlePrompt . $customPrompt . $previousPostsPrompt;
            
            // Добавляем инструкции по форматированию
            $formattingInstructions = "
            Пожалуйста, соблюдай следующие правила форматирования:
            1. Текст должен быть компактным, не более 5-7 абзацев
            2. Используй markdown для форматирования: *курсив*, **жирный шрифт**, __подчеркнутый__
            3. Добавь эмодзи для увеличения читаемости и выразительности
            4. Разбей текст на логические части с подзаголовками
            5. Добавь 2-3 хэштега в конце поста, связанные с тематикой {$channelPrompt}
            6. Общий объем текста - не более 1500 символов
            ";
            
            $fullPrompt = $combinedPrompt . "\n" . $formattingInstructions;
            
            // Логируем составленный промпт для отладки
            Log::info('Generating post with prompt', [
                'channel_name' => $channel->name,
                'channel_prompt' => $channelPrompt,
                'title' => $validated['title'] ?? 'не указан',
                'custom_prompt' => $validated['custom_prompt'] ?? 'не указан',
                'full_prompt' => $fullPrompt
            ]);
            
            // Проверка состояния GigaChatService
            Log::info('GigaChatService state before generation', [
                'service_initialized' => isset($this->gigaChat),
                'has_credentials' => $this->gigaChat && method_exists($this->gigaChat, 'hasCredentials') 
                    ? $this->gigaChat->hasCredentials() 
                    : 'method not exists'
            ]);
            
            // Проверяем, включен ли тестовый режим
            $testMode = env('GIGACHAT_TEST_MODE', false);
            if ($testMode) {
                Log::info('Используется тестовый режим GigaChat');
            }
            
            // Генерируем текст поста (используем заглушку, если нет учетных данных)
            $generatedText = $this->gigaChat->generatePost([
                'topic' => $validated['title'] ?? $channelPrompt,
                'channel_name' => $channel->name,
                'channel_description' => $channel->description ?? $channelPrompt,
                'additional_info' => $fullPrompt
            ]);
            
            // Если текст был сгенерирован с помощью заглушки, добавляем информацию в лог
            if ($this->gigaChat && method_exists($this->gigaChat, 'hasCredentials') && !$this->gigaChat->hasCredentials()) {
                Log::info('Использована заглушка вместо GigaChat API из-за отсутствия учетных данных');
            }
            
            // Логируем результат генерации
            Log::info('Generation result', [
                'success' => !empty($generatedText),
                'text_length' => $generatedText ? strlen($generatedText) : 0,
                'text_sample' => $generatedText ? substr($generatedText, 0, 100) . '...' : 'empty'
            ]);
            
            if (!$generatedText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось сгенерировать текст поста. Проверьте настройки GigaChat API или введите текст вручную.'
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'content' => $generatedText,
                'channel_id' => $channel->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка генерации поста', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при генерации поста: ' . $e->getMessage()
            ], 500);
        }
    }
} 