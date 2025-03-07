<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Date;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AutoPostingCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Установка часового пояса для планировщика
        Date::setTestNow(now()->setTimezone(config('app.timezone')));
        
        // Тестовая задача
        $schedule->call(function () {
            $now = now();
            $message = "Scheduler test at: " . $now->format('Y-m-d H:i:s');
            
            info($message);
            file_put_contents(
                storage_path('logs/scheduler-test.log'),
                $message . PHP_EOL,
                FILE_APPEND
            );
        })->everyMinute();
        
        // Автопостинг
        $schedule->command('auto-posting:run')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
} 