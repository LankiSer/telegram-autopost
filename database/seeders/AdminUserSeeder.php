<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Делаем администратором пользователя с email admin@admin.com
        $admin = User::where('email', 'admin@admin.com')->first();
        
        if ($admin) {
            $admin->update([
                'is_admin' => true,
                'telegram_bot_name' => 'Telegram AutoPost Bot',
                'telegram_bot_username' => 'telegram_autopost_bot',
                'telegram_bot_description' => 'Бот для автоматического постинга в Telegram каналы. Добавьте этого бота в свой канал и предоставьте ему права администратора для публикации.',
                'telegram_bot_link' => 'https://t.me/telegram_autopost_bot',
            ]);
            
            $this->command->info('Администратор успешно настроен: admin@admin.com');
        } else {
            $this->command->error('Пользователь admin@admin.com не найден!');
        }
    }
} 