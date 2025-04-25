<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\TelegramService;

class SetTelegramToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-token {token} {--admin_id= : ID администратора}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Устанавливает токен Telegram бота';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->argument('token');
        $adminId = $this->option('admin_id');

        try {
            if (empty($token)) {
                $this->error('Токен не может быть пустым');
                return 1;
            }

            // Проверяем валидность токена - он должен содержать один символ ':'
            if (substr_count($token, ':') !== 1) {
                $this->error('Неверный формат токена Telegram');
                return 1;
            }

            // Сначала сохраняем в настройках администратора
            $admin = null;
            if ($adminId) {
                $admin = User::where('id', $adminId)->where('is_admin', true)->first();
            } else {
                $admin = User::where('is_admin', true)->first();
            }

            if ($admin) {
                $admin->telegram_bot_token = $token;
                $admin->save();
                
                $this->info('Токен бота сохранен в настройках администратора');
            } else {
                $this->warn('Администратор не найден, токен сохранен только в settings.json');
            }

            // Также сохраняем в settings.json для совместимости
            $settings = [];
            if (Storage::exists('settings.json')) {
                $settings = json_decode(Storage::get('settings.json'), true) ?: [];
            }
            
            $settings['telegram_bot_token'] = $token;
            Storage::put('settings.json', json_encode($settings, JSON_PRETTY_PRINT));
            
            $this->info('Токен Telegram бота успешно установлен');
            
            // Проверяем подключение
            $this->info('Проверка подключения к Telegram API...');
            $telegramService = new TelegramService();
            
            // Явно устанавливаем токен в сервисе
            $telegramReflection = new \ReflectionClass($telegramService);
            $tokenProperty = $telegramReflection->getProperty('token');
            $tokenProperty->setAccessible(true);
            $tokenProperty->setValue($telegramService, $token);
            
            $result = $telegramService->testConnection();
            
            if ($result['success']) {
                $this->info('Успешное подключение к Telegram API!');
                $this->info('Информация о боте:');
                $this->info('ID: ' . $result['bot_id']);
                $this->info('Имя: ' . $result['bot_name']);
                $this->info('Пользователь: @' . $result['bot_username']);
            } else {
                $this->warn('Не удалось подключиться к Telegram API');
                $this->warn('Ошибка: ' . ($result['error'] ?? 'Неизвестная ошибка'));
            }
            
            return 0;
        } catch (\Exception $e) {
            Log::error('Ошибка при установке токена Telegram', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }
    }
}
