<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateGigaChatMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gigachat:update-mode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет режим работы GigaChat, удаляет устаревшие тестовые режимы';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало обновления режима работы GigaChat...');

        try {
            // Запуск миграции для удаления тестового режима
            $this->info('Запуск миграции для удаления тестового режима...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info('Миграция успешно выполнена.');

            // Очистка кеша токенов
            $this->info('Очистка кеша токенов GigaChat...');
            $keys = Cache::get('gigachat_token_*', []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            $this->info('Кеш токенов очищен.');

            // Очистка тестового соединения
            $this->info('Очистка кеша тестового соединения...');
            Cache::forget('gigachat_test_connection');
            $this->info('Кеш тестового соединения очищен.');

            // Обновление кеша приложения
            $this->info('Обновление кеша приложения...');
            Artisan::call('optimize:clear');
            $this->info('Кеш приложения обновлен.');

            $this->info('Удаление env-переменной GIGACHAT_TEST_MODE...');
            $this->updateDotEnv('GIGACHAT_TEST_MODE', null);
            $this->info('Переменная GIGACHAT_TEST_MODE удалена из .env.');

            $this->info('Обновление режима работы GigaChat успешно завершено!');
            $this->info('Теперь GigaChat работает только с реальными ключами API из админки.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Произошла ошибка при обновлении режима работы GigaChat:');
            $this->error($e->getMessage());
            Log::error('Ошибка при обновлении режима работы GigaChat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Обновляет значение переменной в .env файле или удаляет её
     *
     * @param string $key Ключ переменной
     * @param string|null $value Новое значение или null для удаления
     * @return bool Успешность операции
     */
    protected function updateDotEnv(string $key, ?string $value): bool
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return false;
        }

        $content = file_get_contents($path);

        if ($value === null) {
            // Удаляем строку с переменной
            $content = preg_replace("/^{$key}=.*$/m", '', $content);
            // Удаляем пустые строки
            $content = preg_replace("/^\n+/m", "\n", $content);
        } else {
            // Если переменная существует, обновляем её значение
            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                // Иначе добавляем новую переменную
                $content .= "\n{$key}={$value}\n";
            }
        }

        return file_put_contents($path, $content) !== false;
    }
} 