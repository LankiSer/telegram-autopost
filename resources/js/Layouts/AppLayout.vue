<script setup>
import { computed, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Transition } from 'vue';

const showingNavigationDropdown = ref(false);
const isDarkMode = ref(false);

const page = usePage();

const user = computed(() => page.props.auth.user);

// Определяем активную ссылку в навигации
const isActive = (routeName) => {
    return route().current(routeName) || 
           (route().current('channels.*') && routeName === 'channels.index') ||
           (route().current('posts.*') && routeName === 'posts.index') ||
           (route().current('scheduler.*') && routeName === 'scheduler.index') ||
           (route().current('channel-groups.*') && routeName === 'channel-groups.index') ||
           (route().current('statistics.*') && routeName === 'statistics') ||
           (route().current('subscriptions.*') && routeName === 'subscriptions.index');
};

onMounted(() => {
    // Проверка предпочтений темы пользователя
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        isDarkMode.value = true;
    } else {
        document.documentElement.classList.remove('dark');
        isDarkMode.value = false;
    }
});

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
};

// Показать или скрыть боковую навигацию на мобильных устройствах
const toggleSidebar = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value;
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Боковая навигация -->
        <div class="flex h-screen overflow-hidden bg-white dark:bg-gray-900">
            <!-- Мобильная панель навигации -->
            <div class="md:hidden flex justify-between items-center px-4 py-2 bg-blue-600 dark:bg-blue-800 text-white">
                <div class="flex items-center">
                    <img class="h-8 w-auto mr-2" src="/favicon.ico" alt="Logo">
                    <span class="font-semibold">TelegramAutopost</span>
                </div>
                <button @click="toggleSidebar" class="p-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Боковая панель (адаптивная) -->
            <div :class="{'hidden': !showingNavigationDropdown, 'block': showingNavigationDropdown}" class="md:block w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 overflow-y-auto">
                <!-- Логотип и название -->
                <div class="p-6 hidden md:flex items-center">
                    <img class="h-8 w-auto mr-2" src="/favicon.ico" alt="Logo">
                    <span class="font-semibold text-lg text-gray-900 dark:text-white">TelegramAutopost</span>
                </div>

                <!-- Основная навигация -->
                <nav class="px-3 py-2">
                    <div class="mb-6">
                        <h3 class="mx-3 mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Основное
                        </h3>
                        <Link :href="route('dashboard')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('dashboard'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('dashboard')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Панель управления
                        </Link>
                        <Link :href="route('channels.index')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('channels.index'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('channels.index')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Каналы
                        </Link>
                        <Link :href="route('posts.index')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('posts.index'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('posts.index')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Посты
                        </Link>
                        <Link :href="route('scheduler.index')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('scheduler.index'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('scheduler.index')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Планировщик
                        </Link>
                        <Link :href="route('channel-groups.index')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('channel-groups.index'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('channel-groups.index')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Группы каналов
                        </Link>
                        <Link :href="route('statistics')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('statistics'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('statistics')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Статистика
                        </Link>
                        <Link :href="route('subscriptions.index')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('subscriptions.index'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('subscriptions.index')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Тарифы
                        </Link>
                    </div>

                    <div class="mb-6">
                        <h3 class="mx-3 mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Настройки
                        </h3>
                        <Link :href="route('profile.edit')" 
                              :class="{'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isActive('profile.edit'), 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800': !isActive('profile.edit')}"
                              class="flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Профиль
                        </Link>
                        <button @click="toggleDarkMode" 
                              class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md mb-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <svg v-if="isDarkMode" class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg v-else class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            {{ isDarkMode ? 'Светлая тема' : 'Тёмная тема' }}
                        </button>
                    </div>
                </nav>

                <!-- Нижняя часть боковой панели с информацией о пользователе -->
                <div class="mt-auto px-4 py-4 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-9 w-9 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold">
                                {{ user?.name?.charAt(0).toUpperCase() || 'U' }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ user?.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ user?.email }}
                            </p>
                        </div>
                        <div class="ml-auto">
                            <Link :href="route('logout')" method="post" as="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Основное содержимое -->
            <div class="flex-1 flex flex-col overflow-y-auto">
                <!-- Верхняя панель -->
                <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex items-center">
                                <button @click="toggleSidebar" class="px-4 text-gray-500 dark:text-gray-400 md:hidden">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div class="ml-3 md:ml-0">
                                    <slot name="header"></slot>
                                </div>
                            </div>
                            
                            <!-- Add theme toggle in header -->
                            <div class="flex items-center">
                                <button @click="toggleDarkMode" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                                    <svg v-if="isDarkMode" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Основной контент -->
                <main class="flex-1 bg-gray-100 dark:bg-gray-900">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <!-- Сообщения и уведомления -->
                            <div v-if="$page.props?.flash">
                                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                                    {{ $page.props.flash.success }}
                                </div>
                                <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                    {{ $page.props.flash.error }}
                                </div>
                                <div v-if="$page.props.flash?.message" class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                                    {{ $page.props.flash.message }}
                                </div>
                            </div>
                            
                            <!-- Основной слот контента -->
                            <slot />
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template> 