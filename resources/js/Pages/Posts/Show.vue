<template>
    <Head :title="`Пост: ${truncateText(post.content, 30)}`" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Просмотр поста
                </h2>
                <div class="flex space-x-2">
                    <Link 
                        v-if="canEdit" 
                        :href="route('posts.edit', post.id)" 
                        class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Редактировать
                    </Link>
                    <Link 
                        :href="route('posts.index')" 
                        class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        К списку
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.error }}
                </div>
                
                <!-- Информация о канале -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-lg font-bold mr-4">
                            {{ post.channel.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ post.channel.name }}</h3>
                            <Link :href="route('channels.show', post.channel.id)" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Перейти к каналу
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Информация о посте -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <!-- Статус и даты -->
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-4 items-center">
                            <div>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': post.status === 'published',
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': post.status === 'scheduled',
                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': post.status === 'draft',
                                          'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100': post.status === 'failed'
                                      }">
                                    {{ getStatusText(post.status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">Создан:</span> {{ formatDate(post.created_at) }}
                            </div>
                            <div v-if="post.published_at" class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">Опубликован:</span> {{ formatDate(post.published_at) }}
                            </div>
                            <div v-if="post.scheduled_at && post.status === 'scheduled'" class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">Запланирован на:</span> {{ formatDate(post.scheduled_at) }}
                            </div>
                            <div v-if="post.error_message" class="bg-red-50 dark:bg-red-900 p-2 rounded text-sm text-red-800 dark:text-red-300 w-full mt-2">
                                <span class="font-semibold">Ошибка:</span> {{ post.error_message }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Содержимое поста -->
                    <div class="p-6">
                        <div class="prose dark:prose-invert max-w-none">
                            <div class="whitespace-pre-wrap">{{ post.content }}</div>
                        </div>
                        
                        <!-- Медиа файлы, если есть -->
                        <div v-if="post.media && post.media.length > 0" class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Медиафайлы:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div v-for="(media, index) in post.media" :key="index" class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                    <a :href="getMediaUrl(media)" target="_blank" class="block">
                                        <img v-if="isImage(media)" :src="getMediaUrl(media)" alt="Media preview" class="w-full h-40 object-cover">
                                        <div v-else class="w-full h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="p-2 text-sm text-gray-700 dark:text-gray-300 truncate">
                                            {{ getMediaName(media) }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки управления -->
                <div class="flex justify-between">
                    <button 
                        v-if="post.status === 'draft' || post.status === 'scheduled' || post.status === 'failed'" 
                        @click="publishPost" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ post.status === 'scheduled' ? 'Опубликовать сейчас' : 'Опубликовать' }}
                    </button>
                    
                    <button 
                        @click="confirmDelete" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Удалить пост
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    post: Object
});

const canEdit = computed(() => {
    const editableStatuses = ['draft', 'scheduled', 'failed', 'pending'];
    return editableStatuses.includes(props.post.status);
});

function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('ru-RU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
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

function confirmDelete() {
    if (confirm('Вы уверены, что хотите удалить этот пост?')) {
        router.delete(route('posts.destroy', props.post.id));
    }
}

function publishPost() {
    if (confirm('Вы уверены, что хотите опубликовать этот пост сейчас?')) {
        router.post(route('posts.publish', props.post.id));
    }
}

function getMediaUrl(mediaPath) {
    try {
        if (!mediaPath) return '';
        // Предполагаем, что медиафайлы хранятся в storage/app/public
        return `/storage/${mediaPath}`;
    } catch (e) {
        console.error('Error getting media URL:', e);
        return '';
    }
}

function getMediaName(mediaPath) {
    try {
        if (!mediaPath) return 'Файл';
        // Извлекаем имя файла из пути
        return mediaPath.split('/').pop();
    } catch (e) {
        console.error('Error getting media name:', e);
        return 'Файл';
    }
}

function isImage(mediaPath) {
    try {
        if (!mediaPath) return false;
        const extension = mediaPath.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension);
    } catch (e) {
        console.error('Error checking if media is image:', e);
        return false;
    }
}
</script> 