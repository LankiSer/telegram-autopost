<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Post;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $channels = Channel::where('user_id', auth()->id())->get();
        
        $postsQuery = Post::query()
            ->whereIn('channel_id', $channels->pluck('id'));
        
        // Фильтр по каналу
        if ($request->has('channel') && $request->channel) {
            $postsQuery->where('channel_id', $request->channel);
        }
        
        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $postsQuery->where('status', $request->status);
        }
        
        $posts = $postsQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('posts.index', compact('posts', 'channels'));
    }

    /**
     * Display posts for a specific channel.
     */
    public function channelPosts(Channel $channel)
    {
        // Проверка принадлежности канала текущему пользователю
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        $posts = Post::where('channel_id', $channel->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('posts.channel', compact('posts', 'channel'));
    }

    public function create()
    {
        $channels = auth()->user()->channels;
        
        // Проверяем, достиг ли пользователь лимита постов
        $subscription = auth()->user()->activeSubscription();
        if (!$subscription) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Вам необходимо оформить подписку для создания постов');
        }
        
        return view('posts.create', compact('channels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'content' => 'required|string',
            'media.*' => 'nullable|file|max:10240', // Восстанавливаем валидацию для медиа-файлов
            'scheduled_at' => 'nullable|date|after:now',
            'status' => 'required|in:draft,publish_now,schedule'
        ]);

        // Получаем канал
        $channel = Channel::findOrFail($validated['channel_id']);
        if ($channel->user_id !== auth()->id()) {
            return back()->with('error', 'Вы не можете создавать посты для этого канала');
        }

        // Проверка лимита постов
        $subscription = auth()->user()->activeSubscription();
        if (!$subscription) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Вам необходимо оформить подписку для создания постов');
        }
        
        $monthStart = now()->startOfMonth();
        $currentPostsCount = Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))
            ->where('created_at', '>=', $monthStart)
            ->count();
        
        if ($currentPostsCount >= $subscription->plan->posts_per_month) {
            return back()->with('error', 'Вы достигли лимита постов для вашего тарифного плана');
        }

        // Определяем статус поста и время публикации
        $status = 'draft';
        $scheduledAt = null;

        if ($validated['status'] === 'schedule' && isset($validated['scheduled_at'])) {
            if (!auth()->user()->canSchedulePosts()) {
                return back()->with('error', 'Планирование постов недоступно для вашего тарифного плана');
            }
            $status = 'scheduled';
            $scheduledAt = $validated['scheduled_at'];
        }

        // Создаем пост
        $post = new Post([
            'channel_id' => $channel->id,
            'content' => $validated['content'],
            'scheduled_at' => $scheduledAt,
            'status' => $status
        ]);
        $post->save();

        // Загрузка медиа-файлов
        $this->handleMediaUpload($request, $post);

        // Если выбрана немедленная публикация
        if ($validated['status'] === 'publish_now') {
            try {
                // Отправляем сообщение в Telegram
                $response = $this->telegram->sendMessage(
                    $channel->telegram_channel_id,
                    $post->content,
                    $post->media
                );

                // Обновленная обработка ответа (массив вместо объекта)
                if (isset($response['success']) && $response['success']) {
                    $post->update([
                        'status' => 'published',
                        'published_at' => now()
                    ]);
                    
                    // Обновляем время последнего поста в канале
                    $channel->update(['last_post_at' => now()]);
                    
                    return redirect()->route('posts.index')
                        ->with('success', 'Пост успешно опубликован');
                } else {
                    $errorMessage = $response['error'] ?? 'Неизвестная ошибка';
                    if (isset($response['data']['description'])) {
                        $errorMessage = $response['data']['description'];
                    }
                    
                    $post->update([
                        'status' => 'failed',
                        'error_message' => $errorMessage
                    ]);
                    
                    return redirect()->route('posts.index')
                        ->with('error', 'Ошибка публикации: ' . $errorMessage);
                }
            } catch (\Exception $e) {
                $post->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                
                return redirect()->route('posts.index')
                    ->with('error', 'Ошибка публикации: ' . $e->getMessage());
            }
        }

        return redirect()->route('posts.index')
            ->with('success', $status === 'scheduled' 
                ? 'Пост успешно запланирован на ' . (new \DateTime($scheduledAt))->format('d.m.Y H:i') 
                : 'Пост сохранен как черновик');
    }

    public function show(Post $post)
    {
        // Проверка доступа
        if ($post->channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        // Проверка доступа
        if ($post->channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Проверяем, можно ли редактировать пост
        if (!in_array($post->status, ['draft', 'scheduled'])) {
            return redirect()->route('posts.index')
                ->with('error', 'Нельзя редактировать опубликованные или неудачные посты');
        }
        
        $channels = auth()->user()->channels;
        return view('posts.edit', compact('post', 'channels'));
    }

    public function update(Request $request, Post $post)
    {
        // Проверка доступа
        if ($post->channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'content' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        // Обновляем только черновики или запланированные посты
        if (!in_array($post->status, ['draft', 'scheduled'])) {
            return back()->with('error', 'Нельзя редактировать опубликованные или неудачные посты');
        }

        // Определение статуса поста
        $status = 'draft';
        if ($validated['scheduled_at'] ?? null) {
            if (!auth()->user()->canSchedulePosts()) {
                return back()->with('error', 'Планирование постов недоступно для вашего тарифного плана');
            }
            $status = 'scheduled';
        }

        $post->update([
            'content' => $validated['content'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => $status
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Пост успешно обновлен');
    }

    public function destroy(Post $post)
    {
        // Проверка доступа
        if ($post->channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Удаляем медиафайлы поста, если они есть
        if ($post->media) {
            foreach ($post->media as $mediaPath) {
                Storage::disk('public')->delete($mediaPath);
            }
        }
        
        $post->delete();
        
        return redirect()->route('posts.index')
            ->with('success', 'Пост успешно удален');
    }

    /**
     * Опубликовать пост в Telegram канале
     */
    public function publish(Post $post)
    {
        // Проверка доступа
        if ($post->channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Проверяем, что пост не опубликован
        if ($post->status === 'published') {
            return back()->with('error', 'Пост уже опубликован');
        }

        // Проверяем, что у канала есть telegram_channel_id
        if (!$post->channel->telegram_channel_id) {
            // Пробуем получить ID канала из сервиса Telegram
            try {
                $telegramService = app(TelegramService::class);
                $chatInfo = $telegramService->checkBotAccess($post->channel->telegram_username);
                
                if (isset($chatInfo['success']) && $chatInfo['success'] && isset($chatInfo['chat_id'])) {
                    // Обновляем ID канала
                    $post->channel->update([
                        'telegram_chat_id' => $chatInfo['chat_id'],
                        'telegram_channel_id' => $chatInfo['chat_id']
                    ]);
                    
                    \Illuminate\Support\Facades\Log::info('Updated missing channel ID', [
                        'channel_id' => $post->channel->id,
                        'telegram_username' => $post->channel->telegram_username,
                        'telegram_channel_id' => $chatInfo['chat_id']
                    ]);
                } else {
                    $post->update([
                        'status' => 'failed',
                        'error_message' => 'Не удалось получить ID канала. Пожалуйста, переподключите бота к каналу.'
                    ]);
                    
                    return back()->with('error', 'Ошибка: ID канала не найден. Пожалуйста, переподключите бота к каналу через настройки канала.');
                }
            } catch (\Exception $e) {
                $post->update([
                    'status' => 'failed',
                    'error_message' => 'Ошибка при получении ID канала: ' . $e->getMessage()
                ]);
                
                return back()->with('error', 'Ошибка: Не удалось получить ID канала. ' . $e->getMessage());
            }
        }

        try {
            // Повторная проверка после возможного обновления
            if (!$post->channel->telegram_channel_id) {
                throw new \Exception('ID канала не установлен. Пожалуйста, переподключите бота к каналу.');
            }
            
            $response = $this->telegram->sendMessage(
                $post->channel->telegram_channel_id,
                $post->content,
                $post->media
            );

            // Обрабатываем новый формат ответа (массив вместо объекта)
            if (isset($response['success']) && $response['success']) {
                $post->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'scheduled_at' => null
                ]);
                
                // Обновляем время последнего поста в канале
                $post->channel->update(['last_post_at' => now()]);
                
                // Если была предупреждение, но сообщение все равно отправлено
                if (isset($response['warning'])) {
                    return back()->with('success', 'Пост опубликован, но с предупреждением')
                        ->with('warning', $response['warning']);
                }
                
                return back()->with('success', 'Пост успешно опубликован');
            } else {
                $errorMessage = $response['error'] ?? 'Неизвестная ошибка';
                if (isset($response['data']['description'])) {
                    $errorMessage = $response['data']['description'];
                }
                
                $post->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage
                ]);
                
                return back()->with('error', 'Ошибка публикации: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            $post->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            \Illuminate\Support\Facades\Log::error('Exception in post publishing', [
                'message' => $e->getMessage(),
                'post_id' => $post->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Ошибка публикации: ' . $e->getMessage());
        }
    }

    /**
     * Обработка загрузки медиа-файлов для поста
     */
    protected function handleMediaUpload(Request $request, $post)
    {
        if ($request->hasFile('media')) {
            $mediaPaths = [];
            foreach ($request->file('media') as $mediaFile) {
                // Сохраняем в публичное хранилище для доступа через web
                $path = $mediaFile->store('media', 'public');
                // Медиа-пути хранятся в формате с префиксом диска
                $mediaPaths[] = $path;  // Храним путь без префикса 'public/'
                
                // Логируем информацию о файле
                \Illuminate\Support\Facades\Log::info('Media file uploaded', [
                    'original_name' => $mediaFile->getClientOriginalName(),
                    'stored_path' => $path,
                    'full_path' => Storage::disk('public')->path($path),
                    'exists' => Storage::disk('public')->exists($path)
                ]);
            }
            
            // Обновляем медиа-файлы поста
            $post->update(['media' => $mediaPaths]);
        }
    }
} 