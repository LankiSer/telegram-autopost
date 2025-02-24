<x-guest-layout>
    <!-- Hero Banner -->
    <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-700 opacity-90"></div>
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
        </div>
        <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Автоматизируйте ваши посты в Telegram
                </h1>
                <p class="text-lg sm:text-xl mb-8 text-blue-100">
                    Планируйте, создавайте и анализируйте контент для вашего Telegram канала с помощью нашего удобного сервиса
                </p>
                <div class="space-x-4">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors cta-button">
                        Начать бесплатно
                    </a>
                    <a href="/features" class="inline-block border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                        Узнать больше
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 sm:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Почему выбирают нас
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Мы предоставляем полный набор инструментов для эффективного управления вашим Telegram каналом
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-8 transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="feature-icon text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Планирование постов</h3>
                    <p class="text-gray-600 text-center">Создавайте и планируйте посты заранее для оптимального времени публикации. Настраивайте расписание и автоматизируйте процесс.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8 transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="feature-icon text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Аналитика</h3>
                    <p class="text-gray-600 text-center">Получайте подробную статистику по вашим публикациям. Анализируйте охват, вовлеченность и рост аудитории.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8 transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="feature-icon text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Автоматизация</h3>
                    <p class="text-gray-600 text-center">Автоматизируйте рутинные задачи. Настройте автопостинг, кросспостинг и интеграции с другими сервисами.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="bg-white py-16 sm:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-blue-600 mb-2">1M+</div>
                    <p class="text-gray-600">Активных пользователей</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-blue-600 mb-2">5M+</div>
                    <p class="text-gray-600">Опубликованных постов</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-blue-600 mb-2">99%</div>
                    <p class="text-gray-600">Точность доставки</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-blue-600 mb-2">24/7</div>
                    <p class="text-gray-600">Поддержка клиентов</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Section -->
    <div class="bg-gray-50 py-16 sm:py-24 overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="max-w-lg">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                        Удобный интерфейс для управления контентом
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Наш интуитивно понятный интерфейс позволяет легко создавать, редактировать и планировать посты. Управляйте несколькими каналами из одного места.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Drag-and-drop интерфейс
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Предпросмотр постов
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Медиа-библиотека
                        </li>
                    </ul>
                </div>
                <div class="relative">
                    <img src="https://placehold.co/600x400" alt="Dashboard Interface" class="rounded-lg shadow-xl max-w-full h-auto">
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600 text-white py-16 sm:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Готовы начать?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Присоединяйтесь к тысячам довольных пользователей уже сегодня. Начните использовать все преимущества нашего сервиса.
            </p>
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors cta-button">
                    Создать аккаунт
                </a>
                <a href="/pricing" class="inline-block border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition-colors">
                    Посмотреть тарифы
                </a>
            </div>
        </div>
    </div>
</x-guest-layout> 