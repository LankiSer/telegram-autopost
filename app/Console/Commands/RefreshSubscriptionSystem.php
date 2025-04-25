<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshSubscriptionSystem extends Command
{
    protected $signature = 'subscriptions:refresh';
    protected $description = 'Refreshes the subscription system and creates test users with subscriptions';

    public function handle()
    {
        $this->info('Starting subscription system refresh...');
        
        // Refresh migrations for subscription tables
        $this->info('Rolling back and recreating subscription tables...');
        
        try {
            if ($this->confirm('This will reset subscription data. Continue?', true)) {
                // We'll be selective about which migrations to roll back
                $this->call('migrate:refresh', [
                    '--path' => 'database/migrations/2024_03_21_000001_create_subscription_plans_table.php',
                ]);
                
                $this->call('migrate:refresh', [
                    '--path' => 'database/migrations/2024_03_21_000002_create_subscriptions_table.php',
                ]);
                
                // Run the subscription seeders
                $this->info('Seeding subscription plans...');
                $this->call('db:seed', [
                    '--class' => 'Database\\Seeders\\SubscriptionPlanSeeder',
                ]);
                
                // Add test user with subscription
                $this->info('Creating test user with subscription...');
                $this->call('db:seed', [
                    '--class' => 'Database\\Seeders\\SubscriptionTestUserSeeder',
                ]);
                
                $this->info('Subscription system refresh completed successfully!');
                $this->info('Test user created with: test@subscriber.com / password');
            } else {
                $this->info('Operation cancelled.');
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
} 