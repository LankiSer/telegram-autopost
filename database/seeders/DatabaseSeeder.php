<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin and test users
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'User',
                'password' => Hash::make('user'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            SubscriptionPlanSeeder::class,
            TestDataSeeder::class, // Add test data
            SubscriptionTestUserSeeder::class, // Add test user with subscription
            AdminUserSeeder::class, // Set admin permissions
        ]);
    }
}
