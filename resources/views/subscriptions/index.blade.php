<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Тарифные планы') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Выберите тарифный план</h3>
                
                @if($currentSubscription)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-blue-800 font-semibold">Ваш текущий тариф</div>
                                <div class="text-lg font-bold text-blue-900">{{ $currentSubscription->plan->name }}</div>
                                <div class="text-sm text-blue-700 mt-1">
                                    @if($currentSubscription->ends_at)
                                        Действует до: {{ $currentSubscription->ends_at->format('d.m.Y') }}
                                        ({{ now()->diffInDays($currentSubscription->ends_at) }} дней)
                                    @else
                                        Бессрочная подписка
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите отменить подписку?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Отменить подписку
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($plans as $plan)
                        <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="px-4 py-5 sm:p-6 bg-white">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $plan->name }}</h3>
                                <div class="mt-2 flex items-baseline">
                                    <span class="text-3xl font-extrabold text-gray-900">
                                        {{ $plan->price > 0 ? $plan->price : 0 }}
                                    </span>
                                    @if($plan->price > 0)
                                        <span class="ml-1 text-xl font-medium text-gray-500">₽/мес</span>
                                    @else
                                        <span class="ml-1 text-xl font-medium text-gray-500">Бесплатно</span>
                                    @endif
                                </div>
                                <p class="mt-3 text-sm text-gray-500">{{ $plan->description }}</p>
                                <ul role="list" class="mt-4 space-y-3">
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">
                                            <span class="font-medium">{{ $plan->channel_limit }}</span> каналов
                                        </p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">
                                            <span class="font-medium">{{ $plan->posts_per_month }}</span> постов в месяц
                                        </p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @if($plan->scheduling_enabled)
                                                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">
                                            Планирование постов
                                        </p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @if($plan->analytics_enabled)
                                                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700">
                                            Расширенная аналитика
                                        </p>
                                    </li>
                                </ul>
                                <div class="mt-6">
                                    @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                                        <button disabled class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-300 focus:outline-none">
                                            Ваш текущий тариф
                                        </button>
                                    @else
                                        <form action="{{ route('subscription.subscribe') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ $currentSubscription ? 'Сменить тариф' : 'Подписаться' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 