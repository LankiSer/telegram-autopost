<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChannelGroupController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\GroupAdvertisingController;
use App\Http\Controllers\GroupModerationController;
use App\Http\Controllers\ExtendedFeaturesController;
use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Post;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\PostsViewController;
use App\Http\Controllers\PostGenerationController;

// Diagnostic route - doesn't require auth
Route::get('/check', function() {
    return [
        'status' => 'ok',
        'message' => 'The server is running correctly',
        'time' => now()->toDateTimeString()
    ];
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Новые страницы для анализа функционала
Route::get('/features/missing', function () {
    return Inertia::render('Features/Missing');
})->middleware(['auth'])->name('features.missing');

Route::get('/features/analysis', function () {
    return Inertia::render('Features/Analysis');
})->middleware(['auth'])->name('features.analysis');

Route::get('/features/generate', function () {
    return Inertia::render('Features/GeneratePost');
})->middleware(['auth'])->name('features.generate');

Route::get('/features/multi-post', function () {
    return Inertia::render('Features/MultiPost');
})->middleware(['auth'])->name('features.multi-post');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Channel routes
    Route::resource('channels', ChannelController::class);
    Route::get('channels/{channel}/posts', [ChannelController::class, 'posts'])->name('channels.posts');
    Route::get('channels/{channel}/auto-posting/edit', [ChannelController::class, 'editAutoPosting'])->name('channels.auto-posting.edit');
    Route::post('channels/{channel}/auto-posting', [ChannelController::class, 'updateAutoPosting'])->name('channels.auto-posting.update');
    Route::get('channels/{channel}/connect-bot', [ChannelController::class, 'connectBot'])->name('channels.connect-bot');
    Route::post('channels/{channel}/force-connect', [ChannelController::class, 'forceConnect'])->name('channels.force-connect');
    Route::get('channels/update-all-status', [ChannelController::class, 'updateAllStatus'])->name('channels.update-all-status');
    
    // Post routes
    Route::resource('posts', PostController::class);
    // Debug route for posts
    Route::get('/posts-debug', function() {
        return response()->json(['status' => 'success', 'message' => 'Posts debug route is working']);
    });
    // Simple HTML route for posts
    Route::get('/posts-html', function() {
        return '<h1>Posts HTML Page</h1><p>This is a simple HTML page to test routing.</p><p><a href="/posts">Go to Posts Inertia page</a></p>';
    });
    Route::get('channels/{channel}/posts', [PostController::class, 'channelPosts'])->name('posts.channel');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::get('/posts/generator', [PostController::class, 'generator'])->name('posts.generator');
    
    // Channel Groups routes
    Route::resource('channel-groups', ChannelGroupController::class);
    // Debug route for channel groups
    Route::get('/channel-groups-debug', function() {
        return response()->json(['status' => 'success', 'message' => 'Channel Groups debug route is working']);
    });
    // Simple HTML route for channel groups
    Route::get('/channel-groups-html', function() {
        return '<h1>Channel Groups HTML Page</h1><p>This is a simple HTML page to test routing.</p><p><a href="/channel-groups">Go to Channel Groups Inertia page</a></p>';
    });
    Route::post('channel-groups/{channelGroup}/cross-promotion', [ChannelGroupController::class, 'generateCrossPromotion'])
        ->name('channel-groups.cross-promotion');
    
    // Маршруты для рекламной рассылки по группе каналов
    Route::get('channel-groups/{channelGroup}/advertising', [GroupAdvertisingController::class, 'create'])
        ->name('channel-groups.advertising.create');
    Route::post('channel-groups/{channelGroup}/advertising', [GroupAdvertisingController::class, 'store'])
        ->name('channel-groups.advertising.store');
    
    // Subscription routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    
    // Debug route for subscription plans
    Route::get('/subscriptions/debug', function() {
        $plans = \App\Models\SubscriptionPlan::all();
        $currentSubscription = auth()->user()->activeSubscription();
        return response()->json([
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'user' => auth()->user()
        ]);
    });
    
    // Statistics routes
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
    
    // Scheduler routes
    Route::get('scheduler', [SchedulerController::class, 'index'])->name('scheduler.index');
    Route::post('scheduler', [SchedulerController::class, 'store'])->name('scheduler.store');
    Route::put('scheduler/{post}', [SchedulerController::class, 'update'])->name('scheduler.update');
    Route::post('scheduler/{post}/cancel', [SchedulerController::class, 'cancel'])->name('scheduler.cancel');
    Route::post('scheduler/{post}/publish-now', [SchedulerController::class, 'publishNow'])->name('scheduler.publish-now');
    
    // Admin routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/statistics', [StatisticsController::class, 'adminStats'])->name('statistics');
        
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/channels', [AdminController::class, 'channels'])->name('channels');
        Route::get('/posts', [AdminController::class, 'posts'])->name('posts');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        
        // Admin plans routes
        Route::resource('plans', AdminPlanController::class);
    });

    Route::get('/debug/posts', [PostController::class, 'debug']);
    Route::get('/debug/channel-groups', [ChannelGroupController::class, 'debug']);

    // Новый маршрут для генерации постов
    Route::post('/posts/generate', [PostGenerationController::class, 'generate'])->name('posts.generate');

    // Расширенный функционал
    Route::get('/features/missing', [ExtendedFeaturesController::class, 'missingFeatures'])->name('features.missing');
    Route::get('/features/analysis', [GroupModerationController::class, 'moderationDashboard'])->name('features.analysis');
    Route::post('/features/generate-post', [ExtendedFeaturesController::class, 'generatePost'])->name('features.generate-post');
    
    // Мультипостинг
    Route::post('/posts/multi', [PostController::class, 'multiPost'])->name('posts.multi');
    
    // Модерация групп
    Route::get('/groups/moderation', [GroupModerationController::class, 'index'])->name('groups.moderation');
    Route::get('/groups/{group}/settings', [GroupModerationController::class, 'settings'])->name('groups.moderation.settings');
    Route::post('/groups/{group}/settings', [GroupModerationController::class, 'updateSettings'])->name('groups.moderation.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Детальная статистика
    Route::get('/statistics/channel/{channel}', [StatisticsController::class, 'channelDetail'])
        ->name('statistics.channel.detail');
    Route::get('/statistics/posts', [StatisticsController::class, 'postAnalytics'])
        ->name('statistics.posts');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Extended Features Routes
    Route::get('/features/missing', [App\Http\Controllers\ExtendedFeaturesController::class, 'missingFeatures'])->name('features.missing');
    Route::get('/features/missing/analysis', [App\Http\Controllers\ExtendedFeaturesController::class, 'missingFeaturesAnalysis'])->name('features.missing.analysis');
    Route::post('/features/multipost', [App\Http\Controllers\ExtendedFeaturesController::class, 'multiPost'])->name('features.multipost.store');
    Route::post('/features/generate-post', [App\Http\Controllers\ExtendedFeaturesController::class, 'generatePost'])->name('features.generate.post');
});

require __DIR__.'/auth.php';
