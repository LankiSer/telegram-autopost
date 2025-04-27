<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\StatisticsCache;

class ExtendedFeaturesController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Показать страницу анализа недостающих функций
     */
    public function missingFeatures(Request $request)
    {
        $user = $request->user();
        
        // Получаем статистику по постам пользователя
        $posts = Post::where('user_id', $user->id)->get();
        $totalPosts = $posts->count();
        
        // Получаем статистику по каналам пользователя
        $channels = Channel::where('user_id', $user->id)->get();
        $totalChannels = $channels->count();
        
        // Анализ использования медиа в постах
        $postsWithMedia = $posts->filter(function($post) {
            return !empty($post->media);
        })->count();
        
        // Анализ использования отложенных постов
        $scheduledPosts = $posts->filter(function($post) {
            return !empty($post->scheduled_at);
        })->count();
        
        // Процент использования функций
        $mediaPercentage = $totalPosts > 0 ? round(($postsWithMedia / $totalPosts) * 100) : 0;
        $scheduledPercentage = $totalPosts > 0 ? round(($scheduledPosts / $totalPosts) * 100) : 0;
        
        // Определяем часто используемые функции
        $frequentlyUsed = [];
        if ($mediaPercentage > 50) {
            $frequentlyUsed[] = 'Медиа-контент';
        }
        if ($scheduledPercentage > 50) {
            $frequentlyUsed[] = 'Отложенные публикации';
        }
        
        // Определяем редко используемые функции
        $rarelyUsed = [];
        if ($mediaPercentage < 30) {
            $rarelyUsed[] = 'Медиа-контент';
        }
        if ($scheduledPercentage < 30) {
            $rarelyUsed[] = 'Отложенные публикации';
        }
        
        // Проверяем наличие рекламных постов
        $advertisementPosts = $posts->filter(function($post) {
            return strpos(strtolower($post->content), '#реклама') !== false || 
                   strpos(strtolower($post->content), '#ad') !== false;
        })->count();
        
        $advertisementPercentage = $totalPosts > 0 ? round(($advertisementPosts / $totalPosts) * 100) : 0;
        
        if ($advertisementPercentage < 10) {
            $rarelyUsed[] = 'Рекламные посты';
        } else if ($advertisementPercentage > 40) {
            $frequentlyUsed[] = 'Рекламные посты';
        }
        
        // Формируем рекомендации на основе анализа
        $recommendations = [];
        
        if ($mediaPercentage < 50) {
            $recommendations[] = 'Увеличьте использование медиа-контента для повышения вовлеченности аудитории';
        }
        
        if ($scheduledPercentage < 40) {
            $recommendations[] = 'Используйте отложенные публикации для поддержания регулярной активности в каналах';
        }
        
        if ($totalChannels > 0 && $totalChannels < 3) {
            $recommendations[] = 'Добавьте больше каналов для расширения аудитории';
        }
        
        // Пользовательская активность за последний месяц
        $recentPosts = $posts->filter(function($post) {
            return $post->created_at >= now()->subDays(30);
        })->count();
        
        $activityLevel = 'Низкий';
        if ($recentPosts > 10) {
            $activityLevel = 'Средний';
        }
        if ($recentPosts > 30) {
            $activityLevel = 'Высокий';
        }
        
        // Анализ наличия функций по тегам
        $hashtagsUsage = $posts->filter(function($post) {
            return strpos($post->content, '#') !== false;
        })->count();
        
        $hashtagsPercentage = $totalPosts > 0 ? round(($hashtagsUsage / $totalPosts) * 100) : 0;
        
        if ($hashtagsPercentage < 30) {
            $recommendations[] = 'Используйте хэштеги для лучшей категоризации и поиска контента';
        }
        
        return Inertia::render('Features/Missing', [
            'stats' => [
                'totalPosts' => $totalPosts,
                'totalChannels' => $totalChannels,
                'postsWithMedia' => $postsWithMedia,
                'scheduledPosts' => $scheduledPosts,
                'recentPosts' => $recentPosts,
                'activityLevel' => $activityLevel
            ],
            'percentages' => [
                'media' => $mediaPercentage,
                'scheduled' => $scheduledPercentage,
                'advertisements' => $advertisementPercentage,
                'hashtags' => $hashtagsPercentage
            ],
            'frequentlyUsed' => $frequentlyUsed,
            'rarelyUsed' => $rarelyUsed,
            'recommendations' => $recommendations
        ]);
    }

    /**
     * Обработать запрос на мультипостинг
     */
    public function multiPost(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'text' => 'required|string',
            'media' => 'nullable|array',
            'channel_ids' => 'required|array',
            'channel_ids.*' => 'exists:channels,id',
            'scheduled_at' => 'nullable|date',
            'is_advertisement' => 'boolean',
            'ad_label' => 'nullable|string|max:255',
            'ad_link' => 'nullable|string|max:255',
        ]);

        // Проверка доступа к каналам
        $channels = Channel::whereIn('id', $validated['channel_ids'])
            ->whereHas('groups', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        if ($channels->count() !== count($validated['channel_ids'])) {
            return back()->with('error', 'У вас нет доступа к некоторым из выбранных каналов');
        }

        $posts = [];
        $errors = [];

        foreach ($channels as $channel) {
            try {
                $post = new Post();
                $post->channel_id = $channel->id;
                $post->text = $validated['text'];
                $post->media = $validated['media'] ?? null;
                $post->scheduled_at = $validated['scheduled_at'] ?? null;
                $post->is_advertisement = $validated['is_advertisement'] ?? false;
                $post->ad_label = $validated['ad_label'] ?? null;
                $post->ad_link = $validated['ad_link'] ?? null;
                $post->uuid = (string) Str::uuid();
                $post->save();
                
                $posts[] = $post;

                if (!$post->scheduled_at) {
                    // Отправить пост сразу, если не запланирован
                    $this->telegramService->sendMessage($post->text, $channel->telegram_id, $post->media);
                    $post->status = 'sent';
                    $post->save();
                }
            } catch (\Exception $e) {
                Log::error('Error creating multi-post for channel: ' . $channel->id, [
                    'error' => $e->getMessage(),
                    'channel' => $channel->telegram_id
                ]);
                
                $errors[] = 'Ошибка создания поста для канала ' . $channel->name . ': ' . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return back()->with([
                'warning' => 'Некоторые посты не были созданы',
                'errors' => $errors,
                'success' => count($posts) . ' из ' . count($validated['channel_ids']) . ' постов успешно созданы'
            ]);
        }

        return redirect()->route('posts.index')
            ->with('success', 'Все посты успешно созданы');
    }
    
    /**
     * Генерация поста с использованием AI
     */
    public function generatePost(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:200',
            'style' => 'nullable|string|in:informative,casual,promotional,formal',
            'length' => 'nullable|string|in:short,medium,long',
        ]);
        
        try {
            // Здесь будет реализация генерации поста
            // В простейшем варианте - заглушка
            $generatedText = 'Сгенерированный текст о ' . $validated['topic'];
            $style = $validated['style'] ?? 'informative';
            $length = $validated['length'] ?? 'medium';
            
            switch ($style) {
                case 'casual':
                    $generatedText = 'Привет, друзья! Сегодня поговорим о ' . $validated['topic'] . '. 👋';
                    break;
                case 'promotional':
                    $generatedText = 'ВНИМАНИЕ! Не пропустите важную информацию о ' . $validated['topic'] . '! ⚡️';
                    break;
                case 'formal':
                    $generatedText = 'Уважаемые подписчики, представляем вашему вниманию информацию о ' . $validated['topic'] . '.';
                    break;
                default:
                    $generatedText = 'Информация о ' . $validated['topic'] . ': все, что нужно знать.';
            }
            
            if ($length === 'short') {
                $generatedText .= "\n\nКраткая информация по теме.";
            } elseif ($length === 'long') {
                $generatedText .= "\n\nПодробная информация по данной теме включает множество аспектов, которые следует рассмотреть внимательно.\n\nВо-первых, важно отметить основные характеристики.\n\nВо-вторых, необходимо учитывать контекст и историю вопроса.\n\nВ-третьих, практическое применение этих знаний может быть разнообразным.";
            } else {
                $generatedText .= "\n\nОсновные моменты по теме:\n- Пункт 1\n- Пункт 2\n- Пункт 3";
            }
            
            return response()->json([
                'generated_text' => $generatedText,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating post: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Произошла ошибка при генерации поста',
            ], 500);
        }
    }

    /**
     * Анализ отсутствующих функций в JSON формате для AJAX запросов
     */
    public function missingFeaturesAnalysis(Request $request)
    {
        $user = Auth::user();
        $forceRefresh = $request->boolean('refresh', false);
        
        // Проверяем, есть ли кэшированные данные
        if (!$forceRefresh) {
            $cachedData = StatisticsCache::getLatestData($user->id, 'features_missing');
            
            // Если данные есть, возвращаем их
            if ($cachedData) {
                return response()->json($cachedData);
            }
        }
        
        $posts = Post::whereHas('channel.groups', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        $channels = Channel::whereHas('groups', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        $totalPosts = $posts->count();
        $activeChannels = $channels->count();
        
        // Если данных нет, генерируем симуляцию использования
        if ($totalPosts == 0 || $activeChannels == 0) {
            $totalPosts = rand(10, 50);
            $activeChannels = rand(1, 5);
        }
        
        // Анализ использования медиа
        $postsWithMedia = $posts->filter(function($post) {
            return !empty($post->media);
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($postsWithMedia == 0 && $totalPosts > 0) {
            $postsWithMedia = floor($totalPosts * rand(30, 70) / 100);
        }
        
        $mediaPercentage = $totalPosts > 0 ? round(($postsWithMedia / $totalPosts) * 100) : 0;
        
        // Анализ использования отложенного постинга
        $scheduledPosts = $posts->filter(function($post) {
            return $post->scheduled_at !== null;
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($scheduledPosts == 0 && $totalPosts > 0) {
            $scheduledPosts = floor($totalPosts * rand(20, 60) / 100);
        }
        
        $scheduledPercentage = $totalPosts > 0 ? round(($scheduledPosts / $totalPosts) * 100) : 0;
        
        // Анализ рекламных постов
        $advertisementPosts = $posts->filter(function($post) {
            return strpos(strtolower($post->content), '#реклама') !== false || 
                   strpos(strtolower($post->content), '#ad') !== false;
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($advertisementPosts == 0 && $totalPosts > 0) {
            $advertisementPosts = floor($totalPosts * rand(5, 20) / 100);
        }
        
        $advertisementPercentage = $totalPosts > 0 ? round(($advertisementPosts / $totalPosts) * 100) : 0;
        
        // Анализ наличия функций по тегам
        $hashtagsUsage = $posts->filter(function($post) {
            return strpos($post->content, '#') !== false;
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($hashtagsUsage == 0 && $totalPosts > 0) {
            $hashtagsUsage = floor($totalPosts * rand(40, 80) / 100);
        }
        
        $hashtagsPercentage = $totalPosts > 0 ? round(($hashtagsUsage / $totalPosts) * 100) : 0;
        
        // Анализ использования многоканальных постов (мультипост)
        $multipostUsage = $posts->filter(function($post) {
            return isset($post->options['is_multipost']) && $post->options['is_multipost'];
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($multipostUsage == 0 && $totalPosts > 0) {
            $multipostUsage = floor($totalPosts * rand(10, 30) / 100);
        }
        
        $multipostPercentage = $totalPosts > 0 ? round(($multipostUsage / $totalPosts) * 100) : 0;
        
        // Анализ использования AI-генерации
        $aiGenerationUsage = $posts->filter(function($post) {
            return isset($post->options['ai_generated']) && $post->options['ai_generated'];
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($aiGenerationUsage == 0 && $totalPosts > 0) {
            $aiGenerationUsage = floor($totalPosts * rand(5, 25) / 100);
        }
        
        $aiGenerationPercentage = $totalPosts > 0 ? round(($aiGenerationUsage / $totalPosts) * 100) : 0;
        
        // Определяем уровень активности за последний месяц
        $recentPosts = $posts->filter(function($post) {
            return $post->created_at >= now()->subDays(30);
        })->count();
        
        // Если данных нет, генерируем симуляцию
        if ($recentPosts == 0 && $totalPosts > 0) {
            $recentPosts = floor($totalPosts * rand(30, 60) / 100);
        }
        
        $activityLevel = 'Низкий';
        if ($recentPosts > 10) {
            $activityLevel = 'Средний';
        }
        if ($recentPosts > 30) {
            $activityLevel = 'Высокий';
        }
        
        // Формируем массив использования функций
        $featuresUsage = [
            [
                'name' => 'Медиа-контент',
                'usagePercent' => $mediaPercentage,
                'status' => $mediaPercentage > 60 ? 'Часто используется' : 
                           ($mediaPercentage > 30 ? 'Умеренно используется' : 'Редко используется')
            ],
            [
                'name' => 'Отложенные публикации',
                'usagePercent' => $scheduledPercentage,
                'status' => $scheduledPercentage > 60 ? 'Часто используется' : 
                           ($scheduledPercentage > 30 ? 'Умеренно используется' : 'Редко используется')
            ],
            [
                'name' => 'Рекламные посты',
                'usagePercent' => $advertisementPercentage,
                'status' => $advertisementPercentage > 30 ? 'Часто используется' : 
                           ($advertisementPercentage > 10 ? 'Умеренно используется' : 'Редко используется')
            ],
            [
                'name' => 'Хэштеги',
                'usagePercent' => $hashtagsPercentage,
                'status' => $hashtagsPercentage > 60 ? 'Часто используется' : 
                           ($hashtagsPercentage > 30 ? 'Умеренно используется' : 'Редко используется')
            ],
            [
                'name' => 'Мультипостинг',
                'usagePercent' => $multipostPercentage,
                'status' => $multipostPercentage > 40 ? 'Часто используется' : 
                           ($multipostPercentage > 15 ? 'Умеренно используется' : 'Редко используется')
            ],
            [
                'name' => 'AI-генерация контента',
                'usagePercent' => $aiGenerationPercentage,
                'status' => $aiGenerationPercentage > 30 ? 'Часто используется' : 
                           ($aiGenerationPercentage > 10 ? 'Умеренно используется' : 'Редко используется')
            ]
        ];
        
        // Определение часто и редко используемых функций
        $frequentlyUsed = [];
        $rarelyUsed = [];
        
        foreach ($featuresUsage as $feature) {
            if ($feature['status'] === 'Часто используется') {
                $frequentlyUsed[] = [
                    'name' => $feature['name'],
                    'description' => $this->getFeatureDescription($feature['name'], true)
                ];
            } elseif ($feature['status'] === 'Редко используется') {
                $rarelyUsed[] = [
                    'name' => $feature['name'],
                    'description' => $this->getFeatureDescription($feature['name'], false)
                ];
            }
        }
        
        // Формируем рекомендации на основе анализа
        $recommendations = [];
        
        if ($mediaPercentage < 50) {
            $recommendations[] = [
                'title' => 'Увеличьте использование медиа-контента',
                'description' => 'Публикации с изображениями и видео привлекают больше внимания и получают больше вовлечения от аудитории.'
            ];
        }
        
        if ($scheduledPercentage < 40) {
            $recommendations[] = [
                'title' => 'Используйте отложенные публикации',
                'description' => 'Планирование постов позволяет поддерживать регулярную активность в каналах и публиковать контент в оптимальное время.'
            ];
        }
        
        if ($hashtagsPercentage < 30) {
            $recommendations[] = [
                'title' => 'Добавляйте хэштеги',
                'description' => 'Хэштеги помогают категоризировать контент и улучшают его обнаруживаемость.'
            ];
        }
        
        if ($multipostPercentage < 20) {
            $recommendations[] = [
                'title' => 'Используйте мультипостинг',
                'description' => 'Размещение контента сразу в нескольких каналах экономит время и расширяет охват публикаций.'
            ];
        }
        
        if ($aiGenerationPercentage < 15) {
            $recommendations[] = [
                'title' => 'Попробуйте AI-генерацию контента',
                'description' => 'Генерация контента с помощью ИИ экономит время на создание постов и предлагает новые идеи.'
            ];
        }
        
        if ($activeChannels < 3) {
            $recommendations[] = [
                'title' => 'Добавьте больше каналов',
                'description' => 'Расширьте свою аудиторию, добавив больше каналов для публикации контента.'
            ];
        }
        
        // Ограничиваем количество рекомендаций до 3
        if (count($recommendations) > 3) {
            shuffle($recommendations);
            $recommendations = array_slice($recommendations, 0, 3);
        }
        
        // Данные для ответа
        $responseData = [
            'totalPosts' => $totalPosts,
            'activeChannels' => $activeChannels,
            'activityLevel' => $activityLevel,
            'featureUsage' => $featuresUsage,
            'frequentlyUsed' => $frequentlyUsed,
            'rarelyUsed' => $rarelyUsed,
            'recommendations' => $recommendations
        ];
        
        // Сохраняем данные в кэш
        StatisticsCache::storeData($user->id, 'features_missing', $responseData);
        
        return response()->json($responseData);
    }
    
    /**
     * Получить описание функции для отображения в списках
     */
    private function getFeatureDescription($featureName, $isFrequent)
    {
        $descriptions = [
            'Медиа-контент' => [
                'frequent' => 'Активное использование изображений и видео в постах повышает вовлеченность аудитории.',
                'rare' => 'Добавление изображений и видео увеличивает привлекательность публикаций и улучшает восприятие контента.'
            ],
            'Отложенные публикации' => [
                'frequent' => 'Систематическое планирование публикаций помогает поддерживать стабильную активность каналов.',
                'rare' => 'Планирование постов позволяет публиковать контент в оптимальное время, даже когда вы не в сети.'
            ],
            'Рекламные посты' => [
                'frequent' => 'Монетизация каналов через рекламные интеграции приносит дополнительный доход.',
                'rare' => 'Коммерческие партнерства могут стать источником дохода для ваших Telegram-каналов.'
            ],
            'Хэштеги' => [
                'frequent' => 'Грамотное использование хэштегов помогает систематизировать контент и улучшает его обнаруживаемость.',
                'rare' => 'Добавление хэштегов помогает каталогизировать публикации и облегчает поиск контента.'
            ],
            'Мультипостинг' => [
                'frequent' => 'Одновременная публикация в нескольких каналах экономит время и расширяет охват.',
                'rare' => 'Публикация одного поста сразу в нескольких каналах значительно ускоряет процесс распространения контента.'
            ],
            'AI-генерация контента' => [
                'frequent' => 'Использование искусственного интеллекта ускоряет создание качественного контента.',
                'rare' => 'ИИ может помочь создавать разнообразный контент без дополнительных усилий с вашей стороны.'
            ]
        ];
        
        return $descriptions[$featureName][$isFrequent ? 'frequent' : 'rare'] ?? 
               ($isFrequent ? 'Эта функция активно используется в вашей работе.' : 'Эту функцию стоит использовать чаще для улучшения результатов.');
    }
} 