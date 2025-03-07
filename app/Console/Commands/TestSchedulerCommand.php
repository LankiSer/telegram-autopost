<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestSchedulerCommand extends Command
{
    protected $signature = 'scheduler:test';
    protected $description = 'Test scheduler is working';

    public function handle()
    {
        $message = 'Scheduler test run at: ' . now()->format('Y-m-d H:i:s');
        
        \Log::info($message);
        $this->info($message);
        
        file_put_contents(
            storage_path('logs/scheduler-test.log'),
            $message . PHP_EOL,
            FILE_APPEND
        );
    }
} 