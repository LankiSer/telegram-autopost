<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check for expired subscriptions and update their status';

    public function handle()
    {
        // Находим активные подписки с истекшей датой окончания
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->get();
            
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            $this->info("Subscription ID: {$subscription->id} for user {$subscription->user_id} marked as expired.");
        }
        
        $this->info("Expired subscriptions check completed. {$expiredSubscriptions->count()} subscriptions processed.");
    }
} 