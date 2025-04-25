<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateEnvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-env {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет значение переменной в файле .env';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->argument('key');
        $value = $this->argument('value');
        
        $this->info("Обновление .env файла: {$key}={$value}");
        
        try {
            // Путь к файлу .env
            $envPath = base_path('.env');
            
            if (!file_exists($envPath)) {
                $this->error("Файл .env не найден!");
                Log::error("Файл .env не найден при попытке обновления {$key}");
                return 1;
            }
            
            // Читаем содержимое файла
            $content = file_get_contents($envPath);
            
            // Проверяем, существует ли переменная
            if (preg_match("/^{$key}=.*/m", $content)) {
                // Переменная существует, обновляем значение
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
                
                $this->info("Переменная {$key} обновлена со значением {$value}");
            } else {
                // Переменная не существует, добавляем в конец файла
                $content .= PHP_EOL . "{$key}={$value}" . PHP_EOL;
                
                $this->info("Переменная {$key}={$value} добавлена в .env файл");
            }
            
            // Записываем обновленное содержимое обратно в файл
            file_put_contents($envPath, $content);
            
            // Очищаем кэш конфигурации
            $this->call('config:clear');
            
            $this->info("Файл .env успешно обновлен!");
            Log::info("Файл .env успешно обновлен: {$key}={$value}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Ошибка при обновлении файла .env: " . $e->getMessage());
            Log::error("Ошибка при обновлении файла .env", [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
} 