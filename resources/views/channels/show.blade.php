<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $channel->name }}
            </h2>
            <div>
                <a href="{{ route('channels.edit', $channel->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Редактировать
                </a>
                <a href="{{ route('posts.channel', $channel->id) }}" class="ml-2 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Посты
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <a href="{{ route('channels.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Вернуться к списку каналов
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Информация о канале</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <span class="text-gray-600 font-medium">Название:</span>
                                    <p>{{ $channel->name }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-gray-600 font-medium">Описание:</span>
                                    <p>{{ $channel->description ?? 'Не указано' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-gray-600 font-medium">Тип:</span>
                                    <p>{{ ucfirst($channel->type) }}</p>
                                </div>
                                
                                @if($channel->type == 'telegram')
                                <div class="mb-4">
                                    <span class="text-gray-600 font-medium">Имя пользователя в Telegram:</span>
                                    <p>@if($channel->telegram_username)
                                        <a href="https://t.me/{{ $channel->telegram_username }}" target="_blank" class="text-blue-600 hover:underline">
                                            @{{ $channel->telegram_username }}
                                        </a>
                                        @else
                                        Не указано
                                        @endif
                                    </p>
                                </div>
                                @endif
                                
                                <div>
                                    <span class="text-gray-600 font-medium">Дата создания:</span>
                                    <p>{{ $channel->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Статистика</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-4">
                                    <span class="text-gray-600 font-medium">Всего постов:</span>
                                    <p>{{ $channel->posts()->count() }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-gray-600 font-medium">Опубликовано:</span>
                                    <p>{{ $channel->posts()->where('status', 'published')->count() }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-gray-600 font-medium">В очереди на публикацию:</span>
                                    <p>{{ $channel->posts()->where('status', 'scheduled')->count() }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Настройки контента</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="mb-4">
                                        <span class="text-gray-600 font-medium">Промт для генерации контента:</span>
                                        <p class="whitespace-pre-line">{{ $channel->content_prompt ?? 'Не указан' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($channel->type == 'telegram')
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Статус бота</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-3">
                                    <span class="font-medium">Статус бота:</span> 
                                    @if ($channel->bot_added)
                                        <span class="text-green-600">Подключен</span>
                                    @else
                                        <span class="text-red-600">Не подключен</span>
                                        <a href="{{ route('channels.connect-bot', $channel->id) }}" class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            Подключить
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 