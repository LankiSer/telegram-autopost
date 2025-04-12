<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Channel;
use App\Models\ChannelGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class SchedulerController extends Controller
{
    /**
     * Показывает страницу планировщика
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        // Получаем каналы пользователя
        $channels = Channel::where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'telegram_channel_id', 'telegram_username'])
            ->map(function($channel) {
                return [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'telegram_channel_id' => $channel->telegram_channel_id,
                    'username' => $channel->telegram_username,
                ];
            });
            
        // Получаем запланированные посты на выбранную дату
        $scheduledPosts = Post::where(function($query) use ($user) {
                $query->whereIn('channel_id', function($subquery) use ($user) {
                    $subquery->select('id')
                        ->from('channels')
                        ->where('user_id', $user->id);
                });
            })
            ->where('status', 'scheduled')
            ->whereDate('scheduled_at', $date)
            ->with('channel')
            ->orderBy('scheduled_at')
            ->get()
            ->map(function($post) {
                try {
                    $content = $post->content ? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content)) : '';
                    $title = $content ? substr($content, 0, 50) . (strlen($content) > 50 ? '...' : '') : 'Без текста';
                    
                    return [
                        'id' => $post->id,
                        'title' => $title,
                        'channel' => [
                            'id' => $post->channel->id,
                            'name' => $post->channel->name
                        ],
                        'time' => Carbon::parse($post->scheduled_at)->format('H:i'),
                        'hasMedia' => !empty($post->media),
                        'status' => $post->status
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error processing scheduled post', [
                        'post_id' => $post->id, 
                        'error' => $e->getMessage()
                    ]);
                    
                    return [
                        'id' => $post->id,
                        'title' => 'Ошибка отображения поста',
                        'channel' => [
                            'id' => $post->channel->id,
                            'name' => $post->channel->name
                        ],
                        'time' => Carbon::parse($post->scheduled_at)->format('H:i'),
                        'hasMedia' => false,
                        'status' => $post->status
                    ];
                }
            });
            
        // Получаем опубликованные посты на выбранную дату
        $publishedPosts = Post::where(function($query) use ($user) {
                $query->whereIn('channel_id', function($subquery) use ($user) {
                    $subquery->select('id')
                        ->from('channels')
                        ->where('user_id', $user->id);
                });
            })
            ->where('status', 'published')
            ->whereDate('published_at', $date)
            ->with('channel')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function($post) {
                try {
                    $content = $post->content ? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content)) : '';
                    $title = $content ? substr($content, 0, 50) . (strlen($content) > 50 ? '...' : '') : 'Без текста';
                    
                    return [
                        'id' => $post->id,
                        'title' => $title,
                        'channel' => [
                            'id' => $post->channel->id,
                            'name' => $post->channel->name
                        ],
                        'time' => Carbon::parse($post->published_at)->format('H:i'),
                        'hasMedia' => !empty($post->media),
                        'status' => $post->status,
                        'views' => $post->views_count
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error processing published post', [
                        'post_id' => $post->id, 
                        'error' => $e->getMessage()
                    ]);
                    
                    return [
                        'id' => $post->id,
                        'title' => 'Ошибка отображения поста',
                        'channel' => [
                            'id' => $post->channel->id,
                            'name' => $post->channel->name
                        ],
                        'time' => Carbon::parse($post->published_at)->format('H:i'),
                        'hasMedia' => false,
                        'status' => $post->status,
                        'views' => $post->views_count ?? 0
                    ];
                }
            });
            
        // Получаем черновики
        $draftPosts = Post::where(function($query) use ($user) {
                $query->whereIn('channel_id', function($subquery) use ($user) {
                    $subquery->select('id')
                        ->from('channels')
                        ->where('user_id', $user->id);
                });
            })
            ->where('status', 'draft')
            ->with('channel')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($post) {
                try {
                    $content = $post->content ? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags($post->content)) : '';
                    $title = $content ? substr($content, 0, 50) . (strlen($content) > 50 ? '...' : '') : 'Без текста';
                    
                    return [
                        'id' => $post->id,
                        'title' => $title,
                        'channel' => [
                            'id' => $post->channel->id,
                            'name' => $post->channel->name
                        ],
                        'updated_at' => Carbon::parse($post->updated_at)->format('d.m.Y H:i'),
                        'hasMedia' => !empty($post->media),
                        'status' => $post->status
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error processing draft post', [
                        'post_id' => $post->id, 
                        'error' => $e->getMessage()
                    ]);
                    
                    return [
                        'id' => $post->id,
                        'title' => 'Ошибка отображения поста',
                        'channel' => [
                            'id' => $post->channel->id,
                            'name' => $post->channel->name
                        ],
                        'updated_at' => Carbon::parse($post->updated_at)->format('d.m.Y H:i'),
                        'hasMedia' => false,
                        'status' => $post->status
                    ];
                }
            });
        
        // Получаем статистику по постам за месяц
        try {
            $monthlyStats = Post::where(function($query) use ($user) {
                    $query->whereIn('channel_id', function($subquery) use ($user) {
                        $subquery->select('id')
                            ->from('channels')
                            ->where('user_id', $user->id);
                    });
                })
                ->where('status', '!=', 'draft')
                ->whereDate('created_at', '>=', Carbon::now()->subMonth())
                ->select(DB::raw('DATE(scheduled_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->date => $item->count];
                });
        } catch (\Exception $e) {
            \Log::error('Error fetching monthly stats', [
                'error' => $e->getMessage()
            ]);
            $monthlyStats = [];
        }
            
        return Inertia::render('Scheduler/Index', [
            'channels' => $channels,
            'scheduledPosts' => $scheduledPosts,
            'publishedPosts' => $publishedPosts,
            'draftPosts' => $draftPosts,
            'selectedDate' => $date->format('Y-m-d'),
            'monthlyStats' => $monthlyStats,
        ]);
    }
    
    /**
     * Создание нового поста через планировщик
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
            'content' => 'required|string',
            'scheduled_at' => 'required|date',
            'media' => 'nullable|array',
        ]);
        
        // Проверяем, что канал принадлежит пользователю
        $channel = Channel::where('id', $validated['channel_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // Создаём новый пост
        $post = new Post();
        $post->channel_id = $validated['channel_id'];
        $post->content = $validated['content'];
        $post->scheduled_at = Carbon::parse($validated['scheduled_at']);
        $post->status = 'scheduled';
        $post->save();
        
        // Добавляем медиафайлы, если они есть
        if (isset($validated['media']) && !empty($validated['media'])) {
            foreach ($validated['media'] as $media) {
                $post->addMedia($media)->toMediaCollection('images');
            }
        }
        
        return redirect()->route('scheduler.index')->with('success', 'Пост успешно запланирован');
    }
    
    /**
     * Обновление запланированного поста
     */
    public function update(Request $request, Post $post)
    {
        $user = Auth::user();
        
        // Проверяем, что пост принадлежит пользователю
        if ($post->channel->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'content' => 'required|string',
            'scheduled_at' => 'required|date',
            'media' => 'nullable|array',
        ]);
        
        $post->content = $validated['content'];
        $post->scheduled_at = Carbon::parse($validated['scheduled_at']);
        $post->save();
        
        // Обновляем медиафайлы, если нужно
        if ($request->has('clear_media') && $request->clear_media) {
            $post->clearMediaCollection('images');
        }
        
        if (isset($validated['media']) && !empty($validated['media'])) {
            foreach ($validated['media'] as $media) {
                $post->addMedia($media)->toMediaCollection('images');
            }
        }
        
        return redirect()->route('scheduler.index')->with('success', 'Пост успешно обновлен');
    }
    
    /**
     * Отмена запланированного поста
     */
    public function cancel(Post $post)
    {
        $user = Auth::user();
        
        // Проверяем, что пост принадлежит пользователю
        if ($post->channel->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Меняем статус на черновик
        $post->status = 'draft';
        $post->save();
        
        return redirect()->route('scheduler.index')->with('success', 'Публикация поста отменена');
    }
    
    /**
     * Немедленная публикация поста
     */
    public function publishNow(Post $post)
    {
        $user = Auth::user();
        
        // Проверяем, что пост принадлежит пользователю
        if ($post->channel->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Отправляем пост на публикацию прямо сейчас
        $post->status = 'processing';
        $post->scheduled_at = Carbon::now();
        $post->save();
        
        // Здесь должна быть логика отправки поста в Telegram через очередь
        // Это просто заглушка для демонстрации
        $post->status = 'published';
        $post->published_at = Carbon::now();
        $post->save();
        
        return redirect()->route('scheduler.index')->with('success', 'Пост опубликован успешно');
    }
} 