<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPlanController;
use Illuminate\Support\Facades\Route;

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
