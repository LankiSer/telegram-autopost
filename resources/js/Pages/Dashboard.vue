<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalChannels: 0,
            totalPosts: 0,
            totalGroups: 0
        })
    },
    recentPosts: {
        type: Array,
        default: () => []
    }
});

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
        'published': 'Опубликован',
        'scheduled': 'Запланирован',
        'draft': 'Черновик',
        'failed': 'Ошибка',
        'pending': 'В очереди'
    };
    return statusMap[status] || status;
}
</script>

<template>
    <Head title="Панель управления" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Панель управления
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg shadow-sm border border-blue-100 dark:border-blue-800">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-500 dark:bg-blue-600 text-white mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Каналы</p>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.totalChannels || 0 }}</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <Link :href="route('channels.index')" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                        Управление каналами →
                                    </Link>
                                </div>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg shadow-sm border border-green-100 dark:border-green-800">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-500 dark:bg-green-600 text-white mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Посты</p>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.totalPosts || 0 }}</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <Link :href="route('posts.index')" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm font-medium">
                                        Просмотр постов →
                                    </Link>
                                </div>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg shadow-sm border border-purple-100 dark:border-purple-800">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-purple-500 dark:bg-purple-600 text-white mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Группы</p>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.totalGroups || 0 }}</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <Link :href="route('channel-groups.index')" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 text-sm font-medium">
                                        Управление группами →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Быстрые действия</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <Link :href="route('channels.create')" class="bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 p-4 rounded-lg border border-blue-100 dark:border-blue-800 transition duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-blue-500 dark:bg-blue-600 text-white mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Создать канал</span>
                                    </div>
                                </Link>

                                <Link :href="route('posts.create')" class="bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 p-4 rounded-lg border border-green-100 dark:border-green-800 transition duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-green-500 dark:bg-green-600 text-white mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Написать пост</span>
                                    </div>
                                </Link>

                                <Link :href="route('channel-groups.create')" class="bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 p-4 rounded-lg border border-purple-100 dark:border-purple-800 transition duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-full bg-purple-500 dark:bg-purple-600 text-white mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Создать группу</span>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <div v-if="recentPosts && recentPosts.length > 0" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Последние посты</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-900">
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Название</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Канал</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="post in recentPosts" :key="post.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <Link :href="route('posts.show', post.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                    {{ truncateText(post.title || post.content || 'Без названия', 40) }}
                                                </Link>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ post.channel.name }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                      :class="{
                                                          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': post.status === 'published',
                                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': post.status === 'scheduled',
                                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': post.status === 'draft',
                                                          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': post.status === 'failed'
                                                      }">
                                                    {{ getStatusText(post.status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatDate(post.created_at) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
