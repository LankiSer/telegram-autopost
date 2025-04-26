<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Post;
use App\Services\GigaChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GroupAdvertisingController extends Controller
{
    protected $gigaChat;

    public function __construct(GigaChatService $gigaChat)
    {
        $this->gigaChat = $gigaChat;
    }

    /**
     * Показ формы создания рекламной рассылки для группы каналов
     */
    public function create(ChannelGroup $channelGroup)
    {
        // Проверка прав доступа
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Получение списка каналов группы
        $channelGroup->load('channels');

        return Inertia::render('ChannelGroups/AdvertisingCreate', [
            'group' => $channelGroup,
            'channels' => $channelGroup->channels
        ]);
    }

    /**
     * Генерация и рассылка рекламного сообщения по всем каналам группы
     */
    public function store(Request $request, ChannelGroup $channelGroup)
    {
        // Проверка прав доступа
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'original_content' => 'required|string',
            'advertisement_link' => 'required|url',
            'selected_channels' => 'required|array',
            'selected_channels.*' => 'exists:channels,id'
        ]);

        try {
            Log::info('Начало создания рекламной рассылки', [
                'group_id' => $channelGroup->id,
                'user_id' => Auth::id(),
                'selected_channels_count' => count($request->selected_channels)
            ]);

            // Проверка наличия GigaChat сервиса
            if (!$this->gigaChat) {
                Log::error('GigaChat сервис не инициализирован');
                throw new \Exception('Сервис улучшения контента недоступен. Пожалуйста, попробуйте позже.');
            }

            // Улучшение рекламного описания через GigaChat
            try {
                $enhancedContent = $this->enhanceAdvertisementContent(
                    $request->original_content,
                    $request->advertisement_link
                );
                
                Log::info('Реклама успешно улучшена', [
                    'original_length' => strlen($request->original_content),
                    'enhanced_length' => strlen($enhancedContent),
                    'link' => $request->advertisement_link
                ]);
            } catch (\Exception $e) {
                Log::error('Ошибка при улучшении рекламного контента', [
                    'error' => $e->getMessage()
                ]);
                
                // Если улучшение не удалось, используем исходный текст
                $enhancedContent = $request->original_content . "\n\n🔗 " . $request->advertisement_link;
                
                // Не бросаем исключение, а продолжаем работу
                Log::warning('Используется исходный текст вместо улучшенного');
            }

            // Счетчик созданных постов
            $createdPosts = 0;
            $failedChannels = [];

            // Создание рекламных постов для выбранных каналов
            foreach ($request->selected_channels as $channelId) {
                try {
                    // Проверка канала
                    $channel = Channel::find($channelId);
                    
                    // Проверка, что канал существует и принадлежит пользователю
                    if (!$channel || $channel->user_id !== Auth::id()) {
                        $failedChannels[] = ['id' => $channelId, 'reason' => 'Канал не найден или нет доступа'];
                        continue;
                    }

                    // Создание рекламного поста
                    $post = new Post();
                    $post->channel_id = $channel->id;
                    $post->user_id = Auth::id();
                    $post->title = "Реклама: " . substr($request->original_content, 0, 30) . "...";
                    $post->content = $enhancedContent;
                    $post->status = 'published';
                    $post->published_at = now();
                    $post->is_advertisement = true;
                    $post->advertisement_data = [
                        'original_content' => $request->original_content,
                        'link' => $request->advertisement_link,
                        'group_id' => $channelGroup->id,
                        'created_at' => now()->toDateTimeString()
                    ];
                    
                    $post->save();
                    $createdPosts++;
                    
                    // Публикация поста в Telegram канал
                    try {
                        // Инициализируем сервис Telegram
                        $telegramService = app(\App\Services\TelegramService::class);
                        
                        // Проверяем, что у канала есть telegram_channel_id
                        if (!$channel->telegram_channel_id && $channel->telegram_username) {
                            // Пробуем получить ID канала
                            $chatInfo = $telegramService->checkBotAccess($channel->telegram_username);
                            
                            if (isset($chatInfo['success']) && $chatInfo['success'] && isset($chatInfo['chat_id'])) {
                                // Обновляем ID канала
                                $channel->update([
                                    'telegram_chat_id' => $chatInfo['chat_id'],
                                    'telegram_channel_id' => $chatInfo['chat_id']
                                ]);
                            }
                        }
                        
                        // Проверяем наличие ID канала после возможного обновления
                        if (!$channel->telegram_channel_id) {
                            Log::warning('Нет ID канала для публикации', [
                                'channel_id' => $channel->id,
                                'channel_name' => $channel->name,
                                'telegram_username' => $channel->telegram_username
                            ]);
                            
                            // Попробуем использовать username, если ID отсутствует
                            if ($channel->telegram_username) {
                                Log::info('Попытка отправки по username вместо ID', [
                                    'channel_id' => $channel->id,
                                    'telegram_username' => $channel->telegram_username
                                ]);
                                
                                $chatId = '@' . ltrim($channel->telegram_username, '@');
                                
                                // Отправка сообщения, используя username
                                $result = $telegramService->sendMessage(
                                    $chatId,
                                    $post->content
                                );
                                
                                if ($result['success']) {
                                    Log::info('Успешная отправка по username', [
                                        'channel_id' => $channel->id,
                                        'telegram_username' => $channel->telegram_username
                                    ]);
                                } else {
                                    throw new \Exception('Не удалось отправить сообщение по username: ' . ($result['error'] ?? 'неизвестная ошибка'));
                                }
                            } else {
                                throw new \Exception('Не удалось определить ID канала для публикации и нет username');
                            }
                        } else {
                            // Если ID канала есть, используем его для отправки
                            Log::info('Отправка поста в канал', [
                                'channel_id' => $channel->id,
                                'telegram_channel_id' => $channel->telegram_channel_id
                            ]);
                            
                            // Попытка публикации с правильным методом
                            $result = $telegramService->sendMessage(
                                $channel->telegram_channel_id,
                                $post->content
                            );
                        }
                        
                        if (!$result['success']) {
                            Log::warning('Пост создан, но не удалось опубликовать в Telegram', [
                                'post_id' => $post->id,
                                'channel_id' => $channel->id,
                                'error' => $result['error'] ?? 'Unknown error'
                            ]);
                        } else {
                            Log::info('Пост успешно опубликован в Telegram', [
                                'post_id' => $post->id,
                                'channel_id' => $channel->id,
                                'message_id' => $result['message_id'] ?? null
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Логируем ошибку, но продолжаем работу
                        Log::error('Ошибка при публикации в Telegram', [
                            'post_id' => $post->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    Log::info('Создан рекламный пост', [
                        'post_id' => $post->id,
                        'channel_id' => $channel->id,
                        'channel_name' => $channel->name,
                        'status' => 'published'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Ошибка при создании рекламного поста для канала', [
                        'channel_id' => $channelId,
                        'error' => $e->getMessage()
                    ]);
                    
                    $failedChannels[] = [
                        'id' => $channelId, 
                        'reason' => 'Ошибка: ' . $e->getMessage()
                    ];
                }
            }

            Log::info('Завершение создания рекламной рассылки', [
                'group_id' => $channelGroup->id,
                'created_posts' => $createdPosts,
                'failed_channels' => count($failedChannels)
            ]);

            if ($createdPosts > 0) {
                $message = "Успешно создано и опубликовано $createdPosts рекламных постов! ";
                
                if (count($failedChannels) > 0) {
                    $message .= "Не удалось создать посты для " . count($failedChannels) . " каналов.";
                }
                
                return redirect()->route('channel-groups.show', $channelGroup)
                    ->with('success', $message);
            } else {
                throw new \Exception('Не удалось создать ни одного рекламного поста');
            }
        
        } catch (\Exception $e) {
            Log::error('Критическая ошибка при создании рекламной рассылки', [
                'group_id' => $channelGroup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Произошла ошибка при создании рекламной рассылки: ' . $e->getMessage());
        }
    }

    /**
     * Улучшение контента рекламы с помощью GigaChat
     */
    protected function enhanceAdvertisementContent(string $originalContent, string $link): string
    {
        try {
            // Проверка состояния GigaChatService
            if (!$this->gigaChat || !method_exists($this->gigaChat, 'hasCredentials') || !$this->gigaChat->hasCredentials()) {
                Log::warning('GigaChatService не готов к использованию', [
                    'service_initialized' => isset($this->gigaChat),
                    'has_credentials_method' => method_exists($this->gigaChat, 'hasCredentials'),
                    'has_credentials' => $this->gigaChat && method_exists($this->gigaChat, 'hasCredentials') 
                        ? $this->gigaChat->hasCredentials() 
                        : 'unknown'
                ]);
                
                // Если сервис не готов, возвращаем исходный текст с добавленной ссылкой
                return $originalContent . "\n\n🔗 " . $link;
            }
            
            // Промпт для улучшения рекламного текста - более конкретный и директивный
            $prompt = "Перепиши следующий рекламный текст для Telegram канала, чтобы он был более привлекательным и убедительным:

$originalContent";
            
            // Системное сообщение с чёткими инструкциями
            $systemMessage = "Ты - профессиональный копирайтер для Telegram, специализирующийся на создании рекламных сообщений. 

СТРОГО СЛЕДУЙ ЭТИМ ПРАВИЛАМ:
1. НИКОГДА не упоминай себя как ИИ, языковую модель или нейросеть
2. НИКОГДА не пиши от первого лица о своих возможностях или ограничениях
3. Сохрани основную идею и все ключевые факты из исходного текста
4. Добавь 2-3 эмодзи для привлечения внимания
5. Используй **жирный шрифт** для выделения важных моментов
6. Сделай текст более эмоциональным и побуждающим к действию
7. Создай чёткую структуру с коротким вступлением и призывом к действию в конце
8. Пиши на разговорном русском языке, понятном широкой аудитории
9. Соблюдай правила пунктуации и орфографии
10. ВСЕГДА пиши только о теме рекламного объявления

Твоя задача - улучшить маркетинговую привлекательность текста, сохранив его основную идею.";

            // Параметры для запроса
            $data = [
                'topic' => 'Реклама: ' . mb_substr($originalContent, 0, 30) . '...',
                'channel_name' => 'Рекламная рассылка',
                'channel_description' => 'Рекламное сообщение',
                'additional_info' => $originalContent
            ];

            // Сначала проверяем соединение
            if (!$this->gigaChat->testConnection()) {
                Log::warning('Тест соединения с GigaChat не пройден, используем исходный текст');
                return $originalContent . "\n\n🔗 " . $link;
            }

            // Генерация улучшенного рекламного текста
            $enhancedContent = $this->gigaChat->generateText($data, $prompt, $systemMessage);

            // Если полученный текст пустой или слишком короткий, используем исходный
            if (empty($enhancedContent) || mb_strlen($enhancedContent) < 20) {
                Log::warning('Результат GigaChat слишком короткий, используем исходный текст', [
                    'result_length' => mb_strlen($enhancedContent ?? '')
                ]);
                return $originalContent . "\n\n🔗 " . $link;
            }

            // Добавление ссылки в конец сообщения, если её там ещё нет
            if (strpos($enhancedContent, $link) === false) {
                $enhancedContent .= "\n\n🔗 " . $link;
            }

            return $enhancedContent;
        } catch (\Exception $e) {
            Log::error('Ошибка при улучшении рекламного контента', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Если произошла ошибка, возвращаем исходный текст с добавленной ссылкой
            return $originalContent . "\n\n🔗 " . $link;
        }
    }
} 