<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // Clear existing plans to avoid duplicates
        \DB::table('subscription_plans')->truncate();
        
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Бесплатный'],
            [
                'description' => 'Базовый план для начинающих пользователей',
                'price' => 0,
                'channel_limit' => 1,
                'posts_per_month' => 3,
                'scheduling_enabled' => false,
                'analytics_enabled' => false,
            ]
        );
        
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Стандартный'],
            [
                'description' => 'Оптимальный план для небольших проектов',
                'price' => 499,
                'channel_limit' => 3,
                'posts_per_month' => 30,
                'scheduling_enabled' => true,
                'analytics_enabled' => false,
            ]
        );
        
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Премиум'],
            [
                'description' => 'Расширенный план для профессионалов',
                'price' => 999,
                'channel_limit' => 10,
                'posts_per_month' => 100,
                'scheduling_enabled' => true,
                'analytics_enabled' => true,
            ]
        );
        
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Бизнес'],
            [
                'description' => 'Максимальные возможности для бизнеса',
                'price' => 2999,
                'channel_limit' => 30,
                'posts_per_month' => 500,
                'scheduling_enabled' => true,
                'analytics_enabled' => true,
            ]
        );
    }
} 