<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SubscriptionTestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user with an active subscription
        $user = User::firstOrCreate(
            ['email' => 'test@subscriber.com'],
            [
                'name' => 'Test Subscriber',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Find the standard plan (or create one if it doesn't exist)
        $plan = SubscriptionPlan::where('name', 'Стандартный')->first();
        
        if (!$plan) {
            $plan = SubscriptionPlan::create([
                'name' => 'Стандартный',
                'description' => 'Оптимальный план для небольших проектов',
                'price' => 499,
                'channel_limit' => 3,
                'posts_per_month' => 30,
                'scheduling_enabled' => true,
                'analytics_enabled' => false,
            ]);
        }
        
        // Cancel any existing subscriptions
        Subscription::where('user_id', $user->id)->update([
            'status' => 'cancelled',
            'ends_at' => now()->subDay() // Make it end yesterday
        ]);
        
        // Create a new active subscription
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(), // One month subscription
            'status' => 'active'
        ]);
        
        $this->command->info('Test subscriber created with email: test@subscriber.com and password: password');
    }
} 