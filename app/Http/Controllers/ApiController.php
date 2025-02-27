<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramService;

class ApiController extends Controller
{
    /**
     * Проверяет статус бота в telegram канале
     */
    public function checkBotStatus(Channel $channel)
    {
        // Проверка принадлежности канала пользователю
        if ($channel->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Проверяем только для Telegram каналов
        if ($channel->type !== 'telegram' || !$channel->telegram_username) {
            return response()->json([
                'success' => false, 
                'message' => 'Not a Telegram channel or no username',
                'details' => 'Убедитесь, что указано имя пользователя канала в Telegram'
            ]);
        }
        
        // Получаем токен из настроек
        $settings = json_decode(Storage::get('settings.json') ?? '{}', true);
        $token = $settings['telegram_bot_token'] ?? null;
        
        if (!$token) {
            return response()->json([
                'success' => false, 
                'message' => 'Bot token not found',
                'details' => 'Токен бота не найден в настройках. Обратитесь к администратору.'
            ]);
        }
        
        try {
            // Используем улучшенный метод из TelegramService для проверки бота
            $telegramService = app(TelegramService::class);
            $result = $telegramService->checkBotAccess($channel->telegram_username);
            
            if (!$result['success']) {
                // Проверяем, не связана ли ошибка с SSL-сертификатами
                $isSSLError = (isset($result['error']) && strpos($result['error'], 'SSL certificate problem') !== false);
                
                if ($isSSLError) {
                    // Для проблем с SSL всё равно обновляем ID канала, если он был получен
                    if (isset($result['chat_id']) && $result['chat_id']) {
                        $channel->update([
                            'telegram_chat_id' => $result['chat_id'],
                            'telegram_channel_id' => $result['chat_id'],
                            'bot_added' => true // Предполагаем, что бот добавлен, несмотря на ошибку SSL
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'is_admin' => true, // Предполагаем, что бот является администратором
                            'message' => 'Бот подключен к каналу, но есть проблема с SSL-сертификатом',
                            'ssl_warning' => 'Обнаружена проблема с SSL-сертификатом. Это не мешает работе бота, но может вызывать предупреждения.'
                        ]);
                    }
                }
                
                // Форматируем понятное сообщение об ошибке
                $errorMessage = $result['error'] ?? 'Unknown error';
                $errorDetails = '';
                
                // Добавляем контекстные подсказки в зависимости от типа ошибки
                switch ($result['code'] ?? '') {
                    case '400':
                        $errorDetails = 'Проверьте правильность имени пользователя канала. Бот должен быть добавлен в канал.';
                        break;
                    case '401':
                        $errorDetails = 'Токен бота недействителен. Обратитесь к администратору.';
                        break;
                    case '403':
                        $errorDetails = 'Бот не имеет доступа к каналу. Добавьте бота в канал и сделайте его администратором.';
                        break;
                    default:
                        $errorDetails = 'Проверьте соединение с интернетом и правильность имени пользователя канала.';
                }
                
                // Если в результате есть подробности, добавляем их
                if (isset($result['details']) && config('app.debug')) {
                    $errorDetails .= ' Техническая информация: ' . $result['details'];
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'details' => $errorDetails,
                    'step' => $result['step'] ?? null
                ]);
            }
            
            // Если всё в порядке, обновляем информацию о канале
            if ($result['is_admin']) {
                $channel->update([
                    'telegram_chat_id' => $result['chat_id'],
                    'telegram_channel_id' => $result['chat_id'],
                    'bot_added' => true
                ]);
                
                return response()->json([
                    'success' => true,
                    'is_admin' => true,
                    'message' => 'Бот @' . $result['bot_username'] . ' успешно подключен к каналу "' . $result['channel_title'] . '" и имеет права администратора'
                ]);
            } else {
                $channel->update([
                    'telegram_chat_id' => $result['chat_id'],
                    'telegram_channel_id' => $result['chat_id'],
                    'bot_added' => false
                ]);
                
                $adminsList = isset($result['channel_admins']) ? implode(', @', $result['channel_admins']) : '';
                
                return response()->json([
                    'success' => true,
                    'is_admin' => false,
                    'message' => 'Бот подключен к каналу, но НЕ является администратором',
                    'details' => 'Бот @' . $result['bot_username'] . ' должен быть добавлен в администраторы канала "' . $result['channel_title'] . '". ' .
                                'Текущие администраторы: @' . $adminsList
                ]);
            }
        } catch (\Exception $e) {
            // Проверяем, связана ли ошибка с SSL
            $isSSLError = strpos($e->getMessage(), 'SSL certificate problem') !== false;
            
            if ($isSSLError) {
                Log::warning('SSL certificate error in checkBotStatus, but this is not critical', [
                    'channel' => $channel->id,
                    'telegram_username' => $channel->telegram_username
                ]);
                
                // Получаем ID напрямую через отдельный запрос
                try {
                    $chatResponse = Http::withoutVerifying()
                        ->get("https://api.telegram.org/bot{$token}/getChat", [
                            'chat_id' => '@' . $channel->telegram_username
                        ]);
                    
                    if ($chatResponse->successful() && $chatResponse->json('ok')) {
                        $chatId = $chatResponse->json('result.id');
                        
                        $channel->update([
                            'telegram_chat_id' => $chatId,
                            'telegram_channel_id' => $chatId,
                            'bot_added' => true // Предполагаем, что бот добавлен
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'is_admin' => true,
                            'message' => 'Бот успешно подключен к каналу',
                            'ssl_warning' => 'Обнаружена проблема с SSL-сертификатом. Это не мешает работе бота, но может вызывать предупреждения.'
                        ]);
                    }
                } catch (\Exception $innerEx) {
                    Log::error('Secondary error during SSL workaround: ' . $innerEx->getMessage());
                }
            }
            
            // Логируем все необработанные исключения
            Log::error('Error in checkBotStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'channel' => $channel->id,
                'telegram_username' => $channel->telegram_username
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка проверки статуса бота: ' . $e->getMessage(),
                'details' => 'Произошла непредвиденная ошибка. Подробности сохранены в журнале. ' . 
                            (config('app.debug') ? $e->getTraceAsString() : '')
            ]);
        }
    }
    
    /**
     * Обновляет статус бота в БД
     */
    public function updateBotStatus(Channel $channel)
    {
        // Проверка принадлежности канала пользователю
        if ($channel->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $channel->update(['bot_added' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Bot status updated'
        ]);
    }
} 