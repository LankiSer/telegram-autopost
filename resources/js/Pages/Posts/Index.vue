<template>
    <Head title="Мои посты" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Мои посты
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
                <!-- Уведомления -->
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm dark:bg-green-900 dark:text-green-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ $page.props.flash.success }}</p>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.flash?.error || error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm dark:bg-red-900 dark:text-red-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ $page.props.flash?.error || error }}</p>
                        </div>
                    </div>
                </div>

                <!-- Фильтры и статистика -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Фильтры -->
                        <div class="lg:col-span-3">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Фильтры</h3>
                            <form @submit.prevent="applyFilters" class="space-y-4 md:space-y-0 md:grid md:grid-cols-3 md:gap-4">
                                <div>
                                    <label for="channel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Канал</label>
                                    <select id="channel" v-model="filters.channel" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Все каналы</option>
                                        <option v-for="channel in channels" :key="channel.id" :value="channel.id">{{ channel.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Статус</label>
                                    <select id="status" v-model="filters.status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Все статусы</option>
                                        <option value="draft">Черновик</option>
                                        <option value="scheduled">Запланирован</option>
                                        <option value="published">Опубликован</option>
                                        <option value="failed">Ошибка</option>
                                    </select>
                                </div>
                                <div class="flex items-end space-x-2">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        Применить
                                    </button>
                                    <button type="button" @click="resetFilters" class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                        Сбросить
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Статистика -->
                        <div class="col-span-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Статистика</h3>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Всего постов:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ postsWithLinks?.total || 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Опубликовано:</span>
                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                            {{ postsWithLinks?.data?.filter(post => post.status === 'published').length || 0 }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Запланировано:</span>
                                        <span class="font-semibold text-yellow-600 dark:text-yellow-400">
                                            {{ postsWithLinks?.data?.filter(post => post.status === 'scheduled').length || 0 }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Черновики:</span>
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">
                                            {{ postsWithLinks?.data?.filter(post => post.status === 'draft').length || 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список постов в виде карточек -->
                <div v-if="postsWithLinks && postsWithLinks.data && postsWithLinks.data.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="post in postsWithLinks.data" :key="post.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg transition-all duration-200 hover:shadow-md hover:translate-y-[-2px]">
                            <div class="p-5">
                                <!-- Заголовок поста и канал -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <Link :href="route('posts.show', post.id)" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ truncateText(post.title || 'Без названия', 50) }}
                                        </Link>
                                        <div class="mt-1">
                                            <Link :href="route('channels.show', post.channel.id)" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ post.channel.name }}
                                            </Link>
                                        </div>
                                    </div>
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
                                
                                <!-- Контент и дата -->
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <div v-if="post.scheduled_at && post.status === 'scheduled'" class="mb-2">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Запланирован на: {{ formatDateTime(post.scheduled_at) }}
                                    </div>
                                    <div v-if="post.published_at && post.status === 'published'" class="mb-2">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Опубликован: {{ formatDateTime(post.published_at) }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Создан: {{ formatDate(post.created_at) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Действия с постом -->
                            <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3 flex justify-between items-center">
                                <div>
                                    <div v-if="post.status === 'published' && post.views_count !== undefined" class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ post.views_count || 0 }} просмотров
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <Link :href="route('posts.edit', post.id)" class="inline-flex items-center p-1.5 text-sm text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900 rounded-md hover:bg-blue-200 dark:hover:bg-blue-800" title="Редактировать">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <Link :href="route('posts.show', post.id)" class="inline-flex items-center p-1.5 text-sm text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900 rounded-md hover:bg-green-200 dark:hover:bg-green-800" title="Просмотреть">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <button @click="confirmDelete(post.id)" class="inline-flex items-center p-1.5 text-sm text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900 rounded-md hover:bg-red-200 dark:hover:bg-red-800" title="Удалить">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Пагинация -->
                    <div class="mt-6" v-if="postsWithLinks.links && postsWithLinks.links.length > 3">
                        <div class="flex flex-wrap justify-center">
                            <template v-for="(link, i) in postsWithLinks.links">
                                <Link v-if="link && link.url !== null" 
                                      :key="i" 
                                      :href="link.url" 
                                      :class="{'bg-blue-600 text-white': link.active, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': !link.active}" 
                                      class="mx-1 px-4 py-2 rounded text-sm transition-colors duration-150" 
                                      v-html="link.label">
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Сообщение об отсутствии постов -->
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-10 text-center">
                        <div class="flex justify-center">
                            <svg class="h-20 w-20 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Постов не найдено</h3>
                        <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            У вас пока нет постов или по выбранным параметрам фильтрации ничего не найдено.
                        </p>
                        <div class="mt-6">
                            <Link :href="route('posts.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Создать первый пост
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    posts: {
        type: Object,
        default: () => ({
            data: [],
            links: []
        })
    },
    channels: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({
            channel: '',
            status: ''
        })
    },
    error: {
        type: String,
        default: null
    }
});

// Make sure posts.links exists to prevent errors
const postsWithLinks = computed(() => {
    if (!props.posts) return { data: [], links: [] };
    if (!props.posts.links) return { ...props.posts, links: [] };
    return props.posts;
});

const filters = reactive({
    channel: props.filters.channel || '',
    status: props.filters.status || ''
});

function applyFilters() {
    router.get(route('posts.index'), {
        channel: filters.channel || undefined,
        status: filters.status || undefined
    }, {
        preserveState: true,
        replace: true,
        only: ['posts']
    });
}

function resetFilters() {
    filters.channel = '';
    filters.status = '';
    applyFilters();
}

function confirmDelete(postId) {
    if (!postId) return;
    
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

function formatDateTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU') + ' ' + date.toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'});
}

function getStatusText(status) {
    const statusMap = {
        'draft': 'Черновик',
        'scheduled': 'Запланирован',
        'published': 'Опубликован',
        'failed': 'Ошибка'
    };
    return statusMap[status] || status;
}
</script> 