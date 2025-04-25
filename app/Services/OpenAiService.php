<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    protected $apiKey;
    protected $model = 'gpt-4o'; // По умолчанию используем новейшую модель GPT-4o
    protected $baseUrl = 'https://api.openai.com/v1';
    
    /**
     * Создает экземпляр сервиса OpenAI с API-ключом администратора
     */
    public function __construct()
    {
        // Получаем ключ API от администратора
        $admin = User::where('is_admin', true)->first();
        
        if ($admin && !empty($admin->openai_api_key)) {
            $this->apiKey = $admin->openai_api_key;
        }
    }
    
    /**
     * Устанавливает модель GPT
     * 
     * @param string $model
     * @return $this
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }
    
    /**
     * Проверяет, настроен ли API-ключ
     * 
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
    
    /**
     * Генерирует текст поста на основе промпта
     * 
     * @param string $prompt
     * @param int $maxTokens
     * @return array
     */
    public function generatePost(string $prompt, int $maxTokens = 500): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'API ключ OpenAI не настроен',
                'content' => null
            ];
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты - помощник для создания постов в Telegram. Твоя задача - создать интересный, информативный и лаконичный пост на основе заданной темы.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
            ]);
            
            $result = $response->json();
            
            if ($response->successful() && isset($result['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'message' => 'Пост успешно сгенерирован',
                    'content' => $result['choices'][0]['message']['content']
                ];
            }
            
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'response' => $result,
            ]);
            
            return [
                'success' => false,
                'message' => isset($result['error']['message']) ? $result['error']['message'] : 'Ошибка API OpenAI',
                'content' => null
            ];
            
        } catch (\Exception $e) {
            Log::error('Exception in OpenAI service', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Ошибка при обращении к API OpenAI: ' . $e->getMessage(),
                'content' => null
            ];
        }
    }
} 