<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Панель управления') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Статус подписки</h3>
                
                @php
                    $subscription = auth()->user()->activeSubscription();
                @endphp
                
                @if($subscription)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-green-800 font-semibold">Активная подписка</div>
                                <div class="text-lg font-bold text-green-900">{{ $subscription->plan->name }}</div>
                                <div class="text-sm text-green-700 mt-1">
                                    @if($subscription->ends_at)
                                        Действует до: {{ $subscription->ends_at->format('d.m.Y') }}
                                        ({{ now()->diffInDays($subscription->ends_at) }} дней)
                                    @else
                                        Бессрочная подписка
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-bold text-green-900">
                                    @if($subscription->plan->price > 0)
                                        {{ $subscription->plan->price }} ₽/месяц
                                    @else
                                        Бесплатно
                                    @endif
                                </div>
                                <a href="{{ route('subscription.plans') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Изменить тариф
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm text-blue-800">Лимит каналов</div>
                                    <div class="text-lg font-bold text-blue-900">
                                        {{ auth()->user()->channels()->count() }} / {{ $subscription->plan->channel_limit }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm text-purple-800">Посты в месяц</div>
                                    @php
                                        $monthStart = now()->startOfMonth();
                                        $postsThisMonth = App\Models\Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))
                                            ->where('created_at', '>=', $monthStart)
                                            ->count();
                                    @endphp
                                    <div class="text-lg font-bold text-purple-900">
                                        {{ $postsThisMonth }} / {{ $subscription->plan->posts_per_month }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-3 rounded-full">
                                    <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm text-yellow-800">Планирование постов</div>
                                    <div class="text-lg font-bold text-yellow-900">
                                        {{ $subscription->plan->scheduling_enabled ? 'Доступно' : 'Недоступно' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">У вас нет активной подписки</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Для использования сервиса необходимо оформить подписку.</p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('subscription.plans') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Выбрать тариф
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Мои каналы</h3>
                
                @php
                    $channels = auth()->user()->channels()->latest()->take(3)->get();
                @endphp
                
                <div class="overflow-hidden bg-white shadow sm:rounded-md mb-4">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($channels as $channel)
                            <li>
                                <a href="{{ route('channels.show', $channel) }}" class="block hover:bg-gray-50">
                                    <div class="flex items-center px-4 py-4 sm:px-6">
                                        <div class="flex min-w-0 flex-1 items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1 px-4">
                                                <div>
                                                    <p class="truncate text-sm font-medium text-indigo-600">{{ $channel->name }}</p>
                                                    <p class="mt-1 truncate text-sm text-gray-500">{{ $channel->telegram_channel_id }}</p>
                                                </div>
                                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                    <span class="flex items-center">
                                                        @if($channel->bot_added)
                                                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span>Бот подключен</span>
                                                        @else
                                                            <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span>Бот не подключен</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-4 py-5 sm:px-6">
                                <div class="text-center">
                                    <p class="text-sm text-gray-500">У вас пока нет каналов</p>
                                    <div class="mt-4">
                                        <a href="{{ route('channels.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Добавить канал
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                
                @if(count($channels) > 0)
                    <div class="flex justify-end">
                        <a href="{{ route('channels.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Все каналы <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                @endif
                
                <h3 class="text-lg font-medium text-gray-900 mb-4 mt-8">Последние посты</h3>
                
                @php
                    $posts = App\Models\Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))
                        ->with('channel')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                
                <div class="overflow-hidden bg-white shadow sm:rounded-md mb-6">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($posts as $post)
                            <li>
                                <a href="{{ route('posts.show', $post) }}" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="truncate text-sm font-medium text-indigo-600">{{ Str::limit($post->content, 70) }}</div>
                                            <div class="ml-2 flex flex-shrink-0">
                                                @if($post->status === 'published')
                                                    <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                        Опубликован
                                                    </span>
                                                @elseif($post->status === 'scheduled')
                                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                                        Запланирован
                                                    </span>
                                                @elseif($post->status === 'draft')
                                                    <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                                        Черновик
                                                    </span>
                                                @elseif($post->status === 'failed')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                                        Ошибка
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-between">
                                            <div class="sm:flex">
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $post->channel->name }}
                                                </div>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $post->created_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-4 py-5 sm:px-6">
                                <div class="text-center">
                                    <p class="text-sm text-gray-500">У вас пока нет постов</p>
                                    <div class="mt-4">
                                        <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Создать пост
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                
                @if(count($posts) > 0)
                    <div class="flex justify-end">
                        <a href="{{ route('posts.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Все посты <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <div class="bg-white p-4 border rounded-lg shadow">
                        <h4 class="font-medium text-gray-800 mb-4">Быстрые действия</h4>
                        <div class="space-y-2">
                            <a href="{{ route('posts.create') }}" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Создать новый пост</span>
                            </a>
                            <a href="{{ route('channels.create') }}" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Добавить новый канал</span>
                            </a>
                            <a href="{{ route('subscription.plans') }}" class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span>Изменить тарифный план</span>
                            </a>
                        </div>
                    </div>
                    
                    @if(auth()->user()->canViewAnalytics())
                        <div class="bg-white p-4 border rounded-lg shadow">
                            <h4 class="font-medium text-gray-800 mb-4">Статистика публикаций</h4>
                            <div class="h-64 bg-gray-50 rounded flex items-center justify-center">
                                <canvas id="postsStatsChart"></canvas>
                            </div>
                        </div>
                        
                        @push('scripts')
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            // Данные для графика (в реальном приложении должны приходить с бекенда)
                            const postsStatsCtx = document.getElementById('postsStatsChart').getContext('2d');
                            const postsChart = new Chart(postsStatsCtx, {
                                type: 'pie',
                                data: {
                                    labels: ['Опубликовано', 'Запланировано', 'Черновики', 'Ошибки'],
                                    datasets: [{
                                        data: [
                                            // Здесь должны быть реальные данные
                                            {{ App\Models\Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))->where('status', 'published')->count() }},
                                            {{ App\Models\Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))->where('status', 'scheduled')->count() }},
                                            {{ App\Models\Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))->where('status', 'draft')->count() }},
                                            {{ App\Models\Post::whereIn('channel_id', auth()->user()->channels->pluck('id'))->where('status', 'failed')->count() }}
                                        ],
                                        backgroundColor: [
                                            'rgba(16, 185, 129, 0.7)',
                                            'rgba(245, 158, 11, 0.7)',
                                            'rgba(107, 114, 128, 0.7)',
                                            'rgba(239, 68, 68, 0.7)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        </script>
                        @endpush
                    @else
                        <div class="bg-white p-4 border rounded-lg shadow">
                            <h4 class="font-medium text-gray-800 mb-4">Статистика и аналитика</h4>
                            <div class="bg-gray-50 p-4 rounded text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h5 class="mt-2 text-sm font-medium text-gray-900">Аналитика недоступна</h5>
                                <p class="mt-1 text-sm text-gray-500">Доступно только на тарифах "Премиум" и "Бизнес"</p>
                                <div class="mt-4">
                                    <a href="{{ route('subscription.plans') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Улучшить тариф
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 