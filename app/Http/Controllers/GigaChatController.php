<?php

namespace App\Http\Controllers;

use App\Models\GigaChatCredential;
use App\Services\GigaChatService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GigaChatController extends Controller
{
    /**
     * Отображение формы с настройками GigaChat
     */
    public function index()
    {
        $credentials = Auth::user()->gigaChatCredential;
        
        return Inertia::render('Settings/GigaChat', [
            'credentials' => $credentials ? [
                'id' => $credentials->id,
                'client_id' => $credentials->client_id,
                'client_secret' => $credentials->client_secret,
                'auth_url' => $credentials->auth_url,
                'api_url' => $credentials->api_url,
                'created_at' => $credentials->created_at,
                'updated_at' => $credentials->updated_at,
            ] : null,
        ]);
    }

    /**
     * Обновление учетных данных GigaChat
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'auth_url' => 'required|url',
            'api_url' => 'required|url',
        ]);

        $user = Auth::user();
        $credentials = $user->gigaChatCredential;

        if ($credentials) {
            // Обновляем существующую запись
            $credentials->update([
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'],
                'auth_url' => $validated['auth_url'],
                'api_url' => $validated['api_url'],
            ]);
        } else {
            // Создаем новую запись
            $credentials = GigaChatCredential::create([
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'],
                'auth_url' => $validated['auth_url'],
                'api_url' => $validated['api_url'],
                'user_id' => $user->id,
            ]);
        }

        // Проверяем валидность учетных данных
        try {
            $service = new GigaChatService($credentials);
            $token = $service->getAccessToken(true); // Принудительно запрашиваем новый токен
            
            if ($token) {
                return redirect()->route('settings.gigachat')->with('success', 'Учетные данные GigaChat успешно обновлены и проверены');
            }
            
            return redirect()->route('settings.gigachat')->with('error', 'Учетные данные обновлены, но не удалось получить токен');
        } catch (Exception $e) {
            Log::error('Ошибка при проверке учетных данных GigaChat', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('settings.gigachat')->with('error', 'Учетные данные обновлены, но произошла ошибка при валидации: ' . $e->getMessage());
        }
    }

    /**
     * Тестирование работы GigaChat API
     */
    public function test()
    {
        Log::info('Начало тестирования GigaChat API');
        
        try {
            // Получаем учетные данные текущего пользователя
            $credentials = Auth::user()->gigaChatCredential;
            
            if (!$credentials) {
                Log::warning('Не настроены учетные данные GigaChat');
                return response()->json([
                    'success' => false,
                    'message' => 'Не настроены учетные данные GigaChat. Пожалуйста, добавьте учетные данные в настройках.'
                ], 404);
            }
            
            // Создаем экземпляр сервиса с учетными данными пользователя
            $service = new GigaChatService($credentials);
            
            // Проверяем получение токена
            $token = $service->getAccessToken(true);
            
            if (!$token) {
                Log::error('Не удалось получить токен доступа GigaChat');
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось авторизоваться в GigaChat API. Проверьте правильность учетных данных.'
                ], 400);
            }
            
            // Выполняем короткий тест генерации с измерением времени
            $startTime = microtime(true);
            
            // Короткий промпт для быстрой проверки
            $prompt = 'Напиши одно предложение о технологиях. Ответ должен быть коротким.';
            $content = $service->generateText($prompt, 0.5, 100);
            
            $duration = round(microtime(true) - $startTime, 2);
            
            if (!$content) {
                Log::error('Не удалось сгенерировать тестовый текст GigaChat');
                return response()->json([
                    'success' => false,
                    'message' => 'Авторизация успешна, но не удалось получить ответ от GigaChat API. Повторите попытку позже.',
                    'duration' => $duration
                ], 500);
            }
            
            Log::info('Тестирование GigaChat API успешно завершено', [
                'duration' => $duration,
                'content_length' => strlen($content)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Подключение к GigaChat API успешно',
                'sample_content' => $content,
                'duration' => $duration
            ]);
        } catch (Exception $e) {
            Log::error('Ошибка при тестировании GigaChat API', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при тестировании: ' . $e->getMessage()
            ], 500);
        }
    }
} 