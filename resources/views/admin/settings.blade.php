<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Настройки системы') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-medium">Общие настройки</h3>
                    </div>

                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="site_name" :value="__('Название сайта')" />
                            <x-text-input id="site_name" name="site_name" type="text" class="mt-1 block w-full" value="{{ config('app.name') }}" />
                        </div>
                        
                        <div>
                            <x-input-label for="admin_email" :value="__('Email администратора')" />
                            <x-text-input id="admin_email" name="admin_email" type="email" class="mt-1 block w-full" value="admin@admin.com" />
                        </div>
                        
                        <div>
                            <x-input-label for="telegram_bot_token" :value="__('Токен Telegram Bot API')" />
                            <x-text-input id="telegram_bot_token" name="telegram_bot_token" type="text" class="mt-1 block w-full" value="{{ config('services.telegram.bot_token', '') }}" />
                            <p class="mt-1 text-sm text-gray-500">Используется для подключения к Telegram API и отправки сообщений</p>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="debug_mode" name="debug_mode" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                            <label for="debug_mode" class="ml-2 block text-sm text-gray-900">Режим отладки</label>
                        </div>
                        
                        <div>
                            <x-primary-button>
                                {{ __('Сохранить настройки') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 