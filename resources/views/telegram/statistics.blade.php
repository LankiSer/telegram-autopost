<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Статистика Telegram') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Общая статистика -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Обзор</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-4 shadow">
                            <div class="text-2xl font-bold">{{ count($channels) }}</div>
                            <div class="text-sm opacity-80">Всего каналов</div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-4 shadow">
                            <div class="text-2xl font-bold">{{ $totalPosts }}</div>
                            <div class="text-sm opacity-80">Всего постов</div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-4 shadow">
                            <div class="text-2xl font-bold">{{ $channels->sum('members_count') }}</div>
                            <div class="text-sm opacity-80">Общая аудитория</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Статистика постов -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Активность постов</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-100 text-green-800 rounded-lg p-4 shadow">
                            <div class="text-2xl font-bold">{{ $sentPosts }}</div>
                            <div class="text-sm text-green-700">Отправленные посты</div>
                        </div>
                        
                        <div class="bg-blue-100 text-blue-800 rounded-lg p-4 shadow">
                            <div class="text-2xl font-bold">{{ $scheduledPosts }}</div>
                            <div class="text-sm text-blue-700">Запланированные посты</div>
                        </div>
                        
                        <div class="bg-gray-100 text-gray-800 rounded-lg p-4 shadow">
                            <div class="text-2xl font-bold">{{ $totalPosts - $sentPosts - $scheduledPosts }}</div>
                            <div class="text-sm text-gray-700">Черновики и другие</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Статистика каналов -->
            @if (count($channels) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Каналы</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Название
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Аудитория
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Посты
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Действия
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($channels as $channel)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $channel->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $channel->channel_username }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $channel->members_count }} подписчиков</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $channel->posts->count() }} постов</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('telegram.posts', $channel->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    Просмотр постов
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 