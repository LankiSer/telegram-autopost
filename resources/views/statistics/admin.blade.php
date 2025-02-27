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
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Пользователей</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ number_format($totalUsers) }}</p>
                            <p class="text-sm text-gray-500">Активных: {{ number_format($activeUsers) }}</p>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Каналов</h3>
                            <p class="text-3xl font-bold text-green-600">{{ number_format($totalChannels) }}</p>
                            <p class="text-sm text-gray-500">Активных: {{ number_format($activeChannels) }}</p>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Подписчиков</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ number_format($totalSubscribers) }}</p>
                            <p class="text-sm text-gray-500">Во всех каналах</p>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Просмотров</h3>
                            <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalViews) }}</p>
                            <p class="text-sm text-gray-500">За последний месяц</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Топ пользователей</h3>
                            <div class="space-y-4">
                                @foreach($topUsers as $user)
                                <div class="flex justify-between items-center">
                                    <span>{{ $user->name }}</span>
                                    <span class="font-semibold">{{ $user->posts_count }} постов</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Топ каналов</h3>
                            <div class="space-y-4">
                                @foreach($topChannels as $channel)
                                <div class="flex justify-between items-center">
                                    <span>{{ $channel->name }}</span>
                                    <span class="font-semibold">{{ number_format($channel->subscribers) }} подписчиков</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Регистрации за месяц</h3>
                            <canvas id="registrationsChart" class="h-64"></canvas>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Публикации за месяц</h3>
                            <canvas id="postsChart" class="h-64"></canvas>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow mt-6">
                        <h3 class="text-lg font-semibold mb-4">Активность по часам</h3>
                        <canvas id="hourlyStatsChart" class="h-64"></canvas>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow mt-6">
                        <h3 class="text-lg font-semibold mb-4">Топ каналов</h3>
                        <div class="space-y-4">
                            @foreach($channelsStats as $stat)
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded">
                                <div>
                                    <h4 class="font-medium">{{ $stat['name'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ number_format($stat['subscribers']) }} подписчиков</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">{{ number_format($stat['views']) }} просмотров</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // График активности по часам
    const hourlyCtx = document.getElementById('hourlyStatsChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [{
                label: 'Просмотры',
                data: @json(array_column($hourlyStats, 'views')),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.4
            }, {
                label: 'Сообщения',
                data: @json(array_column($hourlyStats, 'messages')),
                borderColor: 'rgb(153, 102, 255)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush 