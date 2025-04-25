<?php

namespace App\Console\Commands;

use App\Models\GigaChatCredential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetGigaChatKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gigachat:set-key {key} {--user_id= : ID пользователя, для которого устанавливается ключ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Устанавливает ключ доступа к GigaChat API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->argument('key');
        $userId = $this->option('user_id');

        try {
            if (empty($key)) {
                $this->error('Ключ не может быть пустым');
                return 1;
            }

            // Настройки URL для GigaChat API (из официальной документации)
            $auth_url = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
            $api_url = 'https://gigachat.devices.sberbank.ru/api/v1/chat/completions';
            
            // ID клиента из документации
            $client_id = '139deec6-3604-445f-a169-09858454ad8a';
            // Authorization key (переданный параметр) используется как Basic auth
            $client_secret = $key;
            
            $this->info('Использую Client ID: ' . $client_id);

            // Ищем существующие учетные данные для пользователя или создаем новые
            $credential = null;
            if ($userId) {
                $credential = GigaChatCredential::where('user_id', $userId)->first();
            } else {
                $credential = GigaChatCredential::first();
            }

            if (!$credential) {
                $credential = new GigaChatCredential();
                if ($userId) {
                    $credential->user_id = $userId;
                }
            }

            // Обновляем учетные данные
            $credential->client_id = $client_id;
            $credential->client_secret = $client_secret;
            $credential->auth_url = $auth_url;
            $credential->api_url = $api_url;
            $credential->save();

            $this->info('Ключ GigaChat успешно установлен');
            
            // Проверяем подключение
            $this->info('Проверка подключения к GigaChat...');
            $service = new \App\Services\GigaChatService($credential);
            
            // Очищаем кэш токенов, чтобы обеспечить новый запрос
            \Illuminate\Support\Facades\Cache::forget('gigachat_token_' . ($credential->user_id ?? 'default'));
            \Illuminate\Support\Facades\Cache::forget('gigachat_test_connection');
            
            $result = $service->testConnection();
            
            if ($result) {
                $this->info('Успешное подключение к GigaChat API!');
                $this->info('Тестовый ответ: ' . substr($result, 0, 100) . '...');
            } else {
                $this->warn('Не удалось получить тестовый ответ от GigaChat.');
                $this->warn('Проверьте правильность ключа и настроек в логе ошибок.');
                
                // Проверяем, не включен ли тестовый режим
                if (env('GIGACHAT_TEST_MODE', false)) {
                    $this->warn('Внимание! Включен тестовый режим GigaChat. Отключите его для работы с реальным API.');
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            Log::error('Ошибка при установке ключа GigaChat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }
}
