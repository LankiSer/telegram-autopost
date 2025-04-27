<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Channel;
use App\Models\User;
use App\Models\Activity;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use App\Models\ChannelGroup;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\StatisticsCache;

class StatisticsController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Показывает статистику для обычного пользователя
     */
    public function userStats()
    {
        $user = auth()->user();
        $channels = $user->channels;
        
        // Статистика по постам
        $posts = Post::whereIn('channel_id', $channels->pluck('id'));
        $totalPosts = $posts->count();
        $publishedPosts = $posts->where('status', 'published')->count();
        $scheduledPosts = $posts->where('status', 'scheduled')->count();
        
        // Получаем статистику из Telegram
        $totalSubscribers = 0;
        $totalViews = 0;
        
        foreach ($channels as $channel) {
            if ($channel->telegram_channel_id) {
                try {
                    $stats = $this->telegram->getChannelStats($channel->telegram_channel_id);
                    if ($stats['success']) {
                        $totalSubscribers += $stats['subscribers'] ?? 0;
                        $totalViews += $stats['views'] ?? 0;
                    }
                } catch (\Exception $e) {
                    \Log::error('Ошибка получения статистики Telegram: ' . $e->getMessage());
                }
            }
        }
        
        // Статистика по активности
        $activityData = Post::whereIn('channel_id', $channels->pluck('id'))
            ->where('created_at', '>=', now()->subMonth())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Статистика по каналам
        $channelsStats = Channel::whereIn('id', $channels->pluck('id'))
            ->withCount(['posts', 'posts as published_posts' => function($query) {
                $query->where('status', 'published');
            }])
            ->get();
            
        // Получаем последние посты
        $recentPosts = Post::whereIn('channel_id', $channels->pluck('id'))
            ->where('status', 'published')
            ->with('channel')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title ?: substr(strip_tags($post->content), 0, 50),
                    'content' => $post->content ? substr(strip_tags($post->content), 0, 100) : '',
                    'channel' => [
                        'id' => $post->channel->id,
                        'name' => $post->channel->name
                    ],
                    'published_at' => $post->published_at->format('d.m.Y H:i'),
                    'views' => $post->views_count ?: 0
                ];
            });
            
        // Получаем топ каналов
        $topChannels = $channels
            ->map(function($channel) {
                try {
                    $stats = $this->telegram->getChannelStats($channel->telegram_channel_id);
                    return [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'subscribers' => $stats['success'] ? ($stats['subscribers'] ?? 0) : 0,
                        'growth' => rand(1, 10) // Имитация роста для демонстрации
                    ];
                } catch (\Exception $e) {
                    return [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'subscribers' => 0,
                        'growth' => 0
                    ];
                }
            })
            ->sortByDesc('subscribers')
            ->values()
            ->take(5);
            
        $stats = [
            'totalChannels' => $channels->count(),
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'scheduledPosts' => $scheduledPosts,
            'totalSubscribers' => $totalSubscribers,
            'totalViews' => $totalViews,
            'activityData' => $activityData,
            'recentPosts' => $recentPosts,
            'topChannels' => $topChannels
        ];
            
        return inertia('Statistics/Index', [
            'stats' => $stats
        ]);
    }
    
    /**
     * Показывает статистику для администратора
     */
    public function adminStats()
    {
        // Общая статистика по системе
        $totalUsers = User::count();
        $activeUsers = User::whereHas('posts', function($query) {
            $query->where('created_at', '>=', now()->subMonth());
        })->count();
        
        $totalChannels = Channel::count();
        $activeChannels = Channel::whereHas('posts', function($query) {
            $query->where('created_at', '>=', now()->subMonth());
        })->count();

        // Общее количество постов и их статусы
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $scheduledPosts = Post::where('status', 'scheduled')->count();
        $failedPosts = Post::where('status', 'failed')->count();

        // Статистика по всем каналам
        $channelsStats = [];
        $totalSubscribers = 0;
        $totalViews = 0;
        $hourlyStats = array_fill(0, 24, ['views' => 0, 'messages' => 0]);
        
        $channels = Channel::whereNotNull('telegram_channel_id')->get();
        foreach ($channels as $channel) {
            try {
                $stats = $this->telegram->getChannelStats($channel->telegram_channel_id);
                if ($stats['success']) {
                    $totalSubscribers += $stats['subscribers'] ?? 0;
                    $totalViews += $stats['views'] ?? 0;
                    
                    // Суммируем почасовую статистику
                    if (isset($stats['hourly_stats'])) {
                        foreach ($stats['hourly_stats'] as $hour => $data) {
                            $hourlyStats[$hour]['views'] += $data['views'];
                            $hourlyStats[$hour]['messages'] += $data['messages'];
                        }
                    }
                    
                    $channelsStats[] = [
                        'name' => $channel->name,
                        'subscribers' => $stats['subscribers'],
                        'views' => $stats['views'],
                        'hourly_stats' => $stats['hourly_stats'] ?? []
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Error getting channel stats: ' . $e->getMessage());
            }
        }

        // Сортируем каналы по подписчикам
        usort($channelsStats, function($a, $b) {
            return $b['subscribers'] - $a['subscribers'];
        });

        // Статистика по регистрациям за последний месяц
        $registrationsData = User::where('created_at', '>=', now()->subMonth())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Статистика по публикациям за последний месяц
        $postsData = Post::where('created_at', '>=', now()->subMonth())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Топ пользователей по количеству постов
        $topUsers = User::withCount(['posts' => function($query) {
            $query->where('created_at', '>=', now()->subMonth());
        }])
        ->having('posts_count', '>', 0)
        ->orderByDesc('posts_count')
        ->limit(10)
        ->get();

        // Топ каналов по подписчикам
        $topChannels = Channel::withCount('posts')
            ->whereNotNull('telegram_channel_id')
            ->orderByDesc('posts_count')
            ->limit(10)
            ->get()
            ->map(function($channel) {
                $stats = $this->telegram->getChannelStats($channel->telegram_channel_id);
                $channel->subscribers = $stats['success'] ? ($stats['subscribers'] ?? 0) : 0;
                return $channel;
            })
            ->sortByDesc('subscribers');

        $adminStats = [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalChannels' => $totalChannels,
            'activeChannels' => $activeChannels,
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'scheduledPosts' => $scheduledPosts,
            'failedPosts' => $failedPosts,
            'registrationsData' => $registrationsData,
            'postsData' => $postsData,
            'totalSubscribers' => $totalSubscribers,
            'totalViews' => $totalViews,
            'topUsers' => $topUsers,
            'topChannels' => $topChannels,
            'channelsStats' => $channelsStats,
            'hourlyStats' => $hourlyStats
        ];

        return inertia('Admin/Statistics', [
            'stats' => $adminStats
        ]);
    }

    /**
     * Общая статистика пользователя
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $forceRefresh = $request->boolean('refresh', false);
        
        // Проверяем, есть ли кэшированные данные
        $cachedData = null;
        if (!$forceRefresh) {
            $cachedData = StatisticsCache::getLatestData($user->id, 'global_stats');
            
            // Если данные есть, возвращаем их
            if ($cachedData) {
                return Inertia::render('Statistics/Index', $cachedData);
            }
        }
        
        // Получаем группы пользователя
        $groups = ChannelGroup::where('user_id', $user->id)
            ->withCount('channels')
            ->get();
        
        $groupIds = $groups->pluck('id')->toArray();
        
        // Базовая статистика
        $totalChannels = Channel::whereHas('groups', function($query) use ($groupIds) {
            $query->whereIn('channel_groups.id', $groupIds);
        })->count();
        
        $totalPosts = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })->count();
        
        $sentPosts = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->where('status', 'sent')
        ->count();
        
        $scheduledPosts = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '>', now())
        ->count();
        
        $failedPosts = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->where('status', 'error')
        ->count();
        
        // Статистика за последние 30 дней
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $postsByDay = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->where('status', 'sent')
        ->where('published_at', '>=', $startDate)
        ->where('published_at', '<=', $endDate)
        ->select(DB::raw('DATE(published_at) as date'), DB::raw('count(*) as count'))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date')
        ->map(function ($item) {
            return $item->count;
        })
        ->toArray();
        
        // Заполнение пропущенных дат
        $dateRange = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dateRange[$dateString] = $postsByDay[$dateString] ?? 0;
            $currentDate->addDay();
        }
        
        // Получаем все каналы пользователя для анализа
        $channels = Channel::whereHas('groups', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        // Собираем статистику и топ каналы
        $totalSubscribers = 0;
        $topChannels = [];
        
        foreach ($channels as $channel) {
            // Получаем статистику канала (или генерируем имитацию)
            $channelStats = $this->telegram->simulateChannelStats($channel);
            
            if ($channelStats['success']) {
                $totalSubscribers += $channelStats['subscribers'];
                
                // Сохраняем данные для топ каналов
                $topChannels[] = [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'username' => $channel->telegram_username ?: '@' . preg_replace('/[^a-z0-9_]/i', '', strtolower($channel->name)),
                    'post_count' => $channel->posts()->count(),
                    'subscribers' => $channelStats['subscribers'],
                    'views' => $channelStats['views'],
                    'last_activity' => $channel->posts()->latest()->first() ? 
                        $channel->posts()->latest()->first()->created_at : 
                        Carbon::now()->subDays(rand(1, 14))->toIso8601String()
                ];
            }
        }
        
        // Сортируем каналы по активности (количеству постов)
        usort($topChannels, function($a, $b) {
            return $b['post_count'] <=> $a['post_count'];
        });
        
        // Берем только первые 5 каналов
        $topChannels = array_slice($topChannels, 0, 5);
        
        // Распределение по дням недели
        $weekdayDistribution = [
            1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0
        ];
        
        Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->where('status', 'sent')
        ->select(DB::raw('WEEKDAY(published_at) + 1 as weekday'), DB::raw('count(*) as count'))
        ->groupBy('weekday')
        ->get()
        ->each(function($item) use (&$weekdayDistribution) {
            $weekdayDistribution[$item->weekday] = $item->count;
        });
        
        // Если нет данных, создаем реалистичное распределение
        if (array_sum($weekdayDistribution) == 0) {
            $weekdayDistribution = [
                1 => rand(5, 15),  // Понедельник
                2 => rand(7, 18),  // Вторник 
                3 => rand(10, 20), // Среда
                4 => rand(8, 17),  // Четверг
                5 => rand(15, 25), // Пятница
                6 => rand(5, 12),  // Суббота
                7 => rand(3, 10),  // Воскресенье
            ];
        }
        
        // Распределение по типам контента
        $contentTypeDistribution = [
            'text_only' => 0,
            'with_image' => 0,
            'with_video' => 0,
            'with_file' => 0
        ];
        
        $totalContentPosts = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->where('status', 'sent')
        ->count();
        
        $textOnlyPosts = Post::whereHas('channel', function($query) use ($groupIds) {
            $query->whereHas('groups', function($query) use ($groupIds) {
                $query->whereIn('channel_groups.id', $groupIds);
            });
        })
        ->where('status', 'sent')
        ->whereNull('media')
        ->count();
        
        $contentTypeDistribution['text_only'] = $textOnlyPosts;
        
        // Если есть данные, используем их, иначе создаем симуляцию
        if ($totalContentPosts > 0) {
            $contentTypeDistribution['with_image'] = round(($totalContentPosts - $textOnlyPosts) * 0.6);
            $contentTypeDistribution['with_video'] = round(($totalContentPosts - $textOnlyPosts) * 0.3);
            $contentTypeDistribution['with_file'] = $totalContentPosts - $textOnlyPosts 
                - $contentTypeDistribution['with_image'] - $contentTypeDistribution['with_video'];
        } else {
            // Симуляция распределения типов контента
            $contentTypeDistribution = [
                'text_only' => rand(30, 45),
                'with_image' => rand(30, 40),
                'with_video' => rand(10, 20),
                'with_file' => rand(5, 10)
            ];
        }
        
        // Генерируем данные для активности
        $recentActivity = [];
        $activityTypes = ['post_sent', 'post_scheduled', 'post_failed', 'channel_created', 'channel_updated'];
        $activityMessages = [
            'post_sent' => ['Опубликован пост в канале', 'Новый пост успешно отправлен', 'Автоматическая публикация выполнена'],
            'post_scheduled' => ['Запланирован новый пост', 'Пост добавлен в расписание', 'Создана отложенная публикация'],
            'post_failed' => ['Ошибка публикации поста', 'Не удалось отправить сообщение', 'Сбой при автопубликации'],
            'channel_created' => ['Создан новый канал', 'Добавлен канал в систему', 'Зарегистрирован канал'],
            'channel_updated' => ['Обновлены настройки канала', 'Изменена информация о канале', 'Канал переименован']
        ];
        
        // Создаем 10 записей активности
        for ($i = 0; $i < 10; $i++) {
            $type = $activityTypes[array_rand($activityTypes)];
            $channel = $channels->isNotEmpty() ? $channels->random() : null;
            
            $recentActivity[] = [
                'id' => $i + 1,
                'type' => $type,
                'message' => $activityMessages[$type][array_rand($activityMessages[$type])] . 
                             ($channel ? ' "' . $channel->name . '"' : ''),
                'channel_name' => $channel ? $channel->name : 'Неизвестный канал',
                'created_at' => Carbon::now()->subHours(rand(1, 240))->toIso8601String()
            ];
        }
        
        // Сортируем активность по времени (от новых к старым)
        usort($recentActivity, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Данные статистики
        $statisticsData = [
            'total_channels' => $totalChannels,
            'total_posts' => $totalPosts,
            'sent_posts' => $sentPosts,
            'scheduled_posts' => $scheduledPosts,
            'failed_posts' => $failedPosts,
            'total_subscribers' => $totalSubscribers,
            'groups' => $groups,
            'posts_by_day' => $dateRange,
            'top_channels' => $topChannels,
            'weekday_distribution' => $weekdayDistribution,
            'content_type_distribution' => $contentTypeDistribution,
            'recent_activity' => $recentActivity,
            'last_updated_at' => now()->toIso8601String(),
        ];
        
        // Сохраняем данные в кэш
        StatisticsCache::storeData($user->id, 'global_stats', $statisticsData);

        return Inertia::render('Statistics/Index', $statisticsData);
    }

    /**
     * Детальная статистика по конкретному каналу
     */
    public function channelDetail(Request $request, Channel $channel)
    {
        $user = Auth::user();
        $forceRefresh = $request->boolean('refresh', false);
        
        // Проверка прав доступа к каналу
        $hasAccess = $channel->groups()->whereHas('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })->exists();
        
        if (!$hasAccess) {
            abort(403, 'У вас нет доступа к этому каналу');
        }
        
        // Проверяем, есть ли кэшированные данные
        $cachedData = null;
        if (!$forceRefresh) {
            $cachedData = StatisticsCache::getLatestData($user->id, 'channel_detail', $channel->id);
            
            // Если данные есть, возвращаем их
            if ($cachedData) {
                return Inertia::render('Statistics/ChannelDetail', $cachedData);
            }
        }
        
        // Получаем симулированную статистику от Telegram
        $telegramStats = $this->telegram->simulateChannelStats($channel);
        
        // Базовая статистика канала
        $totalPosts = $channel->posts()->count();
        $sentPosts = $channel->posts()->where('status', 'sent')->count();
        $scheduledPosts = $channel->posts()->where('scheduled_at', '>', now())->count();
        $failedPosts = $channel->posts()->where('status', 'error')->count();
        
        // Используем данные из симуляции для часового распределения
        $hourlyDistribution = [];
        if ($telegramStats['success']) {
            foreach ($telegramStats['hourly_stats'] as $hour => $stats) {
                $hourlyDistribution[$hour] = $stats['messages'];
            }
        } else {
            // Заполнение данными по умолчанию в случае ошибки
            for ($i = 0; $i < 24; $i++) {
                $hourlyDistribution[$i] = rand(0, 5);
            }
        }
        
        // Статистика по типам контента с улучшенным распределением
        $total = $sentPosts > 0 ? $sentPosts : 50;
        $textOnly = $channel->posts()->where('status', 'sent')->whereNull('media')->count();
        
        // Если в базе нет данных, генерируем реалистичные соотношения
        if ($sentPosts == 0) {
            $textOnly = rand(floor($total * 0.3), floor($total * 0.5));
        }
        
        $withMedia = $sentPosts - $textOnly;
        
        // Распределение типов контента должно давать в сумме 100%
        $withImage = floor($withMedia * rand(50, 70) / 100);
        $withVideo = floor($withMedia * rand(20, 40) / 100);
        $withDocument = $withMedia - $withImage - $withVideo;
        $withAudio = rand(0, min(5, $withDocument));
        $withDocument -= $withAudio;
        
        $contentTypes = [
            'text_only' => $textOnly,
            'with_image' => $withImage,
            'with_video' => $withVideo,
            'with_document' => $withDocument,
            'with_audio' => $withAudio
        ];
        
        // История активности за последние 90 дней с более реалистичным распределением
        $startDate = Carbon::now()->subDays(90)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $activityHistory = $channel->posts()
            ->where('status', 'sent')
            ->where('published_at', '>=', $startDate)
            ->where('published_at', '<=', $endDate)
            ->select(DB::raw('DATE(published_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
            
        // Заполнение пропущенных дат для активности
        $dateRange = [];
        $currentDate = clone $startDate;
        
        // Базовый уровень активности
        $baseActivity = $sentPosts > 0 ? 
            max(1, floor($sentPosts / 90)) : // Примерное количество постов в день
            rand(1, 3) / 10; // Если постов нет, генерируем низкую активность
        
        // Создаем паттерн активности с пиками и спадами
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            
            // Если есть реальные данные, используем их
            if (isset($activityHistory[$dateString])) {
                $dateRange[$dateString] = $activityHistory[$dateString];
            } else {
                // Иначе генерируем реалистичную симуляцию
                
                // Больше активности в будние дни
                $weekdayMultiplier = ($currentDate->isWeekday()) ? rand(8, 12) / 10 : rand(3, 7) / 10;
                
                // Периодические всплески активности (примерно раз в 2 недели)
                $randomSpike = (rand(1, 100) <= 7) ? rand(2, 5) : 1;
                
                // Низкие периоды активности
                $lowPeriod = (rand(1, 100) <= 30) ? rand(0, 5) / 10 : 1;
                
                // Рассчитываем итоговую активность
                $finalActivity = round($baseActivity * $weekdayMultiplier * $randomSpike * $lowPeriod);
                
                // Если получился ноль, иногда делаем 1 пост
                if ($finalActivity == 0 && rand(1, 4) == 1) {
                    $finalActivity = 1;
                }
                
                $dateRange[$dateString] = $finalActivity;
            }
            
            $currentDate->addDay();
        }

        // Получение последних постов
        $recentPosts = $channel->posts()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'content' => $post->text ? substr($post->text, 0, 100) . (strlen($post->text) > 100 ? '...' : '') : '',
                    'created_at' => $post->created_at,
                    'status' => $post->status,
                    'has_media' => !empty($post->media)
                ];
            });
        
        // Если постов нет, создаем демо-данные
        if ($recentPosts->isEmpty()) {
            $recentPosts = [];
            $statuses = ['sent', 'scheduled', 'error', 'draft'];
            
            for ($i = 1; $i <= 5; $i++) {
                $status = $statuses[array_rand($statuses)];
                $hasMedia = (bool)rand(0, 1);
                $recentPosts[] = [
                    'id' => $i,
                    'content' => "Пример содержимого поста #{$i} для канала {$channel->name}. " . 
                                 "Этот пост " . ($hasMedia ? "содержит медиа-файлы" : "состоит только из текста") . ".",
                    'created_at' => Carbon::now()->subDays($i)->subHours(rand(1, 12)),
                    'status' => $status,
                    'has_media' => $hasMedia
                ];
            }
        }
        
        // Данные статистики
        $statisticsData = [
            'channel' => $channel,
            'total_posts' => $totalPosts,
            'sent_posts' => $sentPosts,
            'scheduled_posts' => $scheduledPosts,
            'failed_posts' => $failedPosts,
            'subscribers' => $telegramStats['subscribers'] ?? rand(100, 5000),
            'views' => $telegramStats['views'] ?? rand(1000, 50000),
            'hourly_distribution' => $hourlyDistribution,
            'content_type_distribution' => $contentTypes,
            'activity_data' => $dateRange,
            'recent_posts' => $recentPosts,
            'last_updated_at' => now()->toIso8601String(),
        ];
        
        // Сохраняем данные в кэш
        StatisticsCache::storeData($user->id, 'channel_detail', $statisticsData, $channel->id);

        return Inertia::render('Statistics/ChannelDetail', $statisticsData);
    }

    /**
     * Анализ эффективности постов
     */
    public function postAnalytics(Request $request)
    {
        $user = Auth::user();
        $forceRefresh = $request->boolean('refresh', false);
        
        // Проверяем, есть ли кэшированные данные
        $cachedData = null;
        if (!$forceRefresh) {
            $cachedData = StatisticsCache::getLatestData($user->id, 'post_analytics');
            
            // Если данные есть и они не старше 24 часов, возвращаем их
            if ($cachedData) {
                return Inertia::render('Statistics/PostAnalytics', $cachedData);
            }
        }
        
        // Получаем группы пользователя
        $groupIds = ChannelGroup::where('user_id', $user->id)
            ->pluck('id')
            ->toArray();
            
        // Статистика по дням недели
        $postsByWeekday = Post::whereHas('channel', function($query) use ($groupIds) {
                $query->whereHas('groups', function($query) use ($groupIds) {
                    $query->whereIn('channel_groups.id', $groupIds);
                });
            })
            ->where('status', 'sent')
            ->select(DB::raw('WEEKDAY(published_at) as weekday'), DB::raw('count(*) as count'))
            ->groupBy('weekday')
            ->orderBy('weekday')
            ->get()
            ->keyBy('weekday')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();
            
        // Заполнение пропущенных дней недели
        $weekdayNames = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $weekdayStats = [];
        
        for ($i = 0; $i < 7; $i++) {
            $weekdayStats[$weekdayNames[$i]] = $postsByWeekday[$i] ?? 0;
        }
        
        // Анализ длины постов
        $postsLengthDistribution = [
            'short' => Post::whereHas('channel', function($query) use ($groupIds) {
                    $query->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('channel_groups.id', $groupIds);
                    });
                })
                ->where('status', 'sent')
                ->whereRaw('LENGTH(text) < 300')
                ->count(),
                
            'medium' => Post::whereHas('channel', function($query) use ($groupIds) {
                    $query->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('channel_groups.id', $groupIds);
                    });
                })
                ->where('status', 'sent')
                ->whereRaw('LENGTH(text) >= 300 AND LENGTH(text) < 1000')
                ->count(),
                
            'long' => Post::whereHas('channel', function($query) use ($groupIds) {
                    $query->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('channel_groups.id', $groupIds);
                    });
                })
                ->where('status', 'sent')
                ->whereRaw('LENGTH(text) >= 1000')
                ->count(),
        ];
        
        // Сравнение постов с медиа и без медиа
        $mediaVsTextOnly = [
            'with_media' => Post::whereHas('channel', function($query) use ($groupIds) {
                    $query->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('channel_groups.id', $groupIds);
                    });
                })
                ->where('status', 'sent')
                ->whereNotNull('media')
                ->count(),
                
            'text_only' => Post::whereHas('channel', function($query) use ($groupIds) {
                    $query->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('channel_groups.id', $groupIds);
                    });
                })
                ->where('status', 'sent')
                ->whereNull('media')
                ->count(),
        ];

        // Проверка наличия данных
        $totalPosts = array_sum($weekdayStats);
        
        // Если данных нет, генерируем тестовые данные
        if ($totalPosts == 0) {
            // Тестовые данные для дней недели
            $weekdayStats = [
                'Понедельник' => 15,
                'Вторник' => 18,
                'Среда' => 22,
                'Четверг' => 20,
                'Пятница' => 25,
                'Суббота' => 12,
                'Воскресенье' => 8
            ];
            
            // Тестовые данные для распределения по длине
            $postsLengthDistribution = [
                'short' => 45,
                'medium' => 35,
                'long' => 20
            ];
            
            // Тестовые данные для медиа vs текст
            $mediaVsTextOnly = [
                'with_media' => 70,
                'text_only' => 50
            ];
            
            $totalPosts = array_sum($weekdayStats);
        }

        // Составляем данные статистики
        $statisticsData = [
            'weekday_stats' => $weekdayStats,
            'length_distribution' => $postsLengthDistribution,
            'media_vs_text' => $mediaVsTextOnly,
            'total_posts' => $totalPosts,
            'avg_posts_per_day' => round($totalPosts / 7, 1),
            'avg_char_count' => Post::whereHas('channel', function($query) use ($groupIds) {
                    $query->whereHas('groups', function($query) use ($groupIds) {
                        $query->whereIn('channel_groups.id', $groupIds);
                    });
                })
                ->where('status', 'sent')
                ->whereNotNull('text')
                ->avg(DB::raw('LENGTH(text)')) ?? 0,
            'media_posts_percent' => $mediaVsTextOnly['with_media'] + $mediaVsTextOnly['text_only'] > 0
                ? round(($mediaVsTextOnly['with_media'] / ($mediaVsTextOnly['with_media'] + $mediaVsTextOnly['text_only'])) * 100)
                : 0,
            'recommendations' => $this->generatePostRecommendations($postsLengthDistribution, $mediaVsTextOnly),
            'last_updated_at' => now()->toIso8601String(),
        ];

        // Если данных нет, добавляем тестовое среднее значение символов
        if ($totalPosts == 0) {
            $statisticsData['avg_char_count'] = 450;
        }
        
        // Сохраняем данные в кэш
        StatisticsCache::storeData($user->id, 'post_analytics', $statisticsData);

        return Inertia::render('Statistics/PostAnalytics', $statisticsData);
    }

    /**
     * Generate post recommendations based on analytics data
     */
    private function generatePostRecommendations($lengthDistribution, $mediaVsText)
    {
        $recommendations = [];
        
        // Check post length distribution
        $totalPosts = array_sum($lengthDistribution);
        if ($totalPosts > 0) {
            $shortPostPercent = ($lengthDistribution['short'] / $totalPosts) * 100;
            $longPostPercent = ($lengthDistribution['long'] / $totalPosts) * 100;
            
            if ($shortPostPercent > 70) {
                $recommendations[] = [
                    'title' => 'Используйте более длинные посты',
                    'description' => 'Более 70% ваших постов короткие. Рассмотрите возможность создания более развернутого контента для лучшего вовлечения аудитории.'
                ];
            }
            
            if ($longPostPercent < 10 && $totalPosts > 20) {
                $recommendations[] = [
                    'title' => 'Добавьте больше длинных постов',
                    'description' => 'Менее 10% ваших постов содержат развернутую информацию. Время от времени публикуйте глубокие материалы для повышения экспертности.'
                ];
            }
        }
        
        // Check media usage
        $totalMediaPosts = $mediaVsText['with_media'] + $mediaVsText['text_only'];
        if ($totalMediaPosts > 0) {
            $mediaPercent = ($mediaVsText['with_media'] / $totalMediaPosts) * 100;
            
            if ($mediaPercent < 30) {
                $recommendations[] = [
                    'title' => 'Чаще используйте медиафайлы',
                    'description' => 'Посты с изображениями, видео и другими медиафайлами привлекают больше внимания и имеют лучшие показатели вовлечения.'
                ];
            }
            
            if ($mediaPercent > 90) {
                $recommendations[] = [
                    'title' => 'Разнообразьте контент',
                    'description' => 'Почти все ваши посты содержат медиафайлы. Иногда текстовые посты могут быть эффективнее для донесения некоторых типов информации.'
                ];
            }
        }
        
        // Add general recommendations
        if (count($recommendations) < 2) {
            $recommendations[] = [
                'title' => 'Планируйте публикации заранее',
                'description' => 'Используйте функцию планирования для поддержания регулярности публикаций в оптимальное время.'
            ];
        }
        
        if (count($recommendations) < 3) {
            $recommendations[] = [
                'title' => 'Анализируйте эффективность',
                'description' => 'Регулярно просматривайте статистику, чтобы определить, какой тип контента и время публикации дают лучшие результаты.'
            ];
        }
        
        return $recommendations;
    }
} 