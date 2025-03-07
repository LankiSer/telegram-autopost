<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TelegramService
{
    protected $token;
    protected $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        // Сначала пробуем получить токен из конфига
        $this->token = config('services.telegram.bot_token');

        // Если токен не найден в конфиге, пробуем получить из settings.json
        if (empty($this->token)) {
            $settings = json_decode(Storage::get('settings.json') ?? '{}', true);
            $this->token = $settings['telegram_bot_token'] ?? null;
        }

        if (empty($this->token)) {
            \Log::error('Telegram bot token not found in config or settings.json');
        } else {
            \Log::info('Telegram bot token configured successfully');
        }
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
            if (empty($this->token)) {
                throw new \Exception('Telegram bot token not configured');
            }

            \Log::info('Attempting to send Telegram message', [
                'chat_id' => $chatId,
                'text_length' => strlen($text),
                'has_media' => !empty($media)
            ]);

            $response = Http::withoutVerifying()
                ->timeout(10)
                ->retry(3, 100)
                ->withToken($this->token)
                ->post("{$this->apiUrl}{$this->token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);

            \Log::info('Telegram API response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful() && $response->json('ok')) {
                \Log::info('Message sent successfully', [
                    'chat_id' => $chatId,
                    'message_id' => $response->json('result.message_id')
                ]);
                return ['success' => true];
            }

            \Log::error('Failed to send message', [
                'chat_id' => $chatId,
                'error' => $response->json('description')
            ]);

            return [
                'success' => false,
                'error' => $response->json('description') ?? 'Unknown error'
            ];
        } catch (\Exception $e) {
            \Log::error('Exception while sending message', [
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

    public function getToken()
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
}
