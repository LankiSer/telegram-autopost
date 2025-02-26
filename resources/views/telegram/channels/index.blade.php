<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Мои Telegram каналы') }}
            </h2>
            <a href="{{ route('telegram.channels.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Добавить канал
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (count($channels) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($channels as $channel)
                                <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="font-semibold text-lg text-gray-800">{{ $channel->name }}</h3>
                                        <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            {{ $channel->members_count }} подписчиков
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mb-4">{{ $channel->channel_username }}</p>
                                    <div class="flex justify-between mt-4">
                                        <a href="{{ route('telegram.posts', $channel->id) }}" class="text-blue-600 hover:text-blue-800">
                                            Управление постами
                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-gray-700">
                                            Обновить статистику
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white text-center">
                        <p class="text-gray-600 mb-4">У вас пока нет добавленных каналов</p>
                        <a href="{{ route('telegram.channels.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Добавить первый канал
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 