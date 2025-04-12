<template>
    <Head title="Мои каналы" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Мои каналы
                </h2>
                <div class="flex space-x-2">
                    <button @click="updateAllStatus" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Обновить статусы
                    </button>
                    <Link :href="route('channels.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Создать канал
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Уведомления отображаются через flash -->

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 md:p-6">
                        <div v-if="channels.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="channel in channels" :key="channel.id" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-150 bg-white dark:bg-gray-800">
                                <!-- Заголовок канала -->
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                             :class="channel.type == 'telegram' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'">
                                            <span class="text-lg font-bold">{{ channel.name.charAt(0).toUpperCase() }}</span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <Link :href="route('channels.show', channel.id)" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ channel.name }}
                                            </Link>
                                        </h3>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full"
                                          :class="channel.type == 'telegram' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'">
                                        {{ channel.type ? (channel.type.charAt(0).toUpperCase() + channel.type.slice(1)) : 'Telegram' }}
                                    </span>
                                </div>
                                
                                <!-- Содержимое карточки канала -->
                                <div class="p-4">
                                    <p v-if="channel.description" class="text-gray-600 dark:text-gray-300 mb-3 text-sm">
                                        {{ truncateText(channel.description, 100) }}
                                    </p>
                                    <p v-else class="text-gray-500 dark:text-gray-400 mb-3 text-sm italic">
                                        Нет описания
                                    </p>
                                    
                                    <!-- Telegram-специфическая информация -->
                                    <div v-if="channel.type == 'telegram' || !channel.type" class="space-y-2">
                                        <!-- Имя канала -->
                                        <div class="flex items-start">
                                            <div class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400 dark:text-gray-500">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    <a v-if="channel.telegram_username" :href="`https://t.me/${channel.telegram_username}`" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                        @{{ channel.telegram_username }}
                                                    </a>
                                                    <span v-else class="text-gray-500 dark:text-gray-400 italic">Не указано</span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Статус бота -->
                                        <div class="flex items-start">
                                            <div class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400 dark:text-gray-500">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex items-center">
                                                <p class="text-sm">
                                                    <span v-if="channel.bot_added" class="text-green-600 dark:text-green-400">Подключен</span>
                                                    <span v-else class="text-red-600 dark:text-red-400">Не подключен</span>
                                                </p>
                                                
                                                <div v-if="!channel.bot_added" class="ml-2 flex space-x-1">
                                                    <Link :href="route('channels.connect-bot', channel.id)" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                        </svg>
                                                        Подключить
                                                    </Link>
                                                    
                                                    <form :action="route('channels.force-connect', channel.id)" method="POST" class="inline">
                                                        <input type="hidden" name="_token" :value="csrf">
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                                            Принуд.
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Подписчики -->
                                        <div v-if="channel.members_count" class="flex items-start">
                                            <div class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400 dark:text-gray-500">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ new Intl.NumberFormat('ru-RU').format(channel.members_count) }} подписчиков
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Нижняя часть карточки с датой и кнопками -->
                                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-wrap justify-between items-center">
                                    <div class="mb-2 sm:mb-0">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Создан: {{ formatDate(channel.created_at) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <!-- Кнопки действий -->
                                        <Link :href="route('channels.edit', channel.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" title="Редактировать">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <Link :href="route('posts.channel', channel.id)" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300" title="Посты канала">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <Link :href="route('channels.auto-posting.edit', channel.id)" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300" title="Настройки автопостинга">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </Link>
                                        
                                        <button @click="confirmDelete(channel.id)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" title="Удалить">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Сообщение, если нет каналов -->
                        <div v-else class="text-center py-10">
                            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">У вас пока нет каналов</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Создайте ваш первый канал, чтобы начать работу с автопостингом.</p>
                            <div class="mt-6">
                                <Link :href="route('channels.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Создать канал
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    channels: {
        type: Array,
        default: () => [],
    }
});

const csrf = usePage().props.csrf;

function confirmDelete(channelId) {
    if (confirm('Вы уверены, что хотите удалить этот канал?')) {
        router.delete(route('channels.destroy', channelId));
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}

function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

function updateAllStatus() {
    router.get(route('channels.update-all-status'));
}
</script>

<style scoped>
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style> 