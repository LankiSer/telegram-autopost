<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GigaChatCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class GigaChatSettingsController extends Controller
{
    /**
     * Показывает страницу настроек GigaChat
     */
    public function index()
    {
        $credential = GigaChatCredential::first();
        
        return view('admin.gigachat-settings', [
            'credential' => $credential
        ]);
    }
    
    /**
     * Сохраняет настройки GigaChat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'auth_url' => 'required|url',
            'api_url' => 'required|url'
        ]);
        
        try {
            // Обновляем или создаем учетные данные
            $credential = GigaChatCredential::first();
            
            if (!$credential) {
                $credential = new GigaChatCredential();
                $credential->user_id = auth()->id();
            }
            
            $credential->client_id = $validated['client_id'];
            $credential->client_secret = $validated['client_secret'];
            $credential->auth_url = $validated['auth_url'];
            $credential->api_url = $validated['api_url'];
            
            $credential->save();
            
            // Очищаем кеш приложения и кеш токенов
            Artisan::call('optimize:clear');
            $this->clearGigaChatTokenCache();
            
            return redirect()->route('admin.gigachat-settings.index')
                ->with('success', 'Настройки GigaChat успешно сохранены');
                
        } catch (\Exception $e) {
            Log::error('Ошибка при сохранении настроек GigaChat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()
                ->with('error', 'Произошла ошибка при сохранении настроек: ' . $e->getMessage());
        }
    }
    
    /**
     * Тестирует соединение с GigaChat API
     */
    public function testConnection()
    {
        try {
            $service = app(\App\Services\GigaChatService::class);
            $result = $service->testConnection();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Соединение с GigaChat API успешно установлено',
                    'data' => $result
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Не удалось подключиться к GigaChat API. Проверьте настройки.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка при тестировании соединения с GigaChat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Очищает кеш токенов GigaChat
     */
    private function clearGigaChatTokenCache()
    {
        try {
            // Очищаем кеш по паттерну
            $keys = \Illuminate\Support\Facades\Cache::get('gigachat_token_*');
            foreach ($keys as $key) {
                \Illuminate\Support\Facades\Cache::forget($key);
            }
            
            // Очищаем кеш тестового соединения
            \Illuminate\Support\Facades\Cache::forget('gigachat_test_connection');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Ошибка при очистке кеша токенов GigaChat', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
} 