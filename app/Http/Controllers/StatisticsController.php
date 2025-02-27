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
            
        return view('statistics.user', compact(
            'totalPosts',
            'publishedPosts',
            'scheduledPosts',
            'activityData',
            'channelsStats',
            'totalSubscribers',
            'totalViews'
        ));
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

        return view('statistics.admin', compact(
            'totalUsers',
            'activeUsers',
            'totalChannels',
            'activeChannels',
            'totalPosts',
            'publishedPosts',
            'scheduledPosts',
            'failedPosts',
            'registrationsData',
            'postsData',
            'totalSubscribers',
            'totalViews',
            'topUsers',
            'topChannels',
            'channelsStats',
            'hourlyStats'
        ));
    }
} 