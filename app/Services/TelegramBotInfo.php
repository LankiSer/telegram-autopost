<?php

namespace App\Services;

use App\Models\User;

class TelegramBotInfo
{
    /**
     * Получить информацию о боте
     * 
     * @return array
     */
    public static function getBotInfo(): array
    {
        $admin = User::where('is_admin', true)->first();
        
        if (!$admin) {
            return [
                'name' => 'Telegram Bot',
                'username' => 'telegram_bot',
                'description' => 'Бот для автопостинга в Telegram каналы',
                'link' => 'https://t.me/telegram_bot',
                'is_configured' => false,
            ];
        }
        
        return [
            'name' => $admin->telegram_bot_name ?: 'Telegram Bot',
            'username' => $admin->telegram_bot_username ?: 'telegram_bot',
            'description' => $admin->telegram_bot_description ?: 'Бот для автопостинга в Telegram каналы',
            'link' => $admin->telegram_bot_link ?: 'https://t.me/telegram_bot',
            'is_configured' => !empty($admin->telegram_bot_token),
        ];
    }
    
    /**
     * Проверяет, настроен ли бот
     * 
     * @return bool
     */
    public static function isConfigured(): bool
    {
        $admin = User::where('is_admin', true)->first();
        return $admin && !empty($admin->telegram_bot_token);
    }
    
    /**
     * Получить имя бота
     * 
     * @return string
     */
    public static function getName(): string
    {
        $info = self::getBotInfo();
        return $info['name'];
    }
    
    /**
     * Получить username бота
     * 
     * @return string
     */
    public static function getUsername(): string
    {
        $info = self::getBotInfo();
        return $info['username'];
    }
    
    /**
     * Получить ссылку на бота
     * 
     * @return string
     */
    public static function getLink(): string
    {
        $info = self::getBotInfo();
        return $info['link'];
    }
} 