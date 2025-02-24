<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramService
{
    protected $token;
    protected $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
    }

    public function sendMessage($channelId, $text, $media = null)
    {
        if ($media) {
            return $this->sendMediaMessage($channelId, $text, $media);
        }

        try {
            $response = Http::post($this->apiUrl . $this->token . '/sendMessage', [
                'chat_id' => $channelId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API error: ' . $response->json()['description'] ?? 'Unknown error');
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Telegram API exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendMediaMessage($channelId, $text, $media)
    {
        // Если несколько файлов, используем sendMediaGroup
        if (is_array($media) && count($media) > 1) {
            return $this->sendMediaGroup($channelId, $text, $media);
        }
        
        // Если один файл, определяем тип и отправляем соответствующим методом
        $mediaPath = is_array($media) ? $media[0] : $media;
        $fullPath = Storage::disk('public')->path($mediaPath);
        $mimeType = mime_content_type($fullPath);
        
        if (strpos($mimeType, 'image/') === 0) {
            return $this->sendPhoto($channelId, $text, $fullPath);
        } elseif (strpos($mimeType, 'video/') === 0) {
            return $this->sendVideo($channelId, $text, $fullPath);
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return $this->sendAudio($channelId, $text, $fullPath);
        } else {
            return $this->sendDocument($channelId, $text, $fullPath);
        }
    }
    
    protected function sendPhoto($channelId, $caption, $photoPath)
    {
        return Http::attach('photo', file_get_contents($photoPath), basename($photoPath))
            ->post($this->apiUrl . $this->token . '/sendPhoto', [
                'chat_id' => $channelId,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
    }
    
    protected function sendVideo($channelId, $caption, $videoPath)
    {
        return Http::attach('video', file_get_contents($videoPath), basename($videoPath))
            ->post($this->apiUrl . $this->token . '/sendVideo', [
                'chat_id' => $channelId,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
    }
    
    protected function sendAudio($channelId, $caption, $audioPath)
    {
        return Http::attach('audio', file_get_contents($audioPath), basename($audioPath))
            ->post($this->apiUrl . $this->token . '/sendAudio', [
                'chat_id' => $channelId,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
    }
    
    protected function sendDocument($channelId, $caption, $docPath)
    {
        return Http::attach('document', file_get_contents($docPath), basename($docPath))
            ->post($this->apiUrl . $this->token . '/sendDocument', [
                'chat_id' => $channelId,
                'caption' => $caption,
                'parse_mode' => 'HTML'
            ]);
    }
    
    protected function sendMediaGroup($channelId, $caption, $mediaPaths)
    {
        $media = [];
        $attachments = [];
        
        foreach ($mediaPaths as $index => $path) {
            $fullPath = Storage::disk('public')->path($path);
            $mimeType = mime_content_type($fullPath);
            $fileContents = file_get_contents($fullPath);
            $fileName = "file{$index}";
            
            $mediaType = 'document';
            if (strpos($mimeType, 'image/') === 0) {
                $mediaType = 'photo';
            } elseif (strpos($mimeType, 'video/') === 0) {
                $mediaType = 'video';
            } elseif (strpos($mimeType, 'audio/') === 0) {
                $mediaType = 'audio';
            }
            
            $media[] = [
                'type' => $mediaType,
                'media' => "attach://{$fileName}",
                'caption' => $index === 0 ? $caption : '',
                'parse_mode' => 'HTML'
            ];
            
            $attachments[$fileName] = $fileContents;
        }
        
        $request = Http::asMultipart();
        foreach ($attachments as $name => $contents) {
            $request->attach($name, $contents, $name);
        }
        
        return $request->post($this->apiUrl . $this->token . '/sendMediaGroup', [
            'chat_id' => $channelId,
            'media' => json_encode($media)
        ]);
    }

    public function checkBotRights($channelId)
    {
        $response = Http::get($this->apiUrl . $this->token . '/getChatMember', [
            'chat_id' => $channelId,
            'user_id' => $this->getBotId()
        ]);

        if ($response->successful()) {
            $status = $response->json()['result']['status'];
            return in_array($status, ['administrator', 'creator']);
        }

        return false;
    }

    protected function getBotId()
    {
        $response = Http::get($this->apiUrl . $this->token . '/getMe');
        return $response->json()['result']['id'];
    }
    
    public function getChannelInfo($channelId)
    {
        $response = Http::get($this->apiUrl . $this->token . '/getChat', [
            'chat_id' => $channelId
        ]);
        
        if ($response->successful()) {
            return $response->json()['result'];
        }
        
        return null;
    }
    
    public function setWebhook($url)
    {
        return Http::post($this->apiUrl . $this->token . '/setWebhook', [
            'url' => $url,
            'allowed_updates' => ['message', 'channel_post', 'callback_query']
        ]);
    }
    
    public function deleteWebhook()
    {
        return Http::post($this->apiUrl . $this->token . '/deleteWebhook');
    }
} 