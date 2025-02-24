<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::withCount(['channels', 'subscriptions'])
            ->with('subscriptions.plan')
            ->paginate(10);
            
        return view('admin.users', compact('users'));
    }
    
    public function channels()
    {
        $channels = Channel::with('user')
            ->withCount('posts')
            ->paginate(10);
            
        return view('admin.channels', compact('channels'));
    }
    
    public function posts()
    {
        $posts = Post::with(['channel.user'])
            ->latest()
            ->paginate(15);
            
        return view('admin.posts', compact('posts'));
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
    
    public function settings()
    {
        return view('admin.settings');
    }
    
    public function logs()
    {
        // Получаем файлы журналов из стандартного места в Laravel
        $logFiles = [];
        $logPath = storage_path('logs');
        
        if (file_exists($logPath)) {
            $files = array_diff(scandir($logPath), ['.', '..']);
            foreach ($files as $file) {
                if (str_ends_with($file, '.log')) {
                    $logFiles[] = [
                        'name' => $file,
                        'size' => round(filesize($logPath . '/' . $file) / 1024, 2),
                        'modified' => date("Y-m-d H:i:s", filemtime($logPath . '/' . $file))
                    ];
                }
            }
        }
        
        return view('admin.logs', compact('logFiles'));
    }
} 