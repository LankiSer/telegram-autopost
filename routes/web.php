<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;

// Главная страница
Route::get('/', function () {
    return view('pages.home');
});

// Защищенные маршруты
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        // Перенаправляем админа на админскую панель
        if (auth()->user()->email === 'admin@admin.com') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard.user');
    })->name('dashboard');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Каналы
    Route::resource('channels', ChannelController::class);

    // Посты
    Route::resource('posts', PostController::class);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');

    // Подписки
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscription.plans');
    Route::post('/subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Добавьте этот маршрут в группу маршрутов, защищенных аутентификацией
    Route::get('/channels/{channel}/connect-bot', [App\Http\Controllers\ChannelController::class, 'connectBot'])
        ->middleware(['auth'])
        ->name('channels.connect-bot');
});

// Маршруты администратора
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.admin');
    })->name('dashboard');
    
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/channels', [AdminController::class, 'channels'])->name('channels');
    Route::get('/posts', [AdminController::class, 'posts'])->name('posts');
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    
    // CRUD для планов подписки
    Route::resource('plans', AdminPlanController::class);

    // Добавить в группу маршрутов админа
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::put('/channels/{id}', [AdminController::class, 'updateChannel'])->name('channels.update');
    Route::delete('/channels/{id}', [AdminController::class, 'destroyChannel'])->name('channels.destroy');

    Route::put('/posts/{id}', [AdminController::class, 'updatePost'])->name('posts.update');
    Route::delete('/posts/{id}', [AdminController::class, 'destroyPost'])->name('posts.destroy');
});

// Маршруты для настроек админа
Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings');
Route::post('/admin/settings', [SettingsController::class, 'update'])->name('admin.settings.update');

// Telegram маршруты
Route::middleware(['auth'])->prefix('telegram')->name('telegram.')->group(function () {
    // Каналы
    Route::get('/channels', [App\Http\Controllers\TelegramController::class, 'index'])->name('channels.index');
    Route::get('/channels/create', [App\Http\Controllers\TelegramController::class, 'create'])->name('channels.create');
    Route::post('/channels', [App\Http\Controllers\TelegramController::class, 'store'])->name('channels.store');
    
    // Посты
    Route::get('/channels/{channel}/posts', [App\Http\Controllers\TelegramController::class, 'posts'])->name('posts');
    Route::get('/channels/{channel}/posts/create', [App\Http\Controllers\TelegramController::class, 'createPost'])->name('posts.create');
    Route::post('/channels/{channel}/posts', [App\Http\Controllers\TelegramController::class, 'storePost'])->name('posts.store');
    
    // Статистика
    Route::get('/statistics', [App\Http\Controllers\TelegramController::class, 'statistics'])->name('statistics');
});

// Маршруты аутентификации
require __DIR__.'/auth.php';

// Маршрут для страниц из директории pages
// Должен быть последним, чтобы не перехватывать другие маршруты
Route::get('/{page}', function ($page) {
    // Исключаем определенные пути
    $excludedPaths = ['profile', 'dashboard', 'login', 'register', 'password', 'verify-email', 
        'channels', 'posts', 'subscriptions'];
    if (in_array($page, $excludedPaths)) {
        abort(404);
    }

    if (view()->exists("pages.{$page}")) {
        return view("pages.{$page}");
    }
    
    abort(404);
})->where('page', '[a-zA-Z0-9\-\/]+');

// Маршрут для отображения постов конкретного канала
Route::get('/channels/{channel}/posts', [PostController::class, 'channelPosts'])->name('posts.channel');

// Временный маршрут для отладки статуса бота (удалите после исправления проблемы)
Route::get('/debug/channels/{channel}', function (\App\Models\Channel $channel) {
    return [
        'channel' => $channel->only(['id', 'name', 'telegram_username', 'bot_added']),
        'updated' => \App\Models\Channel::where('id', $channel->id)->update(['bot_added' => true]),
        'after_update' => \App\Models\Channel::find($channel->id)->only(['id', 'name', 'telegram_username', 'bot_added'])
    ];
})->middleware(['auth']);

// Маршрут для принудительного обновления статуса бота
Route::post('/channels/{channel}/force-connect', [App\Http\Controllers\ChannelController::class, 'forceConnect'])
    ->middleware(['auth'])
    ->name('channels.force-connect');

// Временный маршрут для обновления статуса всех каналов
Route::get('/update-all-channels-status', function () {
    $channels = \App\Models\Channel::where('user_id', auth()->id())
        ->where('type', 'telegram')
        ->get();
    
    $updated = 0;
    foreach ($channels as $channel) {
        if ($channel->telegram_username) {
            $channel->bot_added = true;
            $channel->save();
            $updated++;
        }
    }
    
    return redirect()->route('channels.index')
        ->with('success', "Статус обновлен для {$updated} каналов");
})->middleware(['auth'])->name('channels.update-all-status');

// Тестовая отправка сообщения в канал через API
Route::post('/api/test-telegram-message/{channel}', function (\App\Models\Channel $channel, \App\Services\TelegramService $telegram) {
    // Проверка авторизации и принадлежности канала
    if (!auth()->check() || $channel->user_id !== auth()->id()) {
        return response()->json(['success' => false, 'error' => 'Нет доступа к каналу'], 403);
    }
    
    // Логируем информацию о канале
    \Illuminate\Support\Facades\Log::info('Тестовое сообщение: Информация о канале', [
        'id' => $channel->id, 
        'username' => $channel->telegram_username,
        'telegram_channel_id' => $channel->telegram_channel_id,
        'telegram_chat_id' => $channel->telegram_chat_id,
        'bot_added' => $channel->bot_added
    ]);
    
    try {
        // Если ID канала неизвестен, пробуем получить его
        if (!$channel->telegram_channel_id) {
            // Пытаемся получить ID канала
            $botResult = $telegram->checkBotAccess($channel->telegram_username);
            
            if (isset($botResult['success']) && $botResult['success'] && isset($botResult['chat_id'])) {
                $channel->telegram_channel_id = $botResult['chat_id'];
                $channel->telegram_chat_id = $botResult['chat_id'];
                $channel->save();
                
                \Illuminate\Support\Facades\Log::info('Тестовое сообщение: ID канала обновлен', [
                    'chat_id' => $botResult['chat_id']
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'error' => 'Невозможно определить ID канала. Пожалуйста, введите ID канала вручную.',
                    'bot_result' => $botResult
                ]);
            }
        }
        
        // Отправляем тестовое сообщение
        $response = $telegram->sendMessage(
            $channel->telegram_channel_id,
            "🔍 *Тестовое сообщение*\n\nЕсли вы видите это сообщение, значит бот успешно подключен к каналу.\n\nДата: " . now()->format('d.m.Y H:i:s'),
            null // без медиа
        );
        
        \Illuminate\Support\Facades\Log::info('Тестовое сообщение: Результат отправки', $response);
        
        if (isset($response['success']) && $response['success']) {
            return response()->json([
                'success' => true, 
                'message' => 'Тестовое сообщение успешно отправлено'
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'error' => $response['error'] ?? 'Неизвестная ошибка',
                'response' => $response
            ]);
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Тестовое сообщение: Ошибка', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
    }
})->middleware(['auth'])->name('api.test-telegram-message');
