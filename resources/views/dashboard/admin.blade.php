<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Панель администратора') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Статистика системы</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-blue-100 p-4 rounded-lg shadow">
                        <div class="text-3xl font-bold text-blue-800">{{ App\Models\User::count() }}</div>
                        <div class="text-sm text-blue-800">Пользователей</div>
                    </div>
                    
                    <div class="bg-green-100 p-4 rounded-lg shadow">
                        <div class="text-3xl font-bold text-green-800">{{ App\Models\Channel::count() }}</div>
                        <div class="text-sm text-green-800">Каналов</div>
                    </div>
                    
                    <div class="bg-purple-100 p-4 rounded-lg shadow">
                        <div class="text-3xl font-bold text-purple-800">{{ App\Models\Post::count() }}</div>
                        <div class="text-sm text-purple-800">Постов</div>
                    </div>
                    
                    <div class="bg-yellow-100 p-4 rounded-lg shadow">
                        <div class="text-3xl font-bold text-yellow-800">{{ App\Models\Subscription::where('status', 'active')->count() }}</div>
                        <div class="text-sm text-yellow-800">Активных подписок</div>
                    </div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Активность за последние 7 дней</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-4 border rounded-lg shadow">
                        <h4 class="font-medium text-gray-800 mb-2">Новые пользователи</h4>
                        <div class="h-64 bg-gray-50 rounded flex items-center justify-center">
                            <canvas id="newUsersChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 border rounded-lg shadow">
                        <h4 class="font-medium text-gray-800 mb-2">Опубликованные посты</h4>
                        <div class="h-64 bg-gray-50 rounded flex items-center justify-center">
                            <canvas id="postsChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Быстрые действия</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.users') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Пользователи</div>
                                <div class="text-sm text-gray-500">Управление пользователями</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.channels') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Каналы</div>
                                <div class="text-sm text-gray-500">Все каналы в системе</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.posts') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Посты</div>
                                <div class="text-sm text-gray-500">Все посты в системе</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.plans.index') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Тарифы</div>
                                <div class="text-sm text-gray-500">Управление тарифами</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.subscriptions') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Подписки</div>
                                <div class="text-sm text-gray-500">Активные подписки</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.settings') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-gray-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Настройки</div>
                                <div class="text-sm text-gray-500">Настройки системы</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.logs') }}" class="bg-white p-4 border rounded-lg shadow hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="bg-red-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Логи</div>
                                <div class="text-sm text-gray-500">Системные логи</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Данные для графиков (в реальном приложении должны приходить с бекенда)
        const dates = [
            '{{ now()->subDays(6)->format("d.m") }}',
            '{{ now()->subDays(5)->format("d.m") }}',
            '{{ now()->subDays(4)->format("d.m") }}',
            '{{ now()->subDays(3)->format("d.m") }}',
            '{{ now()->subDays(2)->format("d.m") }}',
            '{{ now()->subDays(1)->format("d.m") }}',
            '{{ now()->format("d.m") }}'
        ];
        
        // График новых пользователей
        const usersCtx = document.getElementById('newUsersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Новые пользователи',
                    data: [3, 5, 2, 7, 4, 6, 8],
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // График опубликованных постов
        const postsCtx = document.getElementById('postsChart').getContext('2d');
        new Chart(postsCtx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Опубликованные посты',
                    data: [12, 19, 15, 25, 22, 30, 28],
                    backgroundColor: 'rgba(16, 185, 129, 0.7)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout> 