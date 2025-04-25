<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Сервис для работы с Telegram API
 * 
 * Для тестирования:
 * - Токен бота: 7833266654:AAHeOa--bxe8Tj4hYHXBsNMbnn6plrOiYFU
 */
class TelegramService
{
    protected $token;
    protected $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        // Получаем токен из настроек администратора
        $admin = \App\Models\User::where('is_admin', true)->first();
        
        if ($admin && !empty($admin->telegram_bot_token)) {
            $this->token = $admin->telegram_bot_token;
            \Log::info('Telegram bot token loaded from admin settings');
            return;
        }
        
        // Если не нашли в админе, пробуем получить токен из конфига
        $this->token = config('services.telegram.bot_token');

        // Если токен не найден в конфиге, пробуем получить из settings.json
        if (empty($this->token)) {
            $settings = json_decode(Storage::get('settings.json') ?? '{}', true);
            $this->token = $settings['telegram_bot_token'] ?? null;
        }

        if (empty($this->token)) {
            \Log::error('Telegram bot token not found in admin settings, config or settings.json');
        } else {
            \Log::info('Telegram bot token configured successfully');
        }
    }

    /**
     * Экранирует специальные символы в тексте для Telegram MarkdownV2
     * 
     * @param string $text Текст для экранирования
     * @return string Экранированный текст
     */
    private function escapeMarkdown($text) 
    {
        // Символы, которые нужно экранировать в MarkdownV2
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        
        // Сохраняем хештеги перед экранированием
        $hashtags = [];
        if (preg_match_all('/(^|\s)(#[\w\d]+)/', $text, $matches)) {
            $hashtags = $matches[2];
            // Временно заменяем хештеги на маркеры
            foreach ($hashtags as $index => $hashtag) {
                $text = str_replace($hashtag, "HASHTAG_MARKER_{$index}", $text);
            }
        }
        
        // Экранируем каждый спецсимвол
        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        
        // Восстанавливаем хештеги
        foreach ($hashtags as $index => $hashtag) {
            // Убираем экранирование для символа # в хештегах
            $cleanHashtag = substr($hashtag, 0, 1) . substr($hashtag, 1);
            $text = str_replace("HASHTAG_MARKER_{$index}", $cleanHashtag, $text);
        }
        
        return $text;
    }

    /**
     * Отправляет сообщение с медиа-файлами в Telegram
     *
     * @param string $chatId ID чата или канала
     * @param string $text Текст сообщения
     * @param array|null $media Массив путей к медиа-файлам
     * @return array Результат отправки
     */
    public function sendMessage($chatId, $text, $media = null)
    {
        try {
            // Проверяем наличие токена
            $token = $this->getToken();
            if (empty($token)) {
                return [
                    'success' => false,
                    'error' => 'Telegram bot token is empty'
                ];
            }
            
            // Автоматически экранируем спецсимволы для Markdown V2
            $escapedText = $this->escapeMarkdown($text);
            
            // Если передан массив медиафайлов, отправляем медиа-группу
            if (is_array($media) && count($media) > 0) {
                // Подготовка данных для media group
                return $this->sendMediaGroup($chatId, $escapedText, $media);
            }
            
            // Обычное сообщение с текстом
            $params = [
                'chat_id' => $chatId,
                'text' => $escapedText,
                'parse_mode' => 'MarkdownV2', // Включаем поддержку Markdown
                'disable_web_page_preview' => false
            ];
            
            // Если есть одиночный медиафайл, отправляем фото или видео
            if (!empty($media) && is_string($media)) {
                $mediaType = $this->getMediaType($media);
                
                if ($mediaType === 'photo') {
                    // Отправляем фото с подписью
                    $params = [
                        'chat_id' => $chatId,
                        'photo' => $media,
                        'caption' => $escapedText,
                        'parse_mode' => 'MarkdownV2'
                    ];
                    
                    $response = Http::withoutVerifying()
                        ->timeout(30)
                        ->post("https://api.telegram.org/bot{$token}/sendPhoto", $params);
                } 
                elseif ($mediaType === 'video') {
                    // Отправляем видео с подписью
                    $params = [
                        'chat_id' => $chatId,
                        'video' => $media,
                        'caption' => $escapedText,
                        'parse_mode' => 'MarkdownV2'
                    ];
                    
                    $response = Http::withoutVerifying()
                        ->timeout(30)
                        ->post("https://api.telegram.org/bot{$token}/sendVideo", $params);
                }
                else {
                    // Если тип медиа не определен, отправляем обычное сообщение
                    $response = Http::withoutVerifying()
                        ->timeout(30)
                        ->post("https://api.telegram.org/bot{$token}/sendMessage", $params);
                }
            } else {
                // Отправляем обычное текстовое сообщение
                $response = Http::withoutVerifying()
                    ->timeout(30)
                    ->post("https://api.telegram.org/bot{$token}/sendMessage", $params);
            }
            
            \Log::info('Telegram API response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            
            if ($response->successful() && isset($response->json()['ok']) && $response->json()['ok'] === true) {
                return [
                    'success' => true,
                    'message_id' => $response->json()['result']['message_id'] ?? null,
                    'result' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'error' => $response->json()['description'] ?? 'Unknown error'
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error sending message to Telegram', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function getMediaType($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $photoExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'mkv'];
        $audioExtensions = ['mp3', 'ogg', 'wav'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'zip', 'rar'];

        if (in_array($extension, $photoExtensions)) {
            return 'photo';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        }

        // По умолчанию считаем файл документом
        return 'document';
    }

    /**
     * Проверяет доступ бота к указанному каналу
     */
    public function checkBotAccess($username)
    {
        if (!$this->token) {
            Log::error('TelegramService: Token not found');
            return [
                'success' => false,
                'error' => 'Bot token not found',
                'code' => 'no_token'
            ];
        }

        if (!$username) {
            Log::error('TelegramService: Username not provided');
            return [
                'success' => false,
                'error' => 'Username not provided',
                'code' => 'no_username'
            ];
        }

        // Нормализуем имя пользователя (удаляем @ если есть)
        $username = ltrim($username, '@');

        try {
            // Используем функционал для игнорирования ошибок SSL
            $chatResponse = Http::withoutVerifying()
                ->timeout(10)
                ->retry(3, 100)
                ->get("https://api.telegram.org/bot{$this->token}/getChat", [
                    'chat_id' => '@' . $username
                ]);

            // Проверяем содержимое ответа независимо от статуса HTTP
            $chatData = $chatResponse->json();

            // Если API вернул данные с ok=true, считаем запрос успешным
            if (isset($chatData['ok']) && $chatData['ok'] === true) {
                $chatId = $chatData['result']['id'];
                Log::info('Successfully got chat info for @' . $username, [
                    'chat_id' => $chatId,
                    'title' => $chatData['result']['title'] ?? 'Unknown'
                ]);
            } else {
                // API вернул ok=false
                $errorCode = $chatData['error_code'] ?? 400;
                $errorDescription = $chatData['description'] ?? 'Unknown error';

                Log::error('Failed to get chat info', [
                    'username' => $username,
                    'error_code' => $errorCode,
                    'description' => $errorDescription
                ]);

                return [
                    'success' => false,
                    'error' => "Failed to get chat info: {$errorDescription}",
                    'code' => (string)$errorCode,
                    'step' => 'getChat'
                ];
            }

            // Проверяем, является ли бот администратором
            $adminResponse = Http::withoutVerifying()
                ->timeout(10)
                ->retry(3, 100)
                ->get("https://api.telegram.org/bot{$this->token}/getChatAdministrators", [
                    'chat_id' => '@' . $username
                ]);

            // Аналогично, проверяем содержимое ответа
            $adminData = $adminResponse->json();

            if (!isset($adminData['ok']) || $adminData['ok'] !== true) {
                $errorCode = $adminData['error_code'] ?? 400;
                $errorDescription = $adminData['description'] ?? 'Unknown error';

                Log::error('Failed to get administrators', [
                    'username' => $username,
                    'error_code' => $errorCode,
                    'description' => $errorDescription
                ]);

                return [
                    'success' => false,
                    'error' => "Failed to get administrators: {$errorDescription}",
                    'code' => (string)$errorCode,
                    'step' => 'getChatAdministrators'
                ];
            }

            // Получаем информацию о боте
            $botInfo = Http::withoutVerifying()
                ->timeout(10)
                ->retry(3, 100)
                ->get("https://api.telegram.org/bot{$this->token}/getMe");

            $botData = $botInfo->json();

            if (!isset($botData['ok']) || $botData['ok'] !== true) {
                $errorCode = $botData['error_code'] ?? 400;
                $errorDescription = $botData['description'] ?? 'Unknown error';

                Log::error('Failed to get bot info', [
                    'response' => $botData,
                    'error_code' => $errorCode
                ]);

                return [
                    'success' => false,
                    'error' => "Failed to get bot info: {$errorDescription}",
                    'code' => (string)$errorCode,
                    'step' => 'getMe'
                ];
            }

            $botId = $botData['result']['id'];
            $botUsername = $botData['result']['username'];
            Log::info('Bot ID: ' . $botId . ', Bot username: @' . $botUsername);

            $admins = $adminData['result'];
            $adminUsernames = [];
            foreach ($admins as $admin) {
                if (isset($admin['user']['username'])) {
                    $adminUsernames[] = $admin['user']['username'];
                }
            }

            Log::info('Channel admins: ' . implode(', ', $adminUsernames));

            $isBotAdmin = false;
            foreach ($admins as $admin) {
                if ($admin['user']['id'] == $botId) {
                    $isBotAdmin = true;
                    break;
                }
            }

            if ($isBotAdmin) {
                Log::info('Bot is admin for @' . $username);
                return [
                    'success' => true,
                    'chat_id' => $chatId,
                    'is_admin' => true,
                    'bot_username' => $botUsername,
                    'channel_title' => $chatData['result']['title'] ?? $username
                ];
            } else {
                Log::warning('Bot is NOT admin for @' . $username);
                return [
                    'success' => true,
                    'chat_id' => $chatId,
                    'is_admin' => false,
                    'bot_username' => $botUsername,
                    'channel_title' => $chatData['result']['title'] ?? $username,
                    'channel_admins' => $adminUsernames
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception in checkBotAccess: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'username' => $username,
                'error_class' => get_class($e)
            ]);

            // Специальная обработка ошибок SSL
            if (strpos($e->getMessage(), 'SSL certificate problem') !== false) {
                Log::warning('SSL certificate error detected, trying to extract response data if available');

                // Попытка получить и использовать данные, несмотря на ошибку SSL
                try {
                    // Проверим, можем ли мы получить канал напрямую без проверки SSL
                    $chatResponse = Http::withoutVerifying()
                        ->get("https://api.telegram.org/bot{$this->token}/getChat", [
                            'chat_id' => '@' . $username
                        ]);

                    if ($chatResponse->successful() && $chatResponse->json('ok')) {
                        $chatId = $chatResponse->json('result.id');

                        return [
                            'success' => true,
                            'chat_id' => $chatId,
                            'is_admin' => false, // Предполагаем, что не админ, требуется проверка
                            'bot_username' => 'unknown_due_to_ssl_error',
                            'channel_title' => $chatResponse->json('result.title') ?? $username,
                            'ssl_warning' => 'Обнаружена проблема с SSL-сертификатом. Данные канала получены, но статус администратора не проверен.'
                        ];
                    }
                } catch (\Exception $innerEx) {
                    Log::error('Secondary exception trying to bypass SSL error: ' . $innerEx->getMessage());
                }
            }

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'code' => 'exception',
                'details' => $e->getTraceAsString()
            ];
        }
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

    public function getChannelStats($channelId)
    {
        try {
            // Получаем количество подписчиков
            $subscribersResponse = Http::withoutVerifying()
                ->get($this->apiUrl . $this->token . '/getChatMemberCount', [
                    'chat_id' => $channelId
                ]);

            $subscribers = 0;
            if ($subscribersResponse->successful()) {
                $subscribers = $subscribersResponse->json()['result'] ?? 0;
            }

            // Получаем статистику сообщений
            $messagesResponse = Http::withoutVerifying()
                ->get($this->apiUrl . $this->token . '/getMessages', [
                    'chat_id' => $channelId,
                    'limit' => 100 // последние 100 сообщений
                ]);

            $totalViews = 0;
            $messageStats = [];

            if ($messagesResponse->successful()) {
                $messages = $messagesResponse->json()['result'] ?? [];
                foreach ($messages as $message) {
                    if (isset($message['views'])) {
                        $totalViews += $message['views'];

                        // Собираем статистику по часам
                        $date = Carbon::createFromTimestamp($message['date']);
                        $hour = $date->format('H');
                        if (!isset($messageStats[$hour])) {
                            $messageStats[$hour] = [
                                'views' => 0,
                                'messages' => 0
                            ];
                        }
                        $messageStats[$hour]['views'] += $message['views'];
                        $messageStats[$hour]['messages']++;
                    }
                }
            }

            // Получаем дополнительную информацию о канале
            $channelInfo = Http::withoutVerifying()
                ->get($this->apiUrl . $this->token . '/getChat', [
                    'chat_id' => $channelId
                ]);

            $channelData = [];
            if ($channelInfo->successful()) {
                $channelData = $channelInfo->json()['result'] ?? [];
            }

            Log::info('Channel stats retrieved', [
                'channel_id' => $channelId,
                'subscribers' => $subscribers,
                'total_views' => $totalViews,
                'hourly_stats' => $messageStats
            ]);

            return [
                'success' => true,
                'subscribers' => $subscribers,
                'views' => $totalViews,
                'hourly_stats' => $messageStats,
                'channel_info' => $channelData
            ];
        } catch (\Exception $e) {
            Log::error('Telegram getChannelStats error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Возвращает токен бота
     * 
     * @return string Токен бота
     */
    public function getToken()
    {
        // Возвращаем токен из свойства класса
        if (empty($this->token)) {
            Log::error('Telegram bot token is empty');
        }
        return $this->token;
    }
    
    /**
     * Получает информацию о настройках токена (для дебага)
     * 
     * @return array Информация о токене и настройках
     */
    public function getTokenInfo()
    {
        try {
            $settings = json_decode(Storage::get('settings.json') ?? '{}', true);
            return [
                'success' => true,
                'token_exists' => !empty($this->token),
                'settings_exists' => Storage::exists('settings.json'),
                'settings_content' => $settings
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Отправляет группу медиа-файлов в Telegram
     *
     * @param string $chatId ID чата или канала
     * @param string $caption Подпись к медиа-группе
     * @param array $media Массив путей к медиа-файлам
     * @return array Результат отправки
     */
    private function sendMediaGroup($chatId, $caption, array $media)
    {
        \Log::info('Sending media group to Telegram', [
            'chat_id' => $chatId,
            'media_count' => count($media),
        ]);

        $token = $this->getToken();
        if (empty($token)) {
            \Log::error('Cannot send media group: Telegram bot token is empty');
            return [
                'success' => false,
                'error' => 'Telegram bot token is empty'
            ];
        }

        try {
            // Prepare media items array
            $mediaItems = [];
            $mediaCounter = 0;
            
            // First 10 media files only, as per Telegram API limitations
            foreach ($media as $mediaFile) {
                if ($mediaCounter >= 10) break;
                
                // Пропускаем пустые значения
                if (empty($mediaFile)) continue;
                
                $mediaUrl = Storage::disk('public')->url($mediaFile);
                
                // Определяем тип медиа
                $mediaType = 'photo'; // По умолчанию
                
                // Если это видео, устанавливаем соответствующий тип
                $extension = pathinfo($mediaFile, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['mp4', 'mov', 'avi', '3gp'])) {
                    $mediaType = 'video';
                }
                
                // Add to media items array
                $mediaItem = [
                    'type' => $mediaType,
                    'media' => $mediaUrl,
                ];
                
                // Add caption to the first media item only
                if ($mediaCounter === 0 && $caption) {
                    $mediaItem['caption'] = $caption;
                    $mediaItem['parse_mode'] = 'MarkdownV2';  // Использовать MarkdownV2 вместо Markdown
                }
                
                $mediaItems[] = $mediaItem;
                $mediaCounter++;
            }
            
            // If we have prepared media items, send them
            if (!empty($mediaItems)) {
                $url = "https://api.telegram.org/bot{$token}/sendMediaGroup";
                
                \Log::info('Sending media group to Telegram API', [
                    'url' => $url,
                    'chat_id' => $chatId,
                    'media_count' => count($mediaItems)
                ]);
                
                $response = Http::withoutVerifying()->timeout(30)->post($url, [
                    'chat_id' => $chatId,
                    'media' => $mediaItems
                ]);
                
                \Log::info('Telegram API response for media group', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
                
                if ($response->successful() && isset($response['ok']) && $response['ok']) {
                    return [
                        'success' => true,
                        'message_id' => $response['result'][0]['message_id'] ?? null
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'Failed to send media group: ' . ($response['description'] ?? 'Unknown error'),
                        'data' => $response->json()
                    ];
                }
            } else {
                \Log::warning('No valid media items to send');
                return [
                    'success' => false,
                    'error' => 'No valid media items to send'
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Exception in sendMediaGroup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Проверка доступности Telegram API и валидности токена
     *
     * @return array Результат проверки
     */
    public function testConnection()
    {
        try {
            $token = $this->getToken();
            if (empty($token)) {
                \Log::error('Test connection failed: Telegram bot token is empty');
                return [
                    'success' => false,
                    'error' => 'Telegram bot token is empty'
                ];
            }
            
            \Log::info('Testing Telegram API connection');
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->retry(3, 100) // Add retry functionality
                ->get("https://api.telegram.org/bot{$token}/getMe");
                
            \Log::info('Telegram API test response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
                
            if ($response->successful() && isset($response->json()['ok']) && $response->json()['ok'] === true) {
                $botInfo = $response->json()['result'];
                return [
                    'success' => true,
                    'bot_id' => $botInfo['id'] ?? null,
                    'bot_name' => $botInfo['first_name'] ?? null,
                    'bot_username' => $botInfo['username'] ?? null,
                    'message' => 'Successfully connected to Telegram API'
                ];
            }
            
            \Log::error('Telegram API test failed', [
                'status_code' => $response->status(),
                'error' => $response->json()['description'] ?? 'Unknown error'
            ]);
            
            return [
                'success' => false,
                'error' => $response->json()['description'] ?? 'Unknown error',
                'status_code' => $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Telegram test connection error', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
