<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Post;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Inertia\Inertia;

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
        try {
            $channels = Channel::where('user_id', auth()->id())->get();
            
            $postsQuery = Post::query()
                ->whereIn('channel_id', $channels->pluck('id'))
                ->with('channel'); // Подгружаем информацию о каналах
            
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
            
            // Добавляем заголовки для постов на основе контента
            $posts->through(function($post) {
                if (!isset($post->title) || empty($post->title)) {
                    $sanitizedContent = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content ?? ''));
                    $post->title = !empty($sanitizedContent) 
                        ? substr($sanitizedContent, 0, 50) . (strlen($sanitizedContent) > 50 ? '...' : '') 
                        : 'Без названия';
                }
                return $post;
            });
            
            return \Inertia\Inertia::render('Posts/Index', [
                'posts' => $posts,
                'channels' => $channels,
                'filters' => $request->only(['channel', 'status'])
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading posts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return \Inertia\Inertia::render('Posts/Index', [
                'posts' => [],
                'channels' => [],
                'filters' => $request->only(['channel', 'status']),
                'error' => 'Произошла ошибка при загрузке постов. Пожалуйста, попробуйте позже.'
            ]);
        }
    }

    /**
     * Display posts for a specific channel.
     */
    public function channelPosts(Channel $channel)
    {
        try {
            // Проверка принадлежности канала текущему пользователю
            if ($channel->user_id !== auth()->id()) {
                abort(403);
            }
            
            $posts = Post::where('channel_id', $channel->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            // Добавляем заголовки для постов на основе контента
            $posts->through(function($post) {
                if (!isset($post->title) || empty($post->title)) {
                    $sanitizedContent = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content ?? ''));
                    $post->title = !empty($sanitizedContent) 
                        ? substr($sanitizedContent, 0, 50) . (strlen($sanitizedContent) > 50 ? '...' : '') 
                        : 'Без названия';
                }
                return $post;
            });
            
            return Inertia::render('Posts/Channel', [
                'posts' => $posts,
                'channel' => $channel
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading channel posts', [
                'channel_id' => $channel->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('channels.index')
                ->with('error', 'Произошла ошибка при загрузке постов канала.');
        }
    }

    public function create()
    {
        $channels = auth()->user()->channels;
        return Inertia::render('Posts/Create', [
            'channels' => $channels
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'content' => 'required|string',
            'publish_type' => 'required|in:now,scheduled',
            'scheduled_at' => 'required_if:publish_type,scheduled|nullable|date|after:now'
        ]);

        // Проверяем, принадлежит ли канал пользователю
        $channel = auth()->user()->channels()->findOrFail($validated['channel_id']);

        // Определяем статус поста
        $status = $validated['publish_type'] === 'now' ? 'pending' : 'scheduled';

        // Проверяем возможность планирования
        if ($status === 'scheduled' && !auth()->user()->canSchedulePosts()) {
            return back()
                ->withInput()
                ->with('error', 'Планирование постов недоступно для вашего тарифного плана');
        }

        $post = $channel->posts()->create([
            'content' => $validated['content'],
            'status' => $status,
            'scheduled_at' => $validated['publish_type'] === 'scheduled' ? $validated['scheduled_at'] : now(),
        ]);

        return redirect()
            ->route('posts.index')
            ->with('success', $validated['publish_type'] === 'now' 
                ? 'Пост добавлен в очередь на публикацию' 
                : 'Пост запланирован на ' . Carbon::parse($validated['scheduled_at'])->format('d.m.Y H:i'));
    }

    public function show(Post $post)
    {
        try {
            // Проверка доступа
            if ($post->channel->user_id !== auth()->id()) {
                abort(403);
            }
            
            // Sanitize content if needed
            if ($post->content) {
                $post->content = preg_replace('/[\x00-\x1F]/', '', $post->content);
            }
            
            // Generate title if missing
            if (!isset($post->title) || empty($post->title)) {
                $sanitizedContent = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content ?? ''));
                $post->title = !empty($sanitizedContent) 
                    ? substr($sanitizedContent, 0, 50) . (strlen($sanitizedContent) > 50 ? '...' : '') 
                    : 'Без названия';
            }
            
            return \Inertia\Inertia::render('Posts/Show', [
                'post' => $post->load('channel')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing post', [
                'post_id' => $post->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('posts.index')
                ->with('error', 'Произошла ошибка при просмотре поста.');
        }
    }

    public function edit(Post $post)
    {
        try {
            \Log::info('Attempting to edit post', [
                'post_id' => $post->id,
                'status' => $post->status,
                'channel_user_id' => $post->channel->user_id,
                'current_user_id' => auth()->id()
            ]);

            // Проверка доступа
            if ($post->channel->user_id !== auth()->id()) {
                \Log::warning('Unauthorized access attempt', [
                    'post_id' => $post->id,
                    'user_id' => auth()->id()
                ]);
                abort(403);
            }

            // Разрешаем редактировать также посты со статусом pending
            if (!in_array($post->status, ['draft', 'scheduled', 'failed', 'pending'])) {
                \Log::info('Post status not editable', [
                    'post_id' => $post->id,
                    'status' => $post->status
                ]);
                return redirect()
                    ->route('posts.index')
                    ->with('error', 'Этот пост нельзя редактировать');
            }
            
            // Sanitize content if needed
            if ($post->content) {
                $post->content = preg_replace('/[\x00-\x1F]/', '', $post->content);
            }
            
            // Generate title if missing
            if (!isset($post->title) || empty($post->title)) {
                $sanitizedContent = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content ?? ''));
                $post->title = !empty($sanitizedContent) 
                    ? substr($sanitizedContent, 0, 50) . (strlen($sanitizedContent) > 50 ? '...' : '') 
                    : 'Без названия';
            }

            $channels = auth()->user()->channels;
            return Inertia::render('Posts/Edit', [
                'post' => $post,
                'channels' => $channels
            ]);

        } catch (\Exception $e) {
            \Log::error('Error editing post', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('posts.index')
                ->with('error', 'Произошла ошибка при открытии поста для редактирования');
        }
    }

    public function update(Request $request, Post $post)
    {
        try {
            // Проверка доступа
            if ($post->channel->user_id !== auth()->id()) {
                abort(403);
            }
            
            $validated = $request->validate([
                'content' => 'required|string',
                'scheduled_at' => 'nullable|date|after:now'
            ]);

            // Разрешаем редактировать также посты со статусом pending
            if (!in_array($post->status, ['draft', 'scheduled', 'pending'])) {
                return back()->with('error', 'Нельзя редактировать опубликованные или неудачные посты');
            }

            // Сохраняем текущий статус, если он pending
            $status = $post->status === 'pending' ? 'pending' : 'draft';
            
            // Если указана дата публикации
            if ($validated['scheduled_at'] ?? null) {
                if (!auth()->user()->canSchedulePosts()) {
                    return back()->with('error', 'Планирование постов недоступно для вашего тарифного плана');
                }
                $status = 'scheduled';
            }

            $post->update([
                'content' => $validated['content'],
                'status' => $status,
                'scheduled_at' => $validated['scheduled_at'] ?? null
            ]);

            return redirect()
                ->route('posts.show', $post)
                ->with('success', 'Пост успешно обновлен');
        } catch (\Exception $e) {
            \Log::error('Error updating post', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Произошла ошибка при обновлении поста');
        }
    }

    public function destroy(Post $post)
    {
        try {
            // Проверка доступа
            if ($post->channel->user_id !== auth()->id()) {
                abort(403);
            }
            
            // Удаляем медиафайлы поста, если они есть
            if (!empty($post->media) && is_array($post->media)) {
                foreach ($post->media as $mediaPath) {
                    try {
                        if ($mediaPath) {
                            Storage::disk('public')->delete($mediaPath);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete media file', [
                            'post_id' => $post->id,
                            'mediaPath' => $mediaPath,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            $post->delete();
            
            return redirect()->route('posts.index')
                ->with('success', 'Пост успешно удален');
        } catch (\Exception $e) {
            \Log::error('Error deleting post', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('posts.index')
                ->with('error', 'Произошла ошибка при удалении поста: ' . $e->getMessage());
        }
    }

    /**
     * Опубликовать пост в Telegram канале
     */
    public function publish(Post $post)
    {
        try {
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

            // Повторная проверка после возможного обновления
            if (!$post->channel->telegram_channel_id) {
                throw new \Exception('ID канала не установлен. Пожалуйста, переподключите бота к каналу.');
            }
            
            // Sanitize content before sending
            $content = $post->content;
            if ($content) {
                $content = preg_replace('/[\x00-\x1F]/', '', $content);
            }
            
            $media = [];
            if (!empty($post->media) && is_array($post->media)) {
                $media = $post->media;
            }
            
            $response = $this->telegram->sendMessage(
                $post->channel->telegram_channel_id,
                $content,
                $media
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
            \Log::error('Exception in post publishing', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $post->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Произошла ошибка при публикации поста: ' . $e->getMessage());
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

    public function debug(Request $request)
    {
        try {
            $channels = Channel::where('user_id', auth()->id())->get();
            
            $postsQuery = Post::query()
                ->whereIn('channel_id', $channels->pluck('id'))
                ->with('channel')
                ->limit(10);
            
            $posts = $postsQuery->get();
            
            return response()->json([
                'status' => 'success',
                'posts' => $posts,
                'channels' => $channels
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