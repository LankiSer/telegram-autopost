<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Процесс запланированных постов каждую минуту
        $schedule->command('posts:process')->everyMinute();
        
        // Проверка статуса подписок и установка истекших как expired
        $schedule->command('subscriptions:check-expired')->daily();
        
        // Отправка запланированных постов в Telegram
        $schedule->command('telegram:send-scheduled-posts')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 