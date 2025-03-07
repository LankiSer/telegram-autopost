<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GigaChatService
{
    private string $baseUrl = 'https://gigachat.devices.sberbank.ru';
    private string $authUrl = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';

    public function __construct()
    {
        // Конструктор оставляем пустым, ключ будем брать напрямую из конфига
    }

    private function getAuthorizationKey(): string
    {
        // Формируем ключ в формате clientId:secretKey
        $clientId = '139deec6-3604-445f-a169-09858454ad8a';
        $secretKey = '3c2b7f42-6a9a-4fac-aa14-b30e85b3d03c';
        return base64_encode($clientId . ':' . $secretKey);
    }

    private function getAccessToken(): string
    {
        return Cache::remember('gigachat_token', 25 * 60, function () {
            try {
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'Authorization' => 'Basic ' . $this->getAuthorizationKey(),
                    'RqUID' => (string) \Str::uuid(),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ])->asForm()
                ->post($this->authUrl, [
                    'scope' => 'GIGACHAT_API_PERS'
                ]);

                \Log::info('GigaChat token request', [
                    'headers' => [
                        'Authorization' => 'Basic ' . $this->getAuthorizationKey(),
                        'RqUID' => (string) \Str::uuid(),
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Accept' => 'application/json'
                    ],
                    'body' => ['scope' => 'GIGACHAT_API_PERS']
                ]);

                \Log::info('GigaChat token response', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);

                if (!$response->successful() || !$response->json('access_token')) {
                    \Log::error('Failed to get GigaChat token', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    throw new \Exception('Failed to get access token from GigaChat: ' . $response->body());
                }

                return $response->json('access_token');
            } catch (\Exception $e) {
                \Log::error('Exception while getting GigaChat token', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    public function generatePost(string $prompt, array $previousTopics = []): string
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->baseUrl . '/api/v1/chat/completions', [
                'model' => 'GigaChat:latest',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a creative content writer for Telegram channel. Generate unique content avoiding these topics: ' . implode(', ', $previousTopics)
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to generate post: ' . $response->body());
            }

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            \Log::error('Error generating post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 