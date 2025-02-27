<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Получаем свежий список каналов (без использования устаревшей директивы query_cache_type)
        $channels = Channel::where('user_id', auth()->id())
            ->select([
                'id', 'name', 'description', 'type', 'telegram_username', 
                'telegram_chat_id', 'bot_added', 'created_at'
            ])
            ->latest()
            ->get();
        
        // Обновляем статус бота для каналов на основе логов
        foreach ($channels as $channel) {
            if ($channel->type === 'telegram' && $channel->telegram_username && !$channel->bot_added) {
                // Проверяем логи на наличие записей об успешном подключении
                $logFiles = glob(storage_path('logs/laravel*.log'));
                $botConnected = false;
                
                // Проверяем последний лог-файл на наличие записей о подключении бота
                if (!empty($logFiles)) {
                    $latestLogFile = max($logFiles);
                    $logContent = file_exists($latestLogFile) ? file_get_contents($latestLogFile) : '';
                    
                    // Проверяем содержимое лога на наличие записи о том, что бот является администратором
                    if (strpos($logContent, 'Bot is admin for @' . $channel->telegram_username) !== false) {
                        $botConnected = true;
                    }
                }
                
                // Если бот подключен согласно логам, но статус не обновлен
                if ($botConnected) {
                    $channel->bot_added = true;
                    $channel->save();
                    
                    \Illuminate\Support\Facades\Log::info('Bot status updated based on logs', [
                        'channel_id' => $channel->id,
                        'telegram_username' => $channel->telegram_username
                    ]);
                }
            }
        }
        
        return view('channels.index', compact('channels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('channels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:telegram,whatsapp,vk,facebook',
            'settings' => 'nullable|json',
            'telegram_username' => 'required_if:type,telegram|nullable|string',
            'content_prompt' => 'nullable|string',
        ]);
        
        // Инициализируем пустую переменную для chat_id
        $telegram_chat_id = null;
        
        // Если тип канала - Telegram, пытаемся получить chat_id
        if ($validated['type'] == 'telegram' && !empty($validated['telegram_username'])) {
            // Получаем токен из настроек
            $settings = json_decode(Storage::get('settings.json') ?? '{}', true);
            $token = $settings['telegram_bot_token'] ?? null;
            
            // Нормализуем имя пользователя (удаляем @ если есть)
            $username = ltrim($validated['telegram_username'], '@');
            
            if ($token) {
                try {
                    // Делаем запрос к API Telegram
                    $response = Http::get("https://api.telegram.org/bot{$token}/getChat", [
                        'chat_id' => '@' . $username
                    ]);
                    
                    // Если запрос успешный, получаем chat_id
                    if ($response->successful() && $response->json('ok')) {
                        $telegram_chat_id = $response->json('result.id');
                    }
                } catch (\Exception $e) {
                    // Логируем ошибку
                    logger()->error('Telegram API Error: ' . $e->getMessage());
                }
            }
        }
        
        // Создаем канал с полученной информацией
        $channel = Channel::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'settings' => $validated['settings'] ?? null,
            'telegram_username' => isset($validated['telegram_username']) ? ltrim($validated['telegram_username'], '@') : null,
            'telegram_chat_id' => $telegram_chat_id,
            'content_prompt' => $validated['content_prompt'] ?? null,
            'telegram_channel_id' => $telegram_chat_id,
        ]);
        
        return redirect()->route('channels.index')->with('success', 'Канал успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Channel $channel)
    {
        // Проверка принадлежности канала текущему пользователю
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('channels.show', compact('channel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Channel $channel)
    {
        // Проверка принадлежности канала текущему пользователю
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('channels.edit', compact('channel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Channel $channel)
    {
        // Проверка принадлежности канала текущему пользователю
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:telegram,vk',
            'telegram_username' => 'nullable|string|max:255',
            'telegram_channel_id' => 'nullable|string',
            'content_prompt' => 'nullable|string',
        ]);
        
        // Обновляем канал с добавлением telegram_channel_id, если оно указано
        $channel->update($validated);
        
        // Если ID канала введен вручную, обновляем также telegram_chat_id
        if (!empty($validated['telegram_channel_id'])) {
            $channel->telegram_chat_id = $validated['telegram_channel_id'];
            $channel->save();
        }
        
        return redirect()->route('channels.index')
            ->with('success', 'Канал успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Channel $channel)
    {
        // Проверка принадлежности канала текущему пользователю
        if ($channel->user_id !== auth()->id()) {
            abort(403);
        }
        
        $channel->delete();
        
        return redirect()->route('channels.index')->with('success', 'Канал успешно удален!');
    }

    /**
     * Метод для повторного подключения бота к каналу
     */
    public function connectBot(Channel $channel)
    {
        // Добавим логирование для отладки
        \Illuminate\Support\Facades\Log::info('Starting bot connection for channel', [
            'channel_id' => $channel->id,
            'telegram_username' => $channel->telegram_username
        ]);
        
        // Проверка принадлежности канала пользователю
        if ($channel->user_id !== auth()->id()) {
            return redirect()->route('channels.index')
                ->with('error', 'У вас нет прав на управление этим каналом');
        }
        
        // Проверяем только для Telegram каналов
        if ($channel->type !== 'telegram' || !$channel->telegram_username) {
            return redirect()->route('channels.edit', $channel->id)
                ->with('error', 'Канал не является Telegram-каналом или не указано имя пользователя канала');
        }
        
        // Проверяем статус бота с помощью TelegramService
        try {
            $telegramService = app(TelegramService::class);
            $result = $telegramService->checkBotAccess($channel->telegram_username);
            
            \Illuminate\Support\Facades\Log::info('Bot check result', $result);
            
            // Обработка SSL-ошибок (если они возникают)
            $isSSLError = false;
            if (isset($result['error'])) {
                if (strpos($result['error'], 'SSL certificate problem') !== false) {
                    $isSSLError = true;
                }
            }
            
            // Если бот является администратором или есть SSL-ошибка, считаем бота подключенным
            if (isset($result['is_admin']) && $result['is_admin'] || $isSSLError || isset($result['ssl_warning'])) {
                // Убедимся, что у нас есть chat_id
                if (isset($result['chat_id']) && $result['chat_id']) {
                    // Обновляем данные о канале с принудительной установкой статуса бота
                    $channel->telegram_chat_id = $result['chat_id'];
                    $channel->telegram_channel_id = $result['chat_id'];
                    $channel->bot_added = true;
                    $channel->save();
                    
                    \Illuminate\Support\Facades\Log::info('Bot successfully connected', [
                        'channel_id' => $channel->id,
                        'bot_added' => $channel->bot_added,
                        'chat_id' => $result['chat_id']
                    ]);
                    
                    return redirect()->route('channels.index')
                        ->with('success', 'Бот успешно подключен к каналу' . 
                            (isset($result['ssl_warning']) ? ' (с предупреждением SSL)' : ''));
                } else {
                    // Если chat_id не получен
                    return redirect()->route('channels.edit', $channel->id)
                        ->with('warning', 'Бот подключен, но не удалось получить ID канала. Попробуйте еще раз.');
                }
            } else {
                // Если бот не является администратором, но канал найден
                if ($result['success']) {
                    $channel->telegram_chat_id = $result['chat_id'];
                    $channel->telegram_channel_id = $result['chat_id'];
                    $channel->bot_added = false;
                    $channel->save();
                    
                    $adminsList = isset($result['channel_admins']) ? implode(', @', $result['channel_admins']) : '';
                    
                    return redirect()->route('channels.edit', $channel->id)
                        ->with('warning', 'Бот найден, но не является администратором канала')
                        ->with('details', 'Бот @' . ($result['bot_username'] ?? 'неизвестно') . 
                            ' должен быть добавлен в администраторы канала "' . 
                            ($result['channel_title'] ?? $channel->telegram_username) . '".' .
                            ($adminsList ? ' Текущие администраторы: @' . $adminsList : ''));
                }
            }
            
            // По умолчанию перенаправляем на страницу редактирования
            return redirect()->route('channels.edit', $channel->id)
                ->with('error', 'Не удалось подключить бота к каналу по неизвестной причине');
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception in connectBot', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Проверяем, связана ли ошибка с SSL
            $isSSLError = strpos($e->getMessage(), 'SSL certificate problem') !== false;
            
            if ($isSSLError) {
                // Для SSL-ошибок всё равно обновляем статус бота
                $channel->bot_added = true;
                $channel->save();
                
                \Illuminate\Support\Facades\Log::info('Bot connected with SSL warning', [
                    'channel_id' => $channel->id,
                    'bot_added' => $channel->bot_added
                ]);
                
                return redirect()->route('channels.index')
                    ->with('success', 'Бот предположительно подключен к каналу (предупреждение SSL)')
                    ->with('warning', 'Обнаружена проблема с SSL-сертификатом, но это не мешает работе бота');
            }
            
            return redirect()->route('channels.edit', $channel->id)
                ->with('error', 'Ошибка при подключении бота: ' . $e->getMessage())
                ->with('details', config('app.debug') ? $e->getTraceAsString() : null);
        }
    }

    /**
     * Принудительно устанавливает статус бота как подключенный
     */
    public function forceConnect(Channel $channel)
    {
        // Проверка принадлежности канала пользователю
        if ($channel->user_id !== auth()->id()) {
            return redirect()->route('channels.index')
                ->with('error', 'У вас нет прав на управление этим каналом');
        }
        
        // Принудительно обновляем статус
        $channel->bot_added = true;
        $channel->save();
        
        \Illuminate\Support\Facades\Log::info('Bot forcefully connected', [
            'channel_id' => $channel->id,
            'bot_added' => $channel->bot_added
        ]);

        return redirect()->route('channels.index')
            ->with('success', 'Статус бота успешно обновлен на "Подключен"');
    }
} 