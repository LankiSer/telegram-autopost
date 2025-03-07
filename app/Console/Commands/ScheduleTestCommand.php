<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleTestCommand extends Command
{
    protected $signature = 'schedule:test {--message= : Test message}';
    protected $description = 'Test command for scheduler';

    public function handle()
    {
        $message = $this->option('message') ?: 'Test at ' . now()->format('Y-m-d H:i:s');
        
        // Логируем в разные места
        info($message);
        
        // Записываем в файл
        $logFile = storage_path('logs/schedule.log');
        file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
        
        // Показываем в консоли
        $this->info($message);
        
        return Command::SUCCESS;
    }
} 