<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use App\Services\TelegramService;

class ChannelController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index()
    {
        $channels = auth()->user()->channels()->paginate(10);
        return view('channels.index', compact('channels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'telegram_channel_id' => 'required|string',
            'name' => 'required|string|max:255'
        ]);

        // Проверяем лимит каналов по подписке
        $subscription = auth()->user()->activeSubscription();
        if (!$subscription || auth()->user()->channels()->count() >= $subscription->plan->channel_limit) {
            return back()->with('error', 'Достигнут лимит каналов для вашего тарифного плана');
        }

        // Проверяем права бота в канале
        if (!$this->telegram->checkBotRights($validated['telegram_channel_id'])) {
            return back()->with('error', 'Бот должен быть добавлен в канал с правами администратора');
        }

        $channel = auth()->user()->channels()->create($validated);

        return redirect()->route('channels.index')
            ->with('success', 'Канал успешно добавлен');
    }
} 