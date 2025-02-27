<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Статистика') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-blue-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">Всего постов</h3>
                            <p class="text-3xl">{{ $totalPosts }}</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">Опубликовано</h3>
                            <p class="text-3xl">{{ $publishedPosts }}</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold">Запланировано</h3>
                            <p class="text-3xl">{{ $scheduledPosts }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="bg-blue-100 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-900">Подписчики в Telegram</h3>
                                    <p class="text-3xl font-bold text-blue-700">{{ number_format($totalSubscribers) }}</p>
                                </div>
                                <div class="text-blue-500">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-green-900">Просмотры постов</h3>
                                    <p class="text-3xl font-bold text-green-700">{{ number_format($totalViews) }}</p>
                                </div>
                                <div class="text-green-500">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm text-green-600 mt-2">За последние 100 постов</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Активность за последний месяц</h3>
                        <div class="h-64">
                            <!-- Здесь можно добавить график с использованием JavaScript -->
                            <div class="bg-gray-100 p-4 rounded">
                                @foreach($activityData as $data)
                                    <div class="mb-2">
                                        <span>{{ $data->date }}: {{ $data->count }} постов</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Статистика по каналам</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($channelsStats as $channel)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium">{{ $channel->name }}</h4>
                                    <p>Всего постов: {{ $channel->posts_count }}</p>
                                    <p>Опубликовано: {{ $channel->published_posts }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 