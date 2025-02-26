<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Настройки') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        
                        <!-- Site Name -->
                        <div class="mb-4">
                            <label for="site_name" class="block text-sm font-medium text-gray-700">Название сайта</label>
                            <input type="text" name="site_name" id="site_name" value="{{ $settings['site_name'] ?? '' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <!-- Admin Email -->
                        <div class="mb-4">
                            <label for="admin_email" class="block text-sm font-medium text-gray-700">Email администратора</label>
                            <input type="email" name="admin_email" id="admin_email" value="{{ $settings['admin_email'] ?? '' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <!-- Telegram Bot Token -->
                        <div class="mb-4">
                            <label for="telegram_bot_token" class="block text-sm font-medium text-gray-700">Токен Telegram бота</label>
                            <input type="text" name="telegram_bot_token" id="telegram_bot_token" value="{{ $settings['telegram_bot_token'] ?? '' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-sm text-gray-500 mt-1">Получите токен у @BotFather в Telegram</p>
                        </div>
                        
                        <!-- Debug Mode -->
                        <div class="mb-4">
                            <label for="debug_mode" class="flex items-center">
                                <input type="checkbox" name="debug_mode" id="debug_mode" 
                                       {{ isset($settings['debug_mode']) && $settings['debug_mode'] ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Режим отладки</span>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Сохранить настройки
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 