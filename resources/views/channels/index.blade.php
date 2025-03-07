<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Мои каналы') }}
            </h2>
            <div>
                <a href="{{ route('channels.update-all-status') }}" class="mr-2 inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Обновить статусы
                </a>
                <a href="{{ route('channels.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Создать канал
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (count($channels) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($channels as $channel)
                                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-150" 
                                     data-channel-id="{{ $channel->id }}" 
                                     data-channel-type="{{ $channel->type }}">
                                    <div class="p-4 border-b bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-lg font-semibold">
                                                <a href="{{ route('channels.show', $channel->id) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $channel->name }}
                                                </a>
                                            </h3>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $channel->type == 'telegram' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($channel->type) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        @if ($channel->description)
                                            <p class="text-gray-600 mb-3 text-sm">
                                                {{ \Illuminate\Support\Str::limit($channel->description, 100) }}
                                            </p>
                                        @else
                                            <p class="text-gray-500 mb-3 text-sm italic">
                                                Нет описания
                                            </p>
                                        @endif
                                        
                                        @if ($channel->type == 'telegram')
                                            <div class="mb-3">
                                                <p class="text-sm">
                                                    <span class="font-medium">Имя канала:</span> 
                                                    @if ($channel->telegram_username)
                                                        <a href="https://t.me/{{ $channel->telegram_username }}" target="_blank" class="text-blue-600 hover:underline">
                                                            @{{ $channel->telegram_username }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-500 italic">Не указано</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-sm">
                                                    <span class="font-medium">Статус бота:</span> 
                                                    @if (!$channel->bot_added)
                                                        <span class="text-red-600 bot-status">Не подключен</span>
                                                        <a href="{{ route('channels.connect-bot', $channel->id) }}" class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 connect-bot-button">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                            </svg>
                                                            Подключить
                                                        </a>
                                                        <form method="POST" action="{{ route('channels.force-connect', $channel->id) }}" class="inline connect-bot-button">
                                                            @csrf
                                                            <button type="submit" class="ml-1 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                                Принудительно подключить
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-green-600 bot-status">Подключен</span>
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="p-4 border-t bg-gray-50 flex justify-between items-center">
                                        <div>
                                            <span class="text-xs text-gray-500">Создан: {{ $channel->created_at->format('d.m.Y') }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('channels.edit', $channel->id) }}" class="text-blue-600 hover:text-blue-900" title="Редактировать">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            
                                            <a href="{{ route('posts.channel', $channel->id) }}" class="text-green-600 hover:text-green-900" title="Посты канала">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                            
                                            <a href="{{ route('channels.auto-posting.edit', $channel->id) }}" class="text-purple-600 hover:text-purple-900" title="Настройки автопостинга">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                            
                                            <form action="{{ route('channels.destroy', $channel->id) }}" method="POST" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот канал?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">У вас пока нет каналов</h3>
                            <p class="mt-1 text-gray-500">Создайте ваш первый канал, чтобы начать работу с автопостингом.</p>
                            <div class="mt-6">
                                <a href="{{ route('channels.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Создать канал
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Проверка статуса ботов при загрузке страницы
        const checkAllBotsStatus = async function() {
            const channelCards = document.querySelectorAll('[data-channel-id]');
            
            for (const card of channelCards) {
                const channelId = card.dataset.channelId;
                const channelType = card.dataset.channelType;
                
                if (channelType === 'telegram') {
                    const statusElement = card.querySelector('.bot-status');
                    if (statusElement && statusElement.classList.contains('text-red-600')) {
                        try {
                            // Проверяем статус через дебаг-маршрут
                            const response = await fetch(`/debug/channels/${channelId}`);
                            const data = await response.json();
                            
                            if (data.updated && data.after_update.bot_added) {
                                // Обновляем UI без перезагрузки
                                statusElement.textContent = 'Подключен';
                                statusElement.classList.remove('text-red-600');
                                statusElement.classList.add('text-green-600');
                                
                                // Скрываем кнопки подключения
                                const connectButtons = card.querySelectorAll('.connect-bot-button');
                                connectButtons.forEach(button => button.classList.add('hidden'));
                            }
                        } catch (error) {
                            console.error('Ошибка при проверке статуса бота:', error);
                        }
                    }
                }
            }
        };
        
        // Запускаем проверку с небольшой задержкой
        setTimeout(checkAllBotsStatus, 500);
    });
</script>
@endpush 