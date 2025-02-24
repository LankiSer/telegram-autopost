<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <a href="/" class="text-2xl font-bold text-blue-600">
                            <img src="{{ asset('path/to/your/logo.png') }}" alt="Logo" class="h-8">
                        </a>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button type="button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600" aria-label="toggle menu">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 fill-current">
                                <path fill-rule="evenodd" d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="/features" class="text-gray-600 hover:text-blue-600 transition-colors">Возможности</a>
                        <a href="/pricing" class="text-gray-600 hover:text-blue-600 transition-colors">Цены</a>
                        <a href="/about" class="text-gray-600 hover:text-blue-600 transition-colors">О нас</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Панель управления
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 transition-colors">Войти</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Регистрация
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="md:hidden hidden">
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                        <a href="/features" class="block px-3 py-2 text-gray-600 hover:text-blue-600">Возможности</a>
                        <a href="/pricing" class="block px-3 py-2 text-gray-600 hover:text-blue-600">Цены</a>
                        <a href="/about" class="block px-3 py-2 text-gray-600 hover:text-blue-600">О нас</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-blue-600 hover:text-blue-700">Панель управления</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-blue-600">Войти</a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 text-blue-600 hover:text-blue-700">Регистрация</a>
                        @endauth
                    </div>
                </div>
            </nav>
        </header>

        <!-- Content -->
        <main class="flex-grow mt-16">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">О компании</h3>
                        <ul class="space-y-2">
                            <li><a href="/about" class="text-gray-400 hover:text-white transition-colors">О нас</a></li>
                            <li><a href="/contact" class="text-gray-400 hover:text-white transition-colors">Контакты</a></li>
                            <li><a href="/careers" class="text-gray-400 hover:text-white transition-colors">Карьера</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Продукты</h3>
                        <ul class="space-y-2">
                            <li><a href="/features" class="text-gray-400 hover:text-white transition-colors">Возможности</a></li>
                            <li><a href="/pricing" class="text-gray-400 hover:text-white transition-colors">Цены</a></li>
                            <li><a href="/docs" class="text-gray-400 hover:text-white transition-colors">Документация</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Поддержка</h3>
                        <ul class="space-y-2">
                            <li><a href="/help" class="text-gray-400 hover:text-white transition-colors">Помощь</a></li>
                            <li><a href="/faq" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                            <li><a href="/support" class="text-gray-400 hover:text-white transition-colors">Техподдержка</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Подписка на новости</h3>
                        <form class="space-y-4">
                            <div class="flex flex-col space-y-2">
                                <input type="email" placeholder="Email" class="px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 focus:outline-none focus:border-blue-500">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Подписаться
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Все права защищены.</p>
                </div>
            </div>
        </footer>

        <!-- Mobile menu JavaScript -->
        <script>
            document.querySelector('button[aria-label="toggle menu"]').addEventListener('click', function() {
                document.querySelector('.md\\:hidden.hidden').classList.toggle('hidden');
            });
        </script>
    </body>
</html>
