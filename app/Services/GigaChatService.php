<?php

namespace App\Services;

use App\Models\GigaChatCredential;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Сервис для работы с GigaChat API
 * 
 * Для тестирования:
 * - Ключ авторизации: MTM5ZGVlYzYtMzYwNC00NDVmLWExNjktMDk4NTg0NTRhZDhhOjFhODM1YWI3LTI1ODItNGUxYS05YzRiLWZlNmQ2OTBhM2NlOQ==
 */
class GigaChatService
{
    protected $credential;
    protected $accessToken;
    protected $tokenExpiry;
    protected $timeout = 30; // Стандартный таймаут для API запросов
    protected $retryCount = 2; // Количество повторов при неудаче

    public function __construct(GigaChatCredential $credential = null)
    {
        $this->credential = $credential ?? GigaChatCredential::first();
    }

    /**
     * Аутентификация в GigaChat API
     * 
     * @return bool Успешность аутентификации
     */
    protected function authenticate()
    {
        // Используем хардкодный ключ авторизации
        $hardcodedAuthKey = 'MTM5ZGVlYzYtMzYwNC00NDVmLWExNjktMDk4NTg0NTRhZDhhOjFhODM1YWI3LTI1ODItNGUxYS05YzRiLWZlNmQ2OTBhM2NlOQ==';
        $defaultAuthUrl = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
        $defaultApiUrl = 'https://gigachat.devices.sberbank.ru/api/v1/chat/completions';
        
        // Если нет учетных данных или они неполные, используем хардкодный ключ
        if (!$this->credential) {
            Log::info('GigaChat: Используем хардкодный ключ авторизации');
            // Создаем временный объект учетных данных для работы
            $this->credential = new GigaChatCredential();
            $this->credential->auth_url = $defaultAuthUrl;
            $this->credential->api_url = $defaultApiUrl;
            $this->credential->client_id = 'hardcoded_id';
            $this->credential->client_secret = $hardcodedAuthKey;
        } else if (empty($this->credential->auth_url) || 
                  empty($this->credential->client_id) || 
                  empty($this->credential->client_secret)) {
            // Дополняем недостающие данные
            Log::info('GigaChat: Дополняем недостающие данные хардкодными значениями');
            if (empty($this->credential->auth_url)) $this->credential->auth_url = $defaultAuthUrl;
            if (empty($this->credential->api_url)) $this->credential->api_url = $defaultApiUrl;
            if (empty($this->credential->client_id)) $this->credential->client_id = 'hardcoded_id';
            if (empty($this->credential->client_secret)) $this->credential->client_secret = $hardcodedAuthKey;
        }

        try {
            // Кеширование токена для пользователя
            $cacheKey = 'gigachat_token_' . ($this->credential->user_id ?? 'default');
            
            // Проверяем кеш
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                $this->accessToken = $cachedData['token'];
                $this->tokenExpiry = $cachedData['expiry'];
                
                // Если токен всё ещё действителен, используем его
                if (now()->lt($this->tokenExpiry)) {
                    return true;
                }
            }
            
            Log::info('GigaChat: Запрос нового токена');
            
            // Используем Basic Auth согласно документации GigaChat
            // Authorization header = "Basic " + client_secret (Authorization key)
            $response = Http::withoutVerifying()
                ->timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                    'RqUID' => $this->generateRqUID(),
                    'Authorization' => 'Basic ' . $this->credential->client_secret,
                ])
                ->asForm()
                ->post($this->credential->auth_url, [
                    'scope' => 'GIGACHAT_API_PERS',
                ]);

            // Логируем детали запроса для отладки
            Log::debug('GigaChat: Детали запроса авторизации', [
                'auth_url' => $this->credential->auth_url,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . substr($this->credential->client_secret, 0, 10) . '...',
                ],
                'form_params' => ['scope' => 'GIGACHAT_API_PERS'],
                'status_code' => $response->status(),
                'response_preview' => substr($response->body(), 0, 500),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (empty($data['access_token'])) {
                    Log::error('GigaChat: В ответе отсутствует токен доступа', [
                        'response' => $data,
                    ]);
                    return false;
                }
                
                $this->accessToken = $data['access_token'];
                // Устанавливаем время жизни токена с запасом в 5 минут
                $expiresIn = ($data['expires_in'] ?? 3600) - 300;
                $this->tokenExpiry = now()->addSeconds($expiresIn);
                
                // Сохраняем токен в кеш
                Cache::put($cacheKey, [
                    'token' => $this->accessToken,
                    'expiry' => $this->tokenExpiry
                ], $this->tokenExpiry);
                
                Log::info('GigaChat: Успешная аутентификация, токен получен', [
                    'expires_in' => $expiresIn,
                    'expiry' => $this->tokenExpiry->toDateTimeString(),
                ]);
                
                return true;
            }

            Log::error('GigaChat: Ошибка аутентификации', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            return false;
        } catch (Exception $e) {
            Log::error('GigaChat: Исключение при аутентификации', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return false;
        }
    }

    /**
     * Получение токена доступа
     * 
     * @param bool $forceRefresh Принудительное обновление токена
     * @return string|null Токен доступа или null при ошибке
     */
    public function getAccessToken($forceRefresh = false)
    {
        // Если токена нет, или он просрочен, или требуется обновление
        if ($forceRefresh || !$this->accessToken || !$this->tokenExpiry || now()->isAfter($this->tokenExpiry)) {
            if (!$this->authenticate()) {
                return null;
            }
        }
        
        return $this->accessToken;
    }

    /**
     * Обновляет URL API и эндпоинты согласно официальной документации
     * 
     * @return void
     */
    public function updateApiUrls()
    {
        if (!$this->credential) {
            return;
        }
        
        // Обновляем URL в базе данных, чтобы они соответствовали официальной документации
        $this->credential->auth_url = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
        $this->credential->api_url = 'https://gigachat.devices.sberbank.ru/api/v1/chat/completions';
        $this->credential->save();
        
        Log::info('GigaChat: URL API обновлены', [
            'auth_url' => $this->credential->auth_url,
            'api_url' => $this->credential->api_url
        ]);
    }

    /**
     * Генерирует текст с помощью GigaChat API
     *
     * @param array $data Данные для генерации текста
     * @param string $prompt Промпт для генерации
     * @return string Сгенерированный текст
     */
    public function generateText(array $data, string $prompt): string
    {
        // Логируем запрос для диагностики
        Log::debug('GigaChat: Запрос на генерацию текста', [
            'data' => $data,
            'prompt_preview' => substr($prompt, 0, 100) . '...'
        ]);

        // Проверяем тестовый режим
        if ($this->isTestMode()) {
            Log::info('GigaChat: Работа в тестовом режиме, возвращаем заглушку');
            return $this->getMockPost($data);
        }

        try {
            // Проверяем наличие токена доступа
            if (!$this->authenticate()) {
                Log::error('GigaChat: Не удалось аутентифицироваться для генерации текста');
                return $this->getMockPost($data);
            }

            // Формируем сообщения для API
            $messages = $this->formatPrompt($prompt);

            // Выполняем запрос к API
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->credential->api_url, [
                    'model' => 'GigaChat',
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);

            // Логируем результаты запроса
            $status = $response->status();
            $responseBody = $response->json();
            
            Log::debug('GigaChat: Ответ API', [
                'status' => $status,
                'success' => $response->successful(),
                'body_preview' => is_array($responseBody) ? json_encode(array_slice($responseBody, 0, 2)) . '...' : 'не массив'
            ]);

            // Обрабатываем ответ
            if ($response->successful() && isset($responseBody['choices'][0]['message']['content'])) {
                $generatedText = $responseBody['choices'][0]['message']['content'];
                
                // Логируем сгенерированный текст
                Log::info('GigaChat: Успешно сгенерирован текст', [
                    'length' => strlen($generatedText),
                    'preview' => substr($generatedText, 0, 50) . '...'
                ]);
                
                return $generatedText;
            } else {
                // Логируем информацию об ошибке
                Log::error('GigaChat: Неожиданный формат ответа API', [
                    'status' => $status,
                    'response' => $responseBody,
                ]);
                
                // Возвращаем тестовый пост в случае ошибки
                return $this->getMockPost($data);
            }
        } catch (\Exception $e) {
            // Логируем ошибку
            Log::error('GigaChat: Исключение при генерации текста', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Возвращаем тестовый пост в случае исключения
            return $this->getMockPost($data);
        }
    }

    /**
     * Генерирует пост для канала
     *
     * @param array $data Данные для генерации поста
     * @return string Сгенерированный пост
     */
    public function generatePost(array $data): string
    {
        Log::info('GigaChat: Начало генерации поста', [
            'data' => $data
        ]);

        try {
            // Проверяем наличие учетных данных
            if (!$this->hasCredentials()) {
                throw new \Exception('GigaChat: Отсутствуют учетные данные');
            }

            // Аутентификация
            if (!$this->authenticate()) {
                throw new \Exception('GigaChat: Ошибка аутентификации');
            }

            // Вызов метода генерации текста
            $result = $this->generateText($data, $this->buildPrompt($data));
            
            Log::info('GigaChat: Завершение генерации поста', [
                'success' => true,
                'length' => strlen($result)
            ]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('GigaChat: Ошибка при генерации поста', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // В случае ошибки возвращаем тестовый пост
            return $this->getMockPost($data);
        }
    }
    
    /**
     * Построение промпта на основе данных
     *
     * @param array $data Данные для построения промпта
     * @return string Сформированный промпт
     */
    private function buildPrompt(array $data): string
    {
        $topic = $data['topic'] ?? 'общая тема';
        $channelName = $data['channel_name'] ?? 'Канал';
        $channelDescription = $data['channel_description'] ?? 'Информационный канал';
        $additionalInfo = $data['additional_info'] ?? '';
        
        $prompt = "Создай интересный и информативный пост для Telegram канала '$channelName'.\n\n";
        $prompt .= "Тематика канала: $channelDescription\n";
        $prompt .= "Тема поста: $topic\n";
        
        if (!empty($additionalInfo)) {
            $prompt .= "\nДополнительные инструкции: $additionalInfo\n";
        }
        
        $prompt .= "\nИспользуй параграфы, эмодзи и форматирование для лучшей читаемости.";
        
        Log::debug('GigaChat: Построен промпт', [
            'prompt_preview' => substr($prompt, 0, 200) . '...'
        ]);
        
        return $prompt;
    }
    
    /**
     * Форматирует данные в промпт для GigaChat API
     *
     * @param string $prompt Текстовый промпт
     * @return array Форматированный промпт для API
     */
    private function formatPrompt(string $prompt): array
    {
        // Формируем системный промпт
        $systemPrompt = "Ты - помощник для создания постов в Telegram. Твоя задача - создать интересный, информативный пост. ";
        $systemPrompt .= "Пост должен быть написан на русском языке, быть грамотным, информативным и подходить для аудитории канала. ";
        $systemPrompt .= "Используй параграфы и эмодзи для лучшей читаемости. ";
        $systemPrompt .= "Пиши текст разговорным языком, избегай формальностей и канцеляризмов. ";
        
        // Формируем структуру сообщений для API
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        
        Log::debug('GigaChat: Сформирован промпт для API', [
            'system_prompt' => $systemPrompt,
            'user_prompt_preview' => substr($prompt, 0, 100) . '...'
        ]);
        
        return $messages;
    }

    /**
     * Генерация случайного идентификатора запроса
     * 
     * @return string UUID v4
     */
    protected function generateRqUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Получает список доступных моделей GigaChat
     * 
     * @return array|null Массив доступных моделей или null при ошибке
     */
    public function getModels()
    {
        // Получаем токен доступа
        $token = $this->getAccessToken();
        if (!$token) {
            Log::error('GigaChat: Не удалось получить токен для запроса моделей');
            return null;
        }

        try {
            $modelsUrl = 'https://gigachat.devices.sberbank.ru/api/v1/models';
            
            Log::info('GigaChat: Запрос списка моделей');
            
            $response = Http::withoutVerifying()
                ->timeout($this->timeout)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get($modelsUrl);
                
            Log::debug('GigaChat: Ответ на запрос моделей', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('GigaChat: Ошибка при получении списка моделей', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('GigaChat: Исключение при получении списка моделей', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Test connection with a simple request to get models list
     * 
     * @return string|null Test response or null on failure
     */
    public function testConnection()
    {
        // Используем короткий кэшированный ответ для тестирования
        $testKey = 'gigachat_test_connection';
        
        if (Cache::has($testKey)) {
            return Cache::get($testKey);
        }
        
        // Проверяем настройки GigaChat - сначала пробуем получить список моделей
        $models = $this->getModels();
        
        if ($models && isset($models['data']) && !empty($models['data'])) {
            // Успешно получили данные - кэшируем и возвращаем информацию о моделях
            $result = "Доступны следующие модели GigaChat:\n";
            foreach ($models['data'] as $model) {
                $result .= "- " . ($model['id'] ?? 'Неизвестная модель') . "\n";
            }
            
            // Кэшируем тестовый ответ на 1 час
            Cache::put($testKey, $result, now()->addHour());
            return $result;
        }
        
        // Если получить модели не удалось, пробуем очень короткий prompt для быстрого ответа
        $prompt = "Напиши одно предложение о технологиях.";
        $result = $this->generateText($prompt);
        
        if ($result) {
            // Кэшируем тестовый ответ на 1 час
            Cache::put($testKey, $result, now()->addHour());
            return $result;
        }
        
        return null;
    }

    /**
     * Проверка наличия учетных данных
     * 
     * @return bool Есть ли корректные учетные данные
     */
    public function hasCredentials()
    {
        return $this->credential && 
               !empty($this->credential->auth_url) && 
               !empty($this->credential->api_url) && 
               !empty($this->credential->client_id) && 
               !empty($this->credential->client_secret);
    }

    /**
     * Возвращает тестовый пост для тестового режима
     *
     * @param array $data Данные для генерации поста
     * @return string Тестовый пост
     */
    private function getMockPost(array $data): string
    {
        $topic = $data['topic'] ?? 'технологии';
        $channelName = $data['channel_name'] ?? 'Тестовый канал';
        
        $tags = ['#test', '#demo', '#автопост'];
        
        $emoji = ['🚀', '🔥', '⚡️', '🌟', '📱', '💻', '📊', '🎯', '🎮', '🧠'];
        $randomEmoji = $emoji[array_rand($emoji)];
        
        $currentDate = now()->format('d.m.Y');
        
        $intro = [
            "Всем привет! $randomEmoji",
            "Доброго дня подписчики! $randomEmoji",
            "Интересная новость! $randomEmoji",
            "Обратите внимание $randomEmoji"
        ];
        
        $body = [
            "Сегодня поговорим о теме \"$topic\". Это тестовый пост, сгенерированный в режиме разработки.",
            "В нашем канале \"$channelName\" мы обсуждаем \"$topic\". Это тестовый пост для отладки.",
            "Тема \"$topic\" очень актуальна для подписчиков \"$channelName\". Это тестовый контент."
        ];
        
        $conclusion = [
            "Оставайтесь с нами для получения новых публикаций!",
            "Подписывайтесь и следите за обновлениями!",
            "Не забудьте поделиться с друзьями!"
        ];
        
        $post = $intro[array_rand($intro)] . "\n\n";
        $post .= $body[array_rand($body)] . "\n\n";
        $post .= "Дата: $currentDate\n\n";
        $post .= $conclusion[array_rand($conclusion)] . "\n\n";
        $post .= implode(' ', $tags);
        
        return $post;
    }

    /**
     * Проверяет, включен ли тестовый режим
     *
     * @return bool
     */
    private function isTestMode(): bool
    {
        // Проверяем сначала конфигурацию, затем переменные окружения
        $testMode = config('services.gigachat.test_mode', false);
        
        // Если не задано в конфигурации, проверяем переменную окружения
        if (!$testMode) {
            $testMode = env('GIGACHAT_TEST_MODE', false);
        }
        
        // Преобразуем в булево значение (на случай строковых значений)
        return filter_var($testMode, FILTER_VALIDATE_BOOLEAN);
    }
} 