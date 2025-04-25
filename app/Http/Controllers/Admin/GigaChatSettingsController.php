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
        $testMode = env('GIGACHAT_TEST_MODE', false);
        
        return view('admin.gigachat-settings', [
            'credential' => $credential,
            'testMode' => $testMode
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
            'api_url' => 'required|url',
            'test_mode' => 'boolean'
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
            
            // Обновляем .env файл для тестового режима
            $this->updateTestModeInEnv($request->has('test_mode'));
            
            // Очищаем кеш приложения
            Artisan::call('optimize:clear');
            
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
     * Обновляет параметр GIGACHAT_TEST_MODE в .env файле
     */
    private function updateTestModeInEnv($enabled)
    {
        try {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);
            
            $value = $enabled ? 'true' : 'false';
            
            if (strpos($envContent, 'GIGACHAT_TEST_MODE=') !== false) {
                // Заменяем существующую строку
                $envContent = preg_replace(
                    '/GIGACHAT_TEST_MODE=.*/m',
                    "GIGACHAT_TEST_MODE={$value}",
                    $envContent
                );
            } else {
                // Добавляем новую строку
                $envContent .= PHP_EOL . "# Настройка тестового режима для GigaChat" . PHP_EOL;
                $envContent .= "GIGACHAT_TEST_MODE={$value}" . PHP_EOL;
            }
            
            file_put_contents($envPath, $envContent);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении .env файла', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
} 