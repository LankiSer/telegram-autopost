<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelGroup;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Отображает админ-панель
     */
    public function dashboard()
    {
        // Получаем общую статистику
        $totalUsers = User::count();
        $totalChannels = Channel::count();
        $totalPosts = Post::count();
        $totalGroups = ChannelGroup::count();
        
        // Статистика по регистрациям пользователей
        $userRegistrations = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(1))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Статистика по созданию постов
        $postsCreated = Post::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(1))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Топ активных пользователей
        $topUsers = User::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit(5)
            ->get();
            
        // Недавно зарегистрированные пользователи
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Формируем массив данных для передачи в шаблон
        $stats = [
            'totalUsers' => $totalUsers,
            'totalChannels' => $totalChannels,
            'totalPosts' => $totalPosts,
            'totalGroups' => $totalGroups,
            'userRegistrations' => $userRegistrations,
            'postsCreated' => $postsCreated,
            'topUsers' => $topUsers,
            'recentUsers' => $recentUsers
        ];
        
        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }
    
    /**
     * Отображает список пользователей
     */
    public function users()
    {
        $users = User::withCount('channels', 'posts')
            ->withSum('channels as subscribers_count', 'subscribers_count')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return Inertia::render('Admin/Users', [
            'users' => $users
        ]);
    }
    
    /**
     * Отображает список каналов
     */
    public function channels()
    {
        $channels = Channel::with('user')
            ->withCount('posts')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return Inertia::render('Admin/Channels', [
            'channels' => $channels
        ]);
    }
    
    /**
     * Отображает список постов
     */
    public function posts()
    {
        $posts = Post::with(['channel', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return Inertia::render('Admin/Posts', [
            'posts' => $posts
        ]);
    }
    
    public function subscriptions()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->paginate(10);
            
        return view('admin.subscriptions', compact('subscriptions'));
    }
    
    public function plans()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->get();
            
        return view('admin.plans', compact('plans'));
    }
    
    /**
     * Отображает настройки системы
     */
    public function settings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        return Inertia::render('Admin/Settings', [
            'settings' => $settings
        ]);
    }
    
    /**
     * Отображает логи системы
     */
    public function logs()
    {
        // Получаем последние 100 записей логов
        $logs = DB::table('logs')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
            
        return Inertia::render('Admin/Logs', [
            'logs' => $logs
        ]);
    }

    /**
     * Обновление пользователя
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        
        $user->update($validated);
        
        return redirect()->route('admin.users')->with('success', 'Пользователь успешно обновлен!');
    }

    /**
     * Удаление пользователя
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'Пользователь успешно удален!');
    }

    /**
     * Обновление канала
     */
    public function updateChannel(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $channel->update($validated);
        
        return redirect()->route('admin.channels')->with('success', 'Канал успешно обновлен!');
    }

    /**
     * Удаление канала
     */
    public function destroyChannel($id)
    {
        $channel = Channel::findOrFail($id);
        $channel->delete();
        
        return redirect()->route('admin.channels')->with('success', 'Канал успешно удален!');
    }

    /**
     * Обновление поста
     */
    public function updatePost(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,scheduled,published,failed',
        ]);
        
        $post->update($validated);
        
        return redirect()->route('admin.posts')->with('success', 'Пост успешно обновлен!');
    }

    /**
     * Удаление поста
     */
    public function destroyPost($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        
        return redirect()->route('admin.posts')->with('success', 'Пост успешно удален!');
    }
} 