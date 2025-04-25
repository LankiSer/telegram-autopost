<?php

namespace App\Console\Commands;

use App\Models\GigaChatCredential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateGigaChatConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gigachat:config {--disable-test-mode : Отключить тестовый режим GigaChat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет конфигурацию GigaChat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Обновить URL эндпоинтов для всех учетных данных GigaChat
            $this->updateEndpoints();
            
            // Если указана опция для отключения тестового режима
            if ($this->option('disable-test-mode')) {
                $this->disableTestMode();
            }
            
            // Очистка кэша приложения
            $this->info('Очистка кэша приложения...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            
            $this->info('Конфигурация GigaChat успешно обновлена!');
            
            return 0;
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении конфигурации GigaChat', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Обновляет URL эндпоинтов для всех учетных данных GigaChat
     */
    protected function updateEndpoints()
    {
        $this->info('Обновление URL эндпоинтов GigaChat...');
        
        // Новые URL из официальной документации
        $authUrl = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
        $apiUrl = 'https://gigachat.devices.sberbank.ru/api/v1/chat/completions';
        
        // Получаем все учетные данные
        $credentials = GigaChatCredential::all();
        
        if ($credentials->isEmpty()) {
            $this->warn('Учетные данные GigaChat не найдены в базе данных.');
            return;
        }
        
        foreach ($credentials as $credential) {
            $credential->auth_url = $authUrl;
            $credential->api_url = $apiUrl;
            $credential->save();
            
            $this->info("Обновлены эндпоинты для учетной записи ID: {$credential->id}");
        }
        
        $this->info('URL эндпоинты GigaChat успешно обновлены.');
    }
    
    /**
     * Отключает тестовый режим GigaChat в .env файле
     */
    protected function disableTestMode()
    {
        $this->info('Отключение тестового режима GigaChat...');
        
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->warn('.env файл не найден.');
            return;
        }
        
        $content = File::get($envPath);
        
        if (Str::contains($content, 'GIGACHAT_TEST_MODE=true')) {
            $content = Str::replace('GIGACHAT_TEST_MODE=true', 'GIGACHAT_TEST_MODE=false', $content);
            File::put($envPath, $content);
            $this->info('Тестовый режим GigaChat успешно отключен в .env файле.');
        } elseif (Str::contains($content, 'GIGACHAT_TEST_MODE=')) {
            $this->info('Тестовый режим GigaChat уже отключен в .env файле.');
        } else {
            // Добавляем параметр, если его нет
            $content .= PHP_EOL . "# Настройка тестового режима для GigaChat" . PHP_EOL;
            $content .= "GIGACHAT_TEST_MODE=false" . PHP_EOL;
            File::put($envPath, $content);
            $this->info('Добавлен параметр GIGACHAT_TEST_MODE=false в .env файл.');
        }
    }
}
