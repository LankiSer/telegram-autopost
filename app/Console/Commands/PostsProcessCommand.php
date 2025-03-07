<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PostsProcessCommand extends Command
{
    protected $signature = 'posts:process';
    protected $description = 'Process scheduled posts';

    public function handle()
    {
        $this->info('Starting posts processing...');
        \Log::info('Posts processing started');
        
        try {
            // Ваша логика обработки постов
            
            $this->info('Posts processing completed');
            \Log::info('Posts processing completed successfully');
            
        } catch (\Exception $e) {
            $this->error('Error processing posts: ' . $e->getMessage());
            \Log::error('Posts processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 