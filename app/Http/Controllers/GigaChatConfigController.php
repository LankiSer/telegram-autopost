<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GigaChatConfigController extends Controller
{
    /**
     * Проверяет, включен ли тестовый режим GigaChat
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTestMode()
    {
        $testModeEnabled = env('GIGACHAT_TEST_MODE', false);
        
        Log::info('GigaChat: Проверка состояния тестового режима', [
            'enabled' => $testModeEnabled ? 'да' : 'нет'
        ]);
        
        return response()->json([
            'enabled' => (bool) $testModeEnabled
        ]);
    }
    
    /**
     * Включает тестовый режим GigaChat (только для админов)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enableTestMode(Request $request)
    {
        // Проверяем, является ли пользователь администратором
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }
        
        try {
            // Обновляем файл .env через консольную команду
            $exitCode = \Artisan::call('app:update-env', [
                'key' => 'GIGACHAT_TEST_MODE',
                'value' => 'true'
            ]);
            
            if ($exitCode !== 0) {
                throw new \Exception('Ошибка выполнения команды обновления .env файла');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Тестовый режим GigaChat успешно включен'
            ]);
        } catch (\Exception $e) {
            Log::error('GigaChat: Ошибка при включении тестового режима', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось включить тестовый режим: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Выключает тестовый режим GigaChat (только для админов)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function disableTestMode(Request $request)
    {
        // Проверяем, является ли пользователь администратором
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }
        
        try {
            // Обновляем файл .env через консольную команду
            $exitCode = \Artisan::call('app:update-env', [
                'key' => 'GIGACHAT_TEST_MODE',
                'value' => 'false'
            ]);
            
            if ($exitCode !== 0) {
                throw new \Exception('Ошибка выполнения команды обновления .env файла');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Тестовый режим GigaChat успешно выключен'
            ]);
        } catch (\Exception $e) {
            Log::error('GigaChat: Ошибка при выключении тестового режима', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось выключить тестовый режим: ' . $e->getMessage()
            ], 500);
        }
    }
} 