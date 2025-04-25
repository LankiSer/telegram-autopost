<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChannelGroupController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPlanController;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Получаем каналы пользователя
    $channelIds = $user->channels()->pluck('id')->toArray();
    
    // Собираем статистику
    $stats = [
        'totalChannels' => $user->channels()->count(),
        'totalPosts' => Post::whereIn('channel_id', $channelIds)->count(),
        'totalGroups' => $user->channelGroups()->count(),
    ];
    
    // Получаем последние посты
    $recentPosts = Post::whereIn('channel_id', $channelIds)
        ->with('channel')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'recentPosts' => $recentPosts
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    
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
    Route::get('/statistics', [StatisticsController::class, 'userStats'])->name('statistics');
    
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
});

require __DIR__.'/auth.php';
