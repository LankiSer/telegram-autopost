<template>
    <Head :title="`Посты канала: ${channel.name}`" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Посты канала: {{ channel.name }}
                </h2>
                <Link :href="route('posts.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Создать пост
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Информация о канале -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-lg font-bold mr-4">
                            {{ channel.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ channel.name }}</h3>
                            <div class="flex items-center space-x-2 mt-1">
                                <span v-if="channel.telegram_username" class="text-sm text-blue-600 dark:text-blue-400">
                                    @{{ channel.telegram_username }}
                                </span>
                                <span v-if="channel.members_count" class="text-sm text-gray-500 dark:text-gray-400">
                                    • {{ channel.members_count }} подписчиков
                                </span>
                                <span v-if="channel.bot_added" class="px-2 py-0.5 text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full">
                                    Бот подключен
                                </span>
                                <span v-else class="px-2 py-0.5 text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 rounded-full">
                                    Бот не подключен
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список постов -->
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
                        <div v-if="posts.data && posts.data.length > 0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Контент</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата создания</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4">
                                                <Link :href="route('posts.show', post.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                    {{ truncateText(post.content, 50) }}
                                                </Link>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                      :class="{
                                                          'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': post.status === 'published',
                                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': post.status === 'scheduled',
                                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': post.status === 'draft',
                                                          'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100': post.status === 'failed'
                                                      }">
                                                    {{ getStatusText(post.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatDate(post.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <Link :href="route('posts.edit', post.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Редактировать">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </Link>
                                                    
                                                    <button @click="confirmDelete(post.id)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Удалить">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Пагинация -->
                            <div class="mt-4" v-if="posts.links && posts.links.length > 3">
                                <div class="flex flex-wrap justify-center">
                                    <Link v-for="(link, i) in posts.links" 
                                          :key="i" 
                                          v-if="link.url !== null"
                                          :href="link.url" 
                                          :class="{'bg-blue-500 text-white': link.active, 'text-gray-700 dark:text-gray-300': !link.active}" 
                                          class="mx-1 px-3 py-1 rounded text-sm" 
                                          v-html="link.label">
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Постов не найдено</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Создайте новый пост для этого канала</p>
                            <div class="mt-6">
                                <Link :href="route('posts.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Создать пост
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
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    posts: Object,
    channel: Object
});

function confirmDelete(postId) {
    if (confirm('Вы уверены, что хотите удалить этот пост?')) {
        router.delete(route('posts.destroy', postId));
    }
}

function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}

function getStatusText(status) {
    const statusMap = {
        'draft': 'Черновик',
        'scheduled': 'Запланирован',
        'published': 'Опубликован',
        'failed': 'Ошибка',
        'pending': 'В очереди'
    };
    return statusMap[status] || status;
}
</script> 