<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();
        Log::info('Received Telegram update', ['update' => $update]);
        
        // Обработка добавления бота в канал
        if (isset($update['my_chat_member'])) {
            $chatMember = $update['my_chat_member'];
            $chatId = $chatMember['chat']['id'];
            $newStatus = $chatMember['new_chat_member']['status'];
            
            // Если бота добавили как админа
            if (in_array($newStatus, ['administrator', 'creator'])) {
                $this->updateChannelBotStatus($chatId, true);
            } 
            // Если бота удалили или понизили права
            elseif (in_array($newStatus, ['left', 'kicked', 'restricted'])) {
                $this->updateChannelBotStatus($chatId, false);
            }
        }
        
        return response()->json(['ok' => true]);
    }
    
    protected function updateChannelBotStatus($chatId, $status)
    {
        // Поиск канала по ID
        $channel = Channel::where('telegram_channel_id', $chatId)
            ->orWhere('telegram_channel_id', '@' . ($chatId['username'] ?? ''))
            ->first();
        
        if ($channel) {
            $channel->update(['bot_added' => $status]);
            Log::info('Updated channel bot status', [
                'channel_id' => $channel->id,
                'telegram_channel_id' => $chatId,
                'status' => $status
            ]);
        }
    }
} 