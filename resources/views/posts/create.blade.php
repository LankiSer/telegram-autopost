<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Создание нового поста') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Вернуться к списку постов
                        </a>
                    </div>
                    
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="channel_id" class="block text-sm font-medium text-gray-700">Канал</label>
                            <select name="channel_id" id="channel_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('channel_id') border-red-500 @enderror">
                                <option value="">Выберите канал</option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ (old('channel_id') == $channel->id || request('channel') == $channel->id) ? 'selected' : '' }} 
                                        data-has-bot="{{ $channel->bot_added ? 'true' : 'false' }}" 
                                        data-username="{{ $channel->telegram_username }}">
                                        {{ $channel->name }} ({{ ucfirst($channel->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('channel_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            <div id="botWarning" class="hidden mt-2 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                <p class="font-bold">Внимание!</p>
                                <p>Бот не добавлен в канал или не является администратором. Публикация может быть недоступна.</p>
                                <a href="#" id="checkBotStatus" class="text-blue-600 underline mt-2 inline-block">Проверить статус бота</a>
                            </div>
                        </div>
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Заголовок</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Содержание</label>
                            <textarea name="content" id="content" rows="6" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Медиа-файлы</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Загрузить файлы</span>
                                            <input id="media" name="media[]" type="file" class="sr-only" multiple>
                                        </label>
                                        <p class="pl-1">или перетащите их сюда</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF до 10MB</p>
                                </div>
                            </div>
                            <div id="fileList" class="mt-2"></div>
                            @error('media')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('media.*')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Статус публикации</label>
                            <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Черновик</option>
                                <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Запланировать публикацию</option>
                                <option value="publish_now" {{ old('status') == 'publish_now' ? 'selected' : '' }}>Опубликовать сейчас</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div id="scheduledDateContainer" class="{{ old('status') == 'scheduled' ? '' : 'hidden' }}">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Дата и время публикации</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('scheduled_at') border-red-500 @enderror">
                            @error('scheduled_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Создать пост
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
            // Обработка изменения статуса публикации
            const statusSelect = document.getElementById('status');
            const scheduledDateContainer = document.getElementById('scheduledDateContainer');
            
            statusSelect.addEventListener('change', function() {
                if (this.value === 'scheduled') {
                    scheduledDateContainer.classList.remove('hidden');
                } else {
                    scheduledDateContainer.classList.add('hidden');
                }
            });
            
            // Обработка изменения канала и проверка статуса бота
            const channelSelect = document.getElementById('channel_id');
            const botWarning = document.getElementById('botWarning');
            const checkBotStatusButton = document.getElementById('checkBotStatus');
            
            function checkBotStatus() {
                const selectedOption = channelSelect.options[channelSelect.selectedIndex];
                if (!selectedOption.value) return;
                
                const hasBotAdded = selectedOption.dataset.hasBotAdded === 'true';
                const username = selectedOption.dataset.username;
                
                if (!hasBotAdded && username) {
                    botWarning.classList.remove('hidden');
                    
                    // Делаем AJAX запрос для проверки статуса бота
                    fetch('/api/check-bot-status/' + selectedOption.value)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.is_admin) {
                                botWarning.classList.add('hidden');
                                // Обновляем статус бота в БД через отдельный запрос
                                fetch('/api/update-bot-status/' + selectedOption.value, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });
                            }
                        })
                        .catch(error => console.error('Ошибка проверки статуса бота:', error));
                } else {
                    botWarning.classList.add('hidden');
                }
            }
            
            channelSelect.addEventListener('change', checkBotStatus);
            checkBotStatusButton.addEventListener('click', function(e) {
                e.preventDefault();
                checkBotStatus();
            });
            
            // Вызываем проверку при загрузке страницы
            if (channelSelect.value) {
                checkBotStatus();
            }
            
            // Обработка загрузки файлов
            const mediaInput = document.getElementById('media');
            const fileList = document.getElementById('fileList');
            
            mediaInput.addEventListener('change', function() {
                fileList.innerHTML = '';
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    const fileItem = document.createElement('div');
                    fileItem.className = 'text-sm mt-1';
                    fileItem.textContent = file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
                    fileList.appendChild(fileItem);
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 