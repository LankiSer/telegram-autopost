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

    public function index()
    {
        $channels = auth()->user()->channels;
        $posts = Post::whereIn('channel_id', $channels->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('posts.index', compact('posts'));
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'content' => 'required|string',
            'media.*' => 'nullable|file|max:10240',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        // Проверка принадлежности канала пользователю
        $channel = Channel::findOrFail($validated['channel_id']);
        if ($channel->user_id !== auth()->id()) {
            return back()->with('error', 'Вы не можете создавать посты для этого канала');
        }

        // Проверка лимита постов
        $subscription = auth()->user()->activeSubscription();
        $monthStart = now()->startOfMonth();
        $currentPostsCount = Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))
            ->where('created_at', '>=', $monthStart)
            ->count();
            
        if ($currentPostsCount >= $subscription->plan->posts_per_month) {
            return back()->with('error', 'Вы достигли лимита постов для вашего тарифного плана');
        }

        // Обработка медиафайлов
        $mediaFiles = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('media', 'public');
                $mediaFiles[] = $path;
            }
        }

        // Определение статуса поста
        $status = 'draft';
        if ($validated['scheduled_at'] ?? null) {
            if (!auth()->user()->canSchedulePosts()) {
                return back()->with('error', 'Планирование постов недоступно для вашего тарифного плана');
            }
            $status = 'scheduled';
        }

        // Создание поста
        $post = Post::create([
            'channel_id' => $validated['channel_id'],
            'content' => $validated['content'],
            'media' => !empty($mediaFiles) ? $mediaFiles : null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => $status
        ]);

        // Если пост не запланирован, публикуем его сразу
        if ($status === 'draft') {
            try {
                $response = $this->telegram->sendMessage(
                    $channel->telegram_channel_id,
                    $post->content,
                    $post->media
                );

                if ($response->successful()) {
                    $post->update([
                        'status' => 'published',
                        'published_at' => now()
                    ]);
                    
                    // Обновляем время последнего поста в канале
                    $channel->update(['last_post_at' => now()]);
                    
                    return redirect()->route('posts.index')
                        ->with('success', 'Пост успешно опубликован');
                } else {
                    $post->update([
                        'status' => 'failed',
                        'error_message' => $response->json()['description'] ?? 'Неизвестная ошибка'
                    ]);
                    return redirect()->route('posts.index')
                        ->with('error', 'Ошибка публикации: ' . ($response->json()['description'] ?? 'Неизвестная ошибка'));
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
            ->with('success', 'Пост успешно запланирован на ' . $post->scheduled_at->format('d.m.Y H:i'));
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

        try {
            $response = $this->telegram->sendMessage(
                $post->channel->telegram_channel_id,
                $post->content,
                $post->media
            );

            if ($response->successful()) {
                $post->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'scheduled_at' => null
                ]);
                
                // Обновляем время последнего поста в канале
                $post->channel->update(['last_post_at' => now()]);
                
                return back()->with('success', 'Пост успешно опубликован');
            } else {
                $post->update([
                    'status' => 'failed',
                    'error_message' => $response->json()['description'] ?? 'Неизвестная ошибка'
                ]);
                return back()->with('error', 'Ошибка публикации: ' . ($response->json()['description'] ?? 'Неизвестная ошибка'));
            }
        } catch (\Exception $e) {
            $post->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            return back()->with('error', 'Ошибка публикации: ' . $e->getMessage());
        }
    }
} 