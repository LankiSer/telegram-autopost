<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        // Получение текущих настроек
        $settings = $this->getSettings();
        
        return view('admin.settings', compact('settings'));
    }
    
    public function update(Request $request)
    {
        // Валидация входящих данных
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'telegram_bot_token' => 'required|string',
            'debug_mode' => 'nullable',
        ]);
        
        // Обработка чекбокса
        $validated['debug_mode'] = $request->has('debug_mode') ? true : false;
        
        // Сохранение настроек
        $this->saveSettings($validated);
        
        return redirect()->route('admin.settings')->with('success', 'Настройки успешно сохранены');
    }
    
    private function getSettings()
    {
        // Проверяем существование файла настроек
        if (Storage::exists('settings.json')) {
            return json_decode(Storage::get('settings.json'), true);
        }
        
        // Возвращаем настройки по умолчанию
        return [
            'site_name' => 'Laravel',
            'admin_email' => 'admin@admin.com',
            'telegram_bot_token' => '',
            'debug_mode' => false,
        ];
    }
    
    private function saveSettings(array $settings)
    {
        Storage::put('settings.json', json_encode($settings, JSON_PRETTY_PRINT));
    }
} 