<?php

namespace App\Http\Controllers;

use App\Models\TelegramChannel;
use App\Models\TelegramPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TelegramController extends Controller
{
    private function getBotToken()
    {
        $settings = json_decode(Storage::get('settings.json'), true);
        return $settings['telegram_bot_token'] ?? null;
    }
    
    public function index()
    {
        $channels = TelegramChannel::where('user_id', auth()->id())->get();
        
        return view('telegram.channels.index', compact('channels'));
    }
    
    public function create()
    {
        return view('telegram.channels.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'channel_username' => 'required|string|max:255',
        ]);
        
        // Проверка существования канала и получение chat_id
        $channelInfo = $this->getChannelInfo($validated['channel_username']);
        
        if (!$channelInfo) {
            return back()->withErrors(['channel_username' => 'Канал не найден или бот не имеет доступа к каналу']);
        }
        
        // Создание записи канала
        TelegramChannel::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'channel_username' => $validated['channel_username'],
            'chat_id' => $channelInfo['id'],
            'members_count' => $channelInfo['members_count'] ?? 0,
        ]);
        
        return redirect()->route('telegram.channels.index')->with('success', 'Канал успешно добавлен');
    }
    
    private function getChannelInfo($channelUsername)
    {
        $token = $this->getBotToken();
        
        if (!$token) {
            return null;
        }
        
        // Проверяем, начинается ли username с @
        if (substr($channelUsername, 0, 1) !== '@') {
            $channelUsername = '@' . $channelUsername;
        }
        
        try {
            $response = Http::get("https://api.telegram.org/bot{$token}/getChat", [
                'chat_id' => $channelUsername
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['ok']) {
                    return $data['result'];
                }
            }
        } catch (\Exception $e) {
            // Логирование ошибки
            logger()->error('Telegram API Error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    // Методы для работы с постами
    
    public function posts($channelId)
    {
        $channel = TelegramChannel::findOrFail($channelId);
        $posts = TelegramPost::where('channel_id', $channelId)->orderBy('created_at', 'desc')->get();
        
        return view('telegram.posts.index', compact('channel', 'posts'));
    }
    
    public function createPost($channelId)
    {
        $channel = TelegramChannel::findOrFail($channelId);
        
        return view('telegram.posts.create', compact('channel'));
    }
    
    public function storePost(Request $request, $channelId)
    {
        $channel = TelegramChannel::findOrFail($channelId);
        
        $validated = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'schedule_time' => 'nullable|date|after:now',
        ]);
        
        $post = new TelegramPost();
        $post->channel_id = $channel->id;
        $post->content = $validated['content'];
        
        // Обработка изображения, если оно загружено
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('telegram_posts', 'public');
            $post->image_path = $path;
        }
        
        // Планирование отправки, если указано время
        if (!empty($validated['schedule_time'])) {
            $post->scheduled_at = $validated['schedule_time'];
            $post->status = 'scheduled';
        } else {
            // Отправляем пост сразу
            $result = $this->sendPostToTelegram($post);
            
            $post->status = $result ? 'sent' : 'failed';
            $post->message_id = $result['message_id'] ?? null;
        }
        
        $post->save();
        
        return redirect()->route('telegram.posts', $channel->id)
            ->with('success', $post->status === 'scheduled' 
                ? 'Пост запланирован на ' . $validated['schedule_time'] 
                : 'Пост успешно отправлен');
    }
    
    private function sendPostToTelegram(TelegramPost $post)
    {
        $token = $this->getBotToken();
        $channel = TelegramChannel::find($post->channel_id);
        
        if (!$token || !$channel) {
            return false;
        }
        
        try {
            // Если есть изображение
            if ($post->image_path) {
                $imageUrl = asset('storage/' . $post->image_path);
                
                $response = Http::attach(
                    'photo', 
                    file_get_contents(storage_path('app/public/' . $post->image_path)), 
                    basename($post->image_path)
                )->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id' => $channel->chat_id,
                    'caption' => $post->content,
                    'parse_mode' => 'HTML'
                ]);
            } else {
                // Только текст
                $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $channel->chat_id,
                    'text' => $post->content,
                    'parse_mode' => 'HTML'
                ]);
            }
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['ok']) {
                    return $data['result'];
                }
            }
        } catch (\Exception $e) {
            // Логирование ошибки
            logger()->error('Telegram Post Error: ' . $e->getMessage());
        }
        
        return false;
    }
    
    // Метод для получения статистики
    public function statistics()
    {
        $channels = TelegramChannel::where('user_id', auth()->id())->get();
        $totalPosts = TelegramPost::whereIn('channel_id', $channels->pluck('id'))->count();
        $sentPosts = TelegramPost::whereIn('channel_id', $channels->pluck('id'))->where('status', 'sent')->count();
        $scheduledPosts = TelegramPost::whereIn('channel_id', $channels->pluck('id'))->where('status', 'scheduled')->count();
        
        return view('telegram.statistics', compact('channels', 'totalPosts', 'sentPosts', 'scheduledPosts'));
    }

    /**
     * Отправка запланированного поста
     */
    public function sendScheduledPost(TelegramPost $post)
    {
        // Если время отправки не наступило, пропускаем
        if ($post->scheduled_at > now()) {
            return false;
        }
        
        // Отправляем пост
        $result = $this->sendPostToTelegram($post);
        
        if ($result) {
            $post->status = 'sent';
            $post->sent_at = now();
            $post->message_id = $result['message_id'] ?? null;
            $post->save();
            
            return true;
        }
        
        // Если отправка не удалась, помечаем как failed
        $post->status = 'failed';
        $post->save();
        
        return false;
    }
} 