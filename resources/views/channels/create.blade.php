<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Создание нового канала') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('channels.store') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Название канала</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Описание (необязательно)</label>
                            <textarea name="description" id="description" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Тип канала</label>
                            <select name="type" id="type" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror"
                                onchange="toggleTelegramFields()">
                                <option value="telegram" {{ old('type') == 'telegram' ? 'selected' : '' }}>Telegram</option>
                                <option value="whatsapp" {{ old('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="vk" {{ old('type') == 'vk' ? 'selected' : '' }}>ВКонтакте</option>
                                <option value="facebook" {{ old('type') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div id="telegram_fields" class="space-y-4">
                            <div>
                                <label for="telegram_username" class="block text-sm font-medium text-gray-700">Имя пользователя канала в Telegram</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">@</span>
                                    <input type="text" name="telegram_username" id="telegram_username" value="{{ old('telegram_username') }}" 
                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md border-gray-300 @error('telegram_username') border-red-500 @enderror"
                                        placeholder="channel_name">
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Укажите имя канала без символа @. Бот должен быть добавлен в канал в качестве администратора.</p>
                                <p class="text-sm text-blue-600 mt-1">
                                    <a href="https://t.me/AutomaticPostingBotbot" target="_blank">Добавить бота в канал</a>
                                </p>
                                @error('telegram_username')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="content_prompt" class="block text-sm font-medium text-gray-700">Промт для генерации контента (необязательно)</label>
                            <textarea name="content_prompt" id="content_prompt" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content_prompt') border-red-500 @enderror">{{ old('content_prompt') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Опишите тематику канала и особенности контента для генерации нейросетью</p>
                            @error('content_prompt')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="settings" class="block text-sm font-medium text-gray-700">Настройки (JSON, необязательно)</label>
                            <textarea name="settings" id="settings" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('settings') border-red-500 @enderror">{{ old('settings') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Формат JSON: {"ключ": "значение"}</p>
                            @error('settings')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end">
                            <a href="{{ route('channels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2">
                                Отмена
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Создать канал
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleTelegramFields() {
            const type = document.getElementById('type').value;
            const telegramFields = document.getElementById('telegram_fields');
            
            if (type === 'telegram') {
                telegramFields.style.display = 'block';
            } else {
                telegramFields.style.display = 'none';
            }
        }
        
        // Вызываем функцию при загрузке страницы
        document.addEventListener('DOMContentLoaded', toggleTelegramFields);
    </script>
</x-app-layout> 