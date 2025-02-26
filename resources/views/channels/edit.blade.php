<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Редактирование канала') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <a href="{{ route('channels.show', $channel->id) }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Вернуться к просмотру канала
                        </a>
                    </div>
                    
                    <form method="POST" action="{{ route('channels.update', $channel->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Название канала</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $channel->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Описание</label>
                            <textarea name="description" id="description" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $channel->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @if($channel->type == 'telegram')
                        <div>
                            <label for="telegram_username" class="block text-sm font-medium text-gray-700">Имя пользователя канала в Telegram</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">@</span>
                                <input type="text" name="telegram_username" id="telegram_username" value="{{ old('telegram_username', $channel->telegram_username) }}" 
                                    class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md border-gray-300 @error('telegram_username') border-red-500 @enderror"
                                    placeholder="channel_name">
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Укажите имя канала без символа @. Бот должен быть добавлен в канал в качестве администратора.</p>
                            @error('telegram_username')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                        
                        <div>
                            <label for="content_prompt" class="block text-sm font-medium text-gray-700">Промт для генерации контента</label>
                            <textarea name="content_prompt" id="content_prompt" rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content_prompt') border-red-500 @enderror">{{ old('content_prompt', $channel->content_prompt) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Опишите тематику канала и особенности контента для генерации нейросетью</p>
                            @error('content_prompt')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-4">
                            <label for="telegram_channel_id" class="block text-sm font-medium text-gray-700">ID канала Telegram (необязательно)</label>
                            <input type="text" name="telegram_channel_id" id="telegram_channel_id" value="{{ old('telegram_channel_id', $channel->telegram_channel_id) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Используйте это поле только если не работает автоматическое определение ID канала. ID канала обычно выглядит как отрицательное число, например -1001234567890.</p>
                        </div>
                        
                        <div class="mt-4 p-4 bg-gray-50 rounded-md">
                            <h3 class="text-lg font-medium text-gray-900">Отладка соединения</h3>
                            <p class="text-sm text-gray-500 mb-2">Если возникают проблемы с отправкой постов, используйте эти инструменты для проверки:</p>
                            
                            <form action="{{ route('channels.force-connect', $channel->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                    Принудительно подключить бота
                                </button>
                            </form>
                            
                            <a href="{{ route('channels.connect-bot', $channel->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm ml-2">
                                Переподключить бота
                            </a>
                            
                            <button id="testMessageBtn" type="button" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm ml-2">
                                Отправить тестовое сообщение
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <a href="{{ route('channels.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Отмена</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Проверка статуса бота при загрузке страницы для Telegram каналов
            const telegramUsername = document.getElementById('telegram_username');
            
            if (telegramUsername) {
                const checkBotStatus = async function() {
                    try {
                        const response = await fetch(`/api/check-bot-status/{{ $channel->id }}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            if (data.is_admin) {
                                // Бот является администратором
                                const statusDiv = document.createElement('div');
                                statusDiv.className = 'mt-2 text-green-600 text-sm';
                                statusDiv.innerHTML = '<svg class="inline mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Бот подключен к каналу и является администратором';
                                telegramUsername.parentNode.appendChild(statusDiv);
                                
                                // Обновляем статус в БД
                                fetch(`/api/update-bot-status/{{ $channel->id }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });
                            } else {
                                // Бот не является администратором
                                const statusDiv = document.createElement('div');
                                statusDiv.className = 'mt-2 text-yellow-600 text-sm';
                                statusDiv.innerHTML = '<svg class="inline mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Бот не является администратором канала. Добавьте бота в администраторы';
                                telegramUsername.parentNode.appendChild(statusDiv);
                            }
                        } else {
                            // Ошибка проверки статуса
                            const statusDiv = document.createElement('div');
                            statusDiv.className = 'mt-2 text-red-600 text-sm';
                            statusDiv.innerHTML = '<svg class="inline mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Ошибка проверки бота: ' + data.message;
                            telegramUsername.parentNode.appendChild(statusDiv);
                        }
                    } catch (error) {
                        console.error('Ошибка при проверке статуса бота:', error);
                    }
                };
                
                // Проверяем статус бота при загрузке страницы
                checkBotStatus();
                
                // Добавляем кнопку для ручной проверки
                const checkButton = document.createElement('button');
                checkButton.type = 'button';
                checkButton.className = 'mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
                checkButton.textContent = 'Проверить статус бота';
                checkButton.onclick = function() {
                    // Удаляем предыдущие статусы
                    const statusDivs = telegramUsername.parentNode.querySelectorAll('div.text-sm');
                    statusDivs.forEach(div => div.remove());
                    
                    checkBotStatus();
                };
                
                telegramUsername.parentNode.appendChild(checkButton);
            }

            // Обработчик для кнопки тестового сообщения
            const testBtn = document.getElementById('testMessageBtn');
            if (testBtn) {
                testBtn.addEventListener('click', async function() {
                    try {
                        const response = await fetch('/api/test-telegram-message/{{ $channel->id }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({})
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Тестовое сообщение успешно отправлено! Проверьте ваш канал.');
                        } else {
                            alert('Ошибка при отправке: ' + result.error);
                        }
                    } catch (error) {
                        alert('Произошла ошибка: ' + error.message);
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout> 