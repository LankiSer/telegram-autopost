<?php

namespace App\Console\Commands;

use App\Models\GigaChatCredential;
use App\Models\User;
use App\Services\GigaChatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GigaChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gigachat:generate 
                            {prompt? : The prompt to generate content} 
                            {--user_id= : User ID to use for credentials}
                            {--save : Save generated content to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate content using GigaChat API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting GigaChat content generation...');
        
        try {
            // Get user if specified or find a user with GigaChat credentials
            $userId = $this->option('user_id');
            $credentials = null;
            
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $credentials = $user->gigaChatCredential;
                }
            } else {
                // Try to find any credentials in the system
                $credentials = GigaChatCredential::first();
            }
            
            if (!$credentials) {
                $this->error('GigaChat credentials not found. Please set up credentials first or specify a user with --user_id.');
                return 1;
            }
            
            // Initialize service with credentials
            $service = new GigaChatService($credentials);
            
            // Get prompt from argument or ask user
            $prompt = $this->argument('prompt') ?? $this->ask('Enter prompt for content generation');
            
            // Generate content
            $content = $service->generatePost($prompt);
            
            if (!$content) {
                $this->error('Failed to generate content');
                return 1;
            }
            
            $this->info('Content generated successfully:');
            $this->line('-------------------------------------');
            $this->line($content);
            $this->line('-------------------------------------');
            
            // Save content if requested
            if ($this->option('save')) {
                // Implementation for saving to posts or other tables would go here
                $this->info('Content saving functionality to be implemented');
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::channel('scheduler')->error('GigaChat command error: ' . $e->getMessage());
            return 1;
        }
    }
} 