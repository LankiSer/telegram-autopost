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
        // User::factory(10)->create();

        // Создаем админа
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);

        // Создаем обычного пользователя
        User::create([
            'name' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('user'),
        ]);

        $this->call([
            SubscriptionPlanSeeder::class,
        ]);
    }
}
