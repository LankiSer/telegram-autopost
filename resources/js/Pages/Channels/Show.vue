<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    channel: {
        type: Object,
        required: true
    },
    flash: Object
});

const deleteForm = useForm({});

function confirmDelete(channelId) {
    if (confirm('Вы уверены, что хотите удалить этот канал?')) {
        deleteForm.delete(route('channels.destroy', channelId));
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}
</script>

<template>
    <Head :title="channel?.name || 'Информация о канале'" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Шапка -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ channel?.name || 'Информация о канале' }}
                </h1>
                <Link :href="route('channels.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Назад к списку
                </Link>
            </div>

            <!-- Уведомления -->
            <div v-if="flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ flash.success }}</span>
            </div>
            <div v-if="flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ flash.error }}</span>
            </div>
            <div v-if="flash?.message" class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ flash.message }}</span>
            </div>

            <!-- Карточка с информацией о канале -->
            <div v-if="channel" class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <!-- Шапка канала -->
                    <div class="flex justify-between items-center pb-4 border-b mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ channel.name }}
                            </h2>
                            <p v-if="channel.description" class="text-gray-600 dark:text-gray-300 mt-2">
                                {{ channel.description }}
                            </p>
                            <p v-else class="text-gray-500 dark:text-gray-400 mt-1 italic">
                                Нет описания
                            </p>
                        </div>
                        <span class="px-3 py-1 text-sm rounded-full"
                            :class="channel.type == 'telegram' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'">
                            {{ channel.type ? (channel.type.charAt(0).toUpperCase() + channel.type.slice(1)) : 'Telegram' }}
                        </span>
                    </div>

                    <!-- Детали канала -->
                    <div class="space-y-4">
                        <div v-if="channel.type == 'telegram' || !channel.type" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Имя канала</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <a v-if="channel.telegram_username" :href="`https://t.me/${channel.telegram_username}`" target="_blank" class="text-blue-600 hover:underline">
                                        @{{ channel.telegram_username }}
                                    </a>
                                    <span v-else class="text-gray-500 italic">Не указано</span>
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Статус бота</h3>
                                <div class="mt-1 flex items-center">
                                    <span v-if="channel.bot_added" class="text-green-600 dark:text-green-400 text-sm">Подключен</span>
                                    <span v-else class="text-red-600 dark:text-red-400 text-sm">Не подключен</span>
                                    
                                    <span v-if="!channel.bot_added" class="ml-2">
                                        <a :href="route('channels.connect-bot', channel.id)" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            Подключить
                                        </a>
                                        
                                        <form :action="route('channels.force-connect', channel.id)" method="POST" class="inline ml-1">
                                            <input type="hidden" name="_token" :value="document.querySelector('meta[name=csrf-token]')?.getAttribute('content')">
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Принудительно подключить
                                            </button>
                                        </form>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="channel.members_count" class="mt-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Подписчиков</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ new Intl.NumberFormat('ru-RU').format(channel.members_count) }}
                            </p>
                        </div>
                        
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Дата создания</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ formatDate(channel.created_at) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="mt-8 pt-4 border-t flex justify-end space-x-3">
                        <Link v-if="channel.id" :href="route('channels.edit', channel.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Редактировать
                        </Link>
                        
                        <Link v-if="channel.id" :href="route('posts.channel', channel.id)" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Посты канала
                        </Link>
                        
                        <Link v-if="channel.id" :href="route('channels.auto-posting.edit', channel.id)" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500 active:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Настройки автопостинга
                        </Link>
                        
                        <button v-if="channel.id" @click="confirmDelete(channel.id)" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 