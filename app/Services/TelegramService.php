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
        $settings = json_decode(Storage::get('settings.json') ?? '{}', true);
        $this->token = $settings['telegram_bot_token'] ?? null;
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
        Log::info('TelegramService: Начало отправки сообщения', [
            'chat_id' => $chatId,
            'text_length' => strlen($text),
            'media_count' => is_array($media) ? count($media) : 0,
            'token_exists' => !empty($this->token)
        ]);

        if (!$this->token) {
            Log::error('TelegramService: Token not found');
            throw new \Exception('Telegram bot token not found');
        }

        if (!$chatId) {
            Log::error('TelegramService: Chat ID not provided');
            throw new \Exception('Chat ID not provided');
        }

        try {
            // Если есть медиа-файлы, отправляем их
            if ($media && count($media) > 0) {
                Log::info('TelegramService: Подготовка к отправке медиа', [
                    'media_paths' => $media
                ]);
                
                if (count($media) == 1) {
                    // Отправляем одно фото с текстом
                    $mediaPath = $media[0];
                    
                    // Проверка и нормализация пути к файлу
                    if (strpos($mediaPath, 'public/') === 0) {
                        // Если путь начинается с 'public/', удаляем префикс для Storage::disk('public')
                        $normalizedPath = str_replace('public/', '', $mediaPath);
                        Log::info('TelegramService: Нормализованный путь к медиа', [
                            'original' => $mediaPath, 
                            'normalized' => $normalizedPath
                        ]);
                        
                        if (Storage::disk('public')->exists($normalizedPath)) {
                            $fullPath = Storage::disk('public')->path($normalizedPath);
                            // Далее используем $fullPath
                        } else {
                            Log::error("TelegramService: Публичный файл не найден: {$normalizedPath}");
                            throw new \Exception("Media file not found in public storage: {$mediaPath}");
                        }
                    } else {
                        // Пробуем найти в корневом хранилище
                        if (Storage::exists($mediaPath)) {
                            $fullPath = Storage::path($mediaPath);
                        } else {
                            // Последняя попытка: проверить в публичном хранилище напрямую
                            if (Storage::disk('public')->exists($mediaPath)) {
                                $fullPath = Storage::disk('public')->path($mediaPath);
                            } else {
                                Log::error("TelegramService: Storage file not found: {$mediaPath}");
                                Log::error("Пробуем поискать в доступных путях...");
                                
                                // Проверяем разные варианты путей для отладки
                                $testPaths = [
                                    $mediaPath,
                                    'public/' . $mediaPath,
                                    'media/' . basename($mediaPath),
                                    'public/media/' . basename($mediaPath)
                                ];
                                
                                foreach ($testPaths as $testPath) {
                                    Log::info("Проверка пути: {$testPath} - " . 
                                              (Storage::exists($testPath) ? "НАЙДЕН" : "НЕ НАЙДЕН"));
                                }
                                
                                throw new \Exception("Media file not found in any storage location: {$mediaPath}");
                            }
                        }
                    }
                    
                    // Проверяем, что файл существует и доступен на диске
                    if (!file_exists($fullPath)) {
                        Log::error("TelegramService: Файл не найден по полному пути: {$fullPath}");
                        throw new \Exception("Media file not found at path: {$mediaPath}");
                    }
                    
                    Log::info("TelegramService: Отправка медиа-файла: {$fullPath}");
                    
                    // Отключаем проверку SSL-сертификата для разработки
                    $response = Http::withoutVerifying()
                        ->attach('photo', file_get_contents($fullPath), basename($fullPath))
                        ->post("{$this->apiUrl}{$this->token}/sendPhoto", [
                            'chat_id' => $chatId,
                            'caption' => $text,
                            'parse_mode' => 'HTML'
                        ]);
                    
                    return [
                        'success' => $response->successful(),
                        'data' => $response->json(),
                        'method' => 'sendPhoto'
                    ];
                } else {
                    // Отправляем медиа-группу
                    $mediaItems = [];
                    
                    // Подготавливаем массив медиа-файлов
                    foreach ($media as $index => $mediaPath) {
                        // Проверяем, существует ли файл
                        if (Storage::exists($mediaPath)) {
                            $fullPath = Storage::path($mediaPath);
                            
                            // Проверяем, что файл существует и доступен
                            if (!file_exists($fullPath)) {
                                Log::error("TelegramService: File not found at path: {$fullPath}");
                                continue; // Пропускаем этот файл
                            }
                            
                            // Определяем тип медиа по расширению
                            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                            $type = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) ? 'photo' : 'document';
                            
                            // Для первого элемента добавляем подпись
                            if ($index === 0) {
                                $mediaItems[] = [
                                    'type' => $type,
                                    'media' => 'attach://' . basename($fullPath),
                                    'caption' => $text,
                                    'parse_mode' => 'HTML'
                                ];
                            } else {
                                $mediaItems[] = [
                                    'type' => $type,
                                    'media' => 'attach://' . basename($fullPath)
                                ];
                            }
                            
                            // Сохраняем путь для последующего прикрепления
                            $attachments[basename($fullPath)] = file_get_contents($fullPath);
                        }
                    }
                    
                    // Если есть хотя бы один файл для отправки
                    if (count($mediaItems) > 0) {
                        $mediaRequest = Http::withoutVerifying();
                        
                        // Прикрепляем все файлы к запросу
                        foreach ($attachments as $name => $content) {
                            $mediaRequest->attach($name, $content);
                        }
                        
                        // Отправляем медиа-группу
                        $response = $mediaRequest->post("{$this->apiUrl}{$this->token}/sendMediaGroup", [
                            'chat_id' => $chatId,
                            'media' => json_encode($mediaItems)
                        ]);
                        
                        return [
                            'success' => $response->successful(),
                            'data' => $response->json(),
                            'method' => 'sendMediaGroup'
                        ];
                    }
                }
            }
            
            // Если нет медиа или они не были обработаны, отправляем просто текст
            $response = Http::withoutVerifying()->post("{$this->apiUrl}{$this->token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
<<<<<<< HEAD

=======
            
>>>>>>> 41ba59d9358b26e1195d37d74b2eab6f3e981777
            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'method' => 'sendMessage'
            ];
        } catch (\Exception $e) {
            Log::error('TelegramService error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'chat_id' => $chatId,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Пытаемся отправить сообщение без медиа, если была ошибка с файлами
            try {
                $response = Http::withoutVerifying()->post("{$this->apiUrl}{$this->token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text . "\n\n⚠️ *Примечание:* Не удалось отправить прикрепленные медиа-файлы из-за ошибки.",
                    'parse_mode' => 'MarkdownV2'
                ]);
                
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'method' => 'sendMessage',
                    'warning' => 'Media files were not sent due to an error: ' . $e->getMessage()
                ];
            } catch (\Exception $innerException) {
                Log::error('TelegramService fallback error: ' . $innerException->getMessage());
                
                return [
                    'success' => false,
                    'error' => $e->getMessage() . ' -> Fallback also failed: ' . $innerException->getMessage()
                ];
            }
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
} 