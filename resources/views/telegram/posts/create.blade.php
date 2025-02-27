<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Создание поста для канала "{{ $channel->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="mb-4">
                        <a href="{{ route('telegram.posts', $channel->id) }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Вернуться к постам канала
                        </a>
                    </div>
                    
                    <form method="POST" action="{{ route('telegram.posts.store', $channel->id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Контент поста -->
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Текст поста</label>
                            <textarea name="content" id="content" rows="6" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Вы можете использовать HTML теги для форматирования.</p>
                            @error('content')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Загрузка изображения -->
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Изображение (необязательно)</label>
                            <input type="file" name="image" id="image" 
                                   class="mt-1 block w-full @error('image') border-red-500 @enderror">
                            <p class="text-sm text-gray-500 mt-1">Поддерживаемые форматы: JPG, PNG, GIF. Максимальный размер: 2 МБ.</p>
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Расписание отправки -->
                        <div class="mb-4">
                            <label for="schedule_time" class="block text-sm font-medium text-gray-700">Запланировать отправку на (необязательно)</label>
                            <input type="datetime-local" name="schedule_time" id="schedule_time" value="{{ old('schedule_time') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('schedule_time') border-red-500 @enderror">
                            <p class="text-sm text-gray-500 mt-1">Оставьте пустым для немедленной отправки</p>
                            @error('schedule_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('telegram.posts', $channel->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2">
                                Отмена
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Опубликовать пост
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 