<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { format, parse } from 'date-fns';
import { ru } from 'date-fns/locale';

const props = defineProps({
    channels: {
        type: Array,
        default: () => []
    },
    scheduledPosts: {
        type: Array,
        default: () => []
    },
    publishedPosts: {
        type: Array,
        default: () => []
    },
    draftPosts: {
        type: Array,
        default: () => []
    },
    selectedDate: {
        type: String,
        default: () => format(new Date(), 'yyyy-MM-dd')
    },
    monthlyStats: {
        type: Object,
        default: () => ({})
    }
});

const activeTab = ref('scheduled');
const selectedChannel = ref('all');

const displayDate = computed(() => {
    try {
        const date = parse(props.selectedDate, 'yyyy-MM-dd', new Date());
        return format(date, 'd MMMM yyyy', { locale: ru });
    } catch (e) {
        return props.selectedDate;
    }
});

const filteredPosts = computed(() => {
    let posts = [];
    
    if (activeTab.value === 'scheduled') {
        posts = props.scheduledPosts;
    } else if (activeTab.value === 'published') {
        posts = props.publishedPosts;
    } else if (activeTab.value === 'draft') {
        posts = props.draftPosts;
    }
    
    if (selectedChannel.value !== 'all') {
        posts = posts.filter(post => post.channel.id.toString() === selectedChannel.value);
    }
    
    return posts;
});
</script>

<template>
    <Head title="Планировщик" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Планировщик
                </h2>
                <Link :href="route('posts.create')" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 px-4 py-2 rounded-md text-white text-sm font-medium inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Создать пост
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Боковая панель с календарем и фильтрами -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="mb-6">
                                <h3 class="font-medium text-lg text-gray-900 dark:text-white mb-4">Календарь</h3>
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md text-center">
                                    <div class="text-gray-800 dark:text-gray-200 font-medium">{{ displayDate }}</div>
                                    <Link :href="route('scheduler.index')" class="mt-2 text-blue-600 dark:text-blue-400 text-sm">
                                        Сегодня
                                    </Link>
                                </div>
                                <div class="mt-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    Полный календарь будет добавлен в ближайшем обновлении
                                </div>
                            </div>

                            <div class="mb-4">
                                <h3 class="font-medium text-lg text-gray-900 dark:text-white mb-4">Фильтры</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Канал
                                        </label>
                                        <select 
                                            v-model="selectedChannel"
                                            class="rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 block w-full"
                                        >
                                            <option value="all">Все каналы</option>
                                            <option v-for="channel in channels" :key="channel.id" :value="channel.id.toString()">
                                                {{ channel.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="font-medium text-lg text-gray-900 dark:text-white mb-4">Статистика</h3>
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md">
                                    <div class="grid grid-cols-2 gap-4 text-center">
                                        <div>
                                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ scheduledPosts.length }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Запланировано</div>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ publishedPosts.length }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Опубликовано</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Основная область с постами -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-medium text-lg text-gray-900 dark:text-white">{{ displayDate }}</h3>
                                <div class="flex space-x-2">
                                    <Link 
                                        :href="route('scheduler.index', { date: format(new Date(new Date(props.selectedDate).setDate(new Date(props.selectedDate).getDate() - 1)), 'yyyy-MM-dd') })"
                                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm"
                                    >
                                        Пред.
                                    </Link>
                                    <Link 
                                        :href="route('scheduler.index', { date: format(new Date(new Date(props.selectedDate).setDate(new Date(props.selectedDate).getDate() + 1)), 'yyyy-MM-dd') })"
                                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm"
                                    >
                                        След.
                                    </Link>
                                </div>
                            </div>

                            <div class="mb-6">
                                <div class="border-b border-gray-200 dark:border-gray-700">
                                    <nav class="-mb-px flex space-x-8">
                                        <button
                                            @click="activeTab = 'scheduled'"
                                            :class="[
                                                activeTab === 'scheduled' 
                                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center'
                                            ]"
                                        >
                                            Запланированные
                                            <span 
                                                :class="[
                                                    activeTab === 'scheduled' 
                                                        ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400'
                                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                                    'ml-2 py-0.5 px-2 rounded-full text-xs'
                                                ]"
                                            >
                                                {{ scheduledPosts.length }}
                                            </span>
                                        </button>
                                        <button
                                            @click="activeTab = 'published'"
                                            :class="[
                                                activeTab === 'published' 
                                                    ? 'border-green-500 text-green-600 dark:text-green-400'
                                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center'
                                            ]"
                                        >
                                            Опубликованные
                                            <span 
                                                :class="[
                                                    activeTab === 'published' 
                                                        ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400'
                                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                                    'ml-2 py-0.5 px-2 rounded-full text-xs'
                                                ]"
                                            >
                                                {{ publishedPosts.length }}
                                            </span>
                                        </button>
                                        <button
                                            @click="activeTab = 'draft'"
                                            :class="[
                                                activeTab === 'draft' 
                                                    ? 'border-gray-500 text-gray-600 dark:text-gray-400'
                                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center'
                                            ]"
                                        >
                                            Черновики
                                            <span 
                                                :class="[
                                                    activeTab === 'draft' 
                                                        ? 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300'
                                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                                    'ml-2 py-0.5 px-2 rounded-full text-xs'
                                                ]"
                                            >
                                                {{ draftPosts.length }}
                                            </span>
                                        </button>
                                    </nav>
                                </div>
                            </div>

                            <!-- Список постов -->
                            <div v-if="filteredPosts.length > 0" class="space-y-4">
                                <div v-for="post in filteredPosts" :key="post.id" class="flex items-start justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-start space-x-4">
                                        <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-md flex items-center justify-center w-12 h-12">
                                            <svg v-if="post.hasMedia" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ post.title }}</h4>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ post.channel.name }}</span>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                                <span v-if="post.time" class="text-sm text-gray-600 dark:text-gray-400">{{ post.time }}</span>
                                                <span v-if="post.updated_at" class="text-sm text-gray-600 dark:text-gray-400">{{ post.updated_at }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div v-if="post.views" class="text-sm text-gray-600 dark:text-gray-400 mr-4">
                                            <span class="font-medium">{{ post.views }}</span> 
                                            <span class="text-gray-500">просмотров</span>
                                        </div>
                                        <div>
                                            <Link v-if="post.status === 'draft'" :href="route('posts.edit', post.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <Link v-else :href="route('posts.show', post.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Сообщение об отсутствии постов -->
                            <div v-else class="bg-gray-50 dark:bg-gray-700 p-6 text-center rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2 text-gray-600 dark:text-gray-400">
                                    {{ 
                                        activeTab === 'scheduled' 
                                            ? 'На выбранную дату нет запланированных постов' 
                                            : activeTab === 'published' 
                                                ? 'На выбранную дату нет опубликованных постов' 
                                                : 'У вас пока нет черновиков' 
                                    }}
                                </p>
                                <Link :href="route('posts.create')" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
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