<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ChannelGroupController extends Controller
{
    /**
     * Display a listing of the channel groups.
     */
    public function index()
    {
        $groups = Auth::user()->channelGroups()
            ->withCount('channels')
            ->get();
        
        return Inertia::render('ChannelGroups/Index', [
            'groups' => $groups
        ]);
    }

    /**
     * Show the form for creating a new channel group.
     */
    public function create()
    {
        $channels = Auth::user()->channels()->get();
        return Inertia::render('ChannelGroups/Create', [
            'channels' => $channels
        ]);
    }

    /**
     * Store a newly created channel group in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'channels' => 'array',
            'channels.*' => 'exists:channels,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group = new ChannelGroup();
        $group->user_id = Auth::id();
        $group->name = $request->name;
        $group->description = $request->description;
        $group->category = $request->category;
        $group->save();

        // Attach channels if any selected
        if ($request->has('channels') && !empty($request->channels)) {
            $group->channels()->attach($request->channels);
        }

        return redirect()->route('channel-groups.index')
            ->with('success', 'Группа каналов успешно создана');
    }

    /**
     * Display the specified channel group.
     */
    public function show(ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $channelGroup->load('channels');

        // Подготовка статистики для отображения
        $statistics = [
            'channels_count' => $channelGroup->channels->count(),
            'total_members' => $channelGroup->channels->sum('members_count'),
            'total_reach' => 0 // Можно добавить реальный расчет
        ];
        
        return Inertia::render('ChannelGroups/Show', [
            'group' => $channelGroup,
            'channels' => $channelGroup->channels,
            'statistics' => $statistics
        ]);
    }

    /**
     * Show the form for editing the specified channel group.
     */
    public function edit(ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $channels = Auth::user()->channels()->get();
        $selectedChannels = $channelGroup->channels->pluck('id')->toArray();
        
        return Inertia::render('ChannelGroups/Edit', [
            'group' => $channelGroup,
            'channels' => $channels,
            'selectedChannels' => $selectedChannels
        ]);
    }

    /**
     * Update the specified channel group in storage.
     */
    public function update(Request $request, ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'channels' => 'array',
            'channels.*' => 'exists:channels,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $channelGroup->name = $request->name;
        $channelGroup->description = $request->description;
        $channelGroup->category = $request->category;
        $channelGroup->save();

        // Sync channels
        $channelGroup->channels()->sync($request->channels ?? []);

        return redirect()->route('channel-groups.index')
            ->with('success', 'Группа каналов успешно обновлена');
    }

    /**
     * Remove the specified channel group from storage.
     */
    public function destroy(ChannelGroup $channelGroup)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $channelGroup->channels()->detach();
        $channelGroup->delete();

        return redirect()->route('channel-groups.index')
            ->with('success', 'Группа каналов успешно удалена');
    }
    
    /**
     * Generate cross-promotion posts between channels in the group
     */
    public function generateCrossPromotion(ChannelGroup $channelGroup, Request $request)
    {
        if (Auth::id() !== $channelGroup->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Ensure the group has at least 2 channels
        if ($channelGroup->channels->count() < 2) {
            return redirect()->route('channel-groups.show', $channelGroup)
                ->with('error', 'В группе должно быть минимум 2 канала для создания кросс-промо');
        }
        
        try {
        // Get all channels in the group
        $channels = $channelGroup->channels;
            
            // Get GigaChatService
            $gigaChatService = app(\App\Services\GigaChatService::class);
        
        // Track created posts
        $createdPosts = 0;
        
            // If we have N channels, create a circular promotion pattern where
            // channel 1 promotes channel 2, channel 2 promotes channel 3, etc.,
            // and the last channel promotes channel 1
            $channelsCount = $channels->count();
            
            for ($i = 0; $i < $channelsCount; $i++) {
                $currentChannel = $channels[$i];
                $promotedChannel = $channels[($i + 1) % $channelsCount]; // Next channel in circular order
                
                // Skip if they are the same (shouldn't happen with proper rotation)
                if ($currentChannel->id === $promotedChannel->id) {
                    continue;
                }
                
                // Prepare data for GigaChat
                $data = [
                    'topic' => 'Реклама канала ' . $promotedChannel->name,
                    'channel_name' => $currentChannel->name,
                    'channel_description' => $currentChannel->description ?? 'Telegram канал',
                    'additional_info' => "Создай краткий рекламный текст (до 200 символов) о канале '{$promotedChannel->name}'. " .
                                        ($promotedChannel->description ? "Описание канала: {$promotedChannel->description}. " : "") .
                                        "Текст должен призывать подписаться на этот канал и быть привлекательным."
                ];
                
                // System message for consistent promotional content
                $systemMessage = "Ты - специалист по продвижению Telegram-каналов. 
                ВАЖНЫЕ ПРАВИЛА:
                1. Создай ОЧЕНЬ КРАТКИЙ рекламный текст, максимум 200 символов
                2. Текст должен быть привлекательным и мотивировать подписаться
                3. Добавь 1-2 эмодзи для привлечения внимания
                4. Не упоминай себя как AI или языковую модель
                5. Сосредоточься только на рекламе указанного канала
                6. Текст должен быть на русском языке
                7. Не добавляй ссылки - они будут добавлены автоматически";
                
                // Generate content with GigaChat
                try {
                    $promptText = "Создай краткий рекламный текст для канала '{$promotedChannel->name}' " .
                               ($promotedChannel->description ? "с описанием: {$promotedChannel->description}. " : "") .
                               "Текст должен быть не более 200 символов, привлекательным и с эмодзи.";
                    
                    $content = $gigaChatService->generateText($data, $promptText, $systemMessage);
                    
                    // Ensure the content isn't too long (failsafe)
                    if (mb_strlen($content) > 300) {
                        $content = mb_substr($content, 0, 200) . '...';
                    }
                } catch (\Exception $e) {
                    // Fallback if GigaChat fails
                    $content = "🔥 Рекомендуем подписаться на канал {$promotedChannel->name}!\n\n";
                if ($promotedChannel->description) {
                        $content .= "О канале: {$promotedChannel->description}\n\n";
                }
                }
                
                // Create the post
                $post = new Post();
                $post->channel_id = $currentChannel->id;
                $post->user_id = Auth::id();
                $post->title = "Промо: {$promotedChannel->name}";
                $post->content = $content;
                
                // Add telegram link if available
                if ($promotedChannel->telegram_username) {
                    $post->content .= "\n\n👉 @{$promotedChannel->telegram_username}";
                }
                
                $post->status = 'published';
                $post->published_at = now();
                $post->is_cross_promotion = true;
                $post->promoted_from_channel_id = $promotedChannel->id;
                $post->cross_promotion_data = [
                    'promoted_channel' => $promotedChannel->name,
                    'promoted_channel_id' => $promotedChannel->id,
                    'promotion_type' => 'circular',
                    'group_id' => $channelGroup->id,
                    'published_at' => now()->toDateTimeString()
                ];
                $post->save();
                
                // Публикация поста в Telegram канал
                try {
                    // Инициализируем сервис Telegram
                    $telegramService = app(\App\Services\TelegramService::class);
                    
                    // Проверяем, что у канала есть telegram_channel_id
                    if (!$currentChannel->telegram_channel_id && $currentChannel->telegram_username) {
                        // Пробуем получить ID канала
                        $chatInfo = $telegramService->checkBotAccess($currentChannel->telegram_username);
                        
                        if (isset($chatInfo['success']) && $chatInfo['success'] && isset($chatInfo['chat_id'])) {
                            // Обновляем ID канала
                            $currentChannel->update([
                                'telegram_chat_id' => $chatInfo['chat_id'],
                                'telegram_channel_id' => $chatInfo['chat_id']
                            ]);
                        }
                    }
                    
                    // Проверяем наличие ID канала после обновления
                    if (!$currentChannel->telegram_channel_id) {
                        \Illuminate\Support\Facades\Log::warning('Нет ID канала для публикации', [
                            'channel_id' => $currentChannel->id,
                            'channel_name' => $currentChannel->name,
                            'telegram_username' => $currentChannel->telegram_username
                        ]);
                        
                        // Попробуем использовать username, если ID отсутствует
                        if ($currentChannel->telegram_username) {
                            \Illuminate\Support\Facades\Log::info('Попытка отправки по username вместо ID', [
                                'channel_id' => $currentChannel->id,
                                'telegram_username' => $currentChannel->telegram_username
                            ]);
                            
                            $chatId = '@' . ltrim($currentChannel->telegram_username, '@');
                            
                            // Отправка сообщения, используя username
                            $result = $telegramService->sendMessage(
                                $chatId,
                                $post->content
                            );
                            
                            if ($result['success']) {
                                \Illuminate\Support\Facades\Log::info('Успешная отправка по username', [
                                    'channel_id' => $currentChannel->id,
                                    'telegram_username' => $currentChannel->telegram_username
                                ]);
                            } else {
                                \Illuminate\Support\Facades\Log::error('Не удалось отправить сообщение по username', [
                                    'channel_id' => $currentChannel->id,
                                    'error' => $result['error'] ?? 'неизвестная ошибка'
                                ]);
                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else {
                        // Попытка публикации используя правильный метод
                        \Illuminate\Support\Facades\Log::info('Отправка поста в канал', [
                            'channel_id' => $currentChannel->id,
                            'telegram_channel_id' => $currentChannel->telegram_channel_id
                        ]);
                        
                        $result = $telegramService->sendMessage(
                            $currentChannel->telegram_channel_id,
                            $post->content
                        );
                    }
                    
                    if (!$result['success']) {
                        \Illuminate\Support\Facades\Log::warning('Пост создан, но не удалось опубликовать в Telegram', [
                            'post_id' => $post->id,
                            'channel_id' => $currentChannel->id,
                            'error' => $result['error'] ?? 'Unknown error'
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::info('Пост успешно опубликован в Telegram', [
                            'post_id' => $post->id,
                            'channel_id' => $currentChannel->id,
                            'message_id' => $result['message_id'] ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    // Логируем ошибку, но продолжаем работу
                    \Illuminate\Support\Facades\Log::error('Ошибка при публикации в Telegram', [
                        'post_id' => $post->id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                $createdPosts++;
            }
        
        return redirect()->route('channel-groups.show', $channelGroup)
                ->with('success', "Успешно создано и опубликовано $createdPosts кросс-промо постов!");
        } catch (\Exception $e) {
            return redirect()->route('channel-groups.show', $channelGroup)
                ->with('error', 'Ошибка при создании кросс-промо: ' . $e->getMessage());
        }
    }

    public function debug()
    {
        try {
            $groups = Auth::user()->channelGroups()
                ->withCount('channels')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'groups' => $groups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
