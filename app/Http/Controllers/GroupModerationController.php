<?php

namespace App\Http\Controllers;

use App\Models\ChannelGroup;
use App\Models\Channel;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\StatisticsCache;

class GroupModerationController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Показать страницу модерации групп
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $groups = ChannelGroup::where('user_id', $user->id)
            ->with(['channels' => function($query) {
                $query->withCount('posts');
            }])
            ->get();

        return Inertia::render('Groups/Moderation', [
            'groups' => $groups,
        ]);
    }

    /**
     * Показать страницу анализа и отчетов по группам
     */
    public function moderationDashboard(Request $request)
    {
        $user = $request->user();
        $forceRefresh = $request->boolean('refresh', false);
        
        // Проверяем, есть ли кэшированные данные
        $cachedData = null;
        if (!$forceRefresh) {
            $cachedData = StatisticsCache::getLatestData($user->id, 'features_analysis');
            
            // Если данные есть, возвращаем их
            if ($cachedData) {
                return Inertia::render('Features/Analysis', $cachedData);
            }
        }
        
        // Получаем статистику по всем группам пользователя
        $channelGroups = ChannelGroup::where('user_id', $user->id)
            ->withCount('channels')
            ->get();
        
        // Получаем каналы, относящиеся к этим группам
        $channelIds = Channel::whereHas('groups', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->pluck('channels.id');
        
        // Подсчет постов по каналам пользователя
        $postsCount = Post::whereIn('channel_id', $channelIds)->count();
        
        // Подсчет подписчиков
        $totalSubscribers = 0; // Since subscribers_count doesn't exist, we'll use 0 or implement a different calculation if needed
        
        // Подсчет активных каналов за последние 30 дней
        $activeChannels = Channel::whereHas('posts', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->whereIn('id', $channelIds)->count();
        
        // Данные для графиков
        $groupsData = [
            'labels' => $channelGroups->pluck('name')->toArray(),
            'data' => []
        ];
        
        foreach ($channelGroups as $group) {
            $groupChannelIds = $group->channels()->pluck('channels.id')->toArray();
            $postCount = Post::whereIn('channel_id', $groupChannelIds)->count();
            $groupsData['data'][] = $postCount;
        }
        
        // Данные по подписчикам за последние 6 месяцев
        $subscribersData = [
            'labels' => [],
            'data' => []
        ];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $subscribersData['labels'][] = $month->format('M Y');
            $subscribersData['data'][] = rand(
                max(1, $totalSubscribers - $totalSubscribers * 0.5), 
                $totalSubscribers
            ); // Имитация роста подписчиков
        }
        
        // Данные по постам за последние 6 месяцев
        $postsData = [
            'labels' => [],
            'data' => []
        ];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $postsData['labels'][] = $month->format('M Y');
            $postsCount = Post::whereIn('channel_id', $channelIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $postsData['data'][] = $postsCount;
        }
        
        // Данные для таблицы групп
        $groups = $channelGroups->map(function($group) use ($channelIds) {
            $groupChannelIds = $group->channels()->pluck('channels.id')->toArray();
            $postsCount = Post::whereIn('channel_id', $groupChannelIds)->count();
            $subscribersCount = 0; // Since subscribers_count doesn't exist, default to 0
            
            // Расчет коэффициента активности 
            $activity = Post::whereIn('channel_id', $groupChannelIds)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            $activityRate = $postsCount > 0 ? min(100, round(($activity / max(1, $postsCount)) * 100)) : 0;
            
            return [
                'id' => $group->id,
                'name' => $group->name,
                'channels_count' => $group->channels_count,
                'subscribers_count' => $subscribersCount,
                'posts_count' => $postsCount,
                'activity_rate' => $activityRate
            ];
        });
        
        $data = [
            'totalSubscribers' => $totalSubscribers,
            'totalPosts' => $postsCount,
            'activeChannels' => $activeChannels,
            'groups' => $groups,
            'groupsData' => $groupsData,
            'subscribersData' => $subscribersData,
            'postsData' => $postsData,
            'last_updated_at' => now()->toIso8601String()
        ];
        
        // Сохраняем данные в кэш
        StatisticsCache::storeData($user->id, 'features_analysis', $data);
        
        return Inertia::render('Features/Analysis', $data);
    }

    /**
     * Показать настройки модерации для группы
     */
    public function settings(Request $request, ChannelGroup $group)
    {
        $this->authorize('update', $group);
        
        $group->load(['channels' => function($query) {
            $query->withCount('posts');
        }]);

        return Inertia::render('Groups/ModerationSettings', [
            'group' => $group,
            'channels' => $group->channels,
        ]);
    }

    /**
     * Обновить настройки модерации для группы
     */
    public function updateSettings(Request $request, ChannelGroup $group)
    {
        $this->authorize('update', $group);
        
        $validated = $request->validate([
            'moderation_enabled' => 'boolean',
            'auto_approve' => 'boolean',
            'moderators' => 'nullable|array',
            'restricted_words' => 'nullable|string',
        ]);

        $group->update([
            'moderation_enabled' => $validated['moderation_enabled'] ?? false,
            'auto_approve' => $validated['auto_approve'] ?? false,
            'moderation_settings' => [
                'moderators' => $validated['moderators'] ?? [],
                'restricted_words' => !empty($validated['restricted_words']) 
                    ? explode(',', preg_replace('/\s+/', '', $validated['restricted_words']))
                    : [],
            ],
        ]);

        return redirect()->route('groups.moderation.settings', $group)
            ->with('success', 'Настройки модерации обновлены успешно');
    }
} 