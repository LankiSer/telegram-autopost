<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetupTelegramWebhook extends Command
{
    protected $signature = 'telegram:setup-webhook';
    protected $description = 'Setup webhook for Telegram bot';

    public function handle(TelegramService $telegram)
    {
        $webhookUrl = config('services.telegram.webhook_url');
        
        if (!$webhookUrl) {
            $this->error('Webhook URL is not defined in config. Please set TELEGRAM_WEBHOOK_URL in .env');
            return 1;
        }
        
        $response = $telegram->setWebhook($webhookUrl);
        
        if ($response->successful() && $response->json()['ok']) {
            $this->info('Webhook has been set successfully!');
            return 0;
        } else {
            $this->error('Failed to set webhook: ' . ($response->json()['description'] ?? 'Unknown error'));
            return 1;
        }
    }
} 