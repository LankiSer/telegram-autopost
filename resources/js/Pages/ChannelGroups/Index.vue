<template>
    <Head title="Группы каналов" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Группы каналов
                </h2>
                <Link :href="route('channel-groups.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Создать группу
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

                <!-- Статистика групп -->
                <div v-if="groups.length > 0" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <div class="text-lg font-bold text-blue-600 dark:text-blue-300">{{ groups.length }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Всего групп</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <div class="text-lg font-bold text-green-600 dark:text-green-300">{{ getTotalChannels() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Всего каналов</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                            <div class="text-lg font-bold text-purple-600 dark:text-purple-300">{{ getCategoriesCount() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Категорий</div>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-900 p-4 rounded-lg">
                            <div class="text-lg font-bold text-orange-600 dark:text-orange-300">{{ getRecentGroupsCount() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Новых за неделю</div>
                        </div>
                    </div>
                </div>

                <!-- Группы в виде карточек -->
                <div v-if="groups.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="group in groups" :key="group.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg transition-all hover:shadow-md hover:translate-y-[-2px] duration-200">
                        <!-- Заголовок с категорией -->
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <Link :href="route('channel-groups.show', group.id)" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ group.name }}
                                    </Link>
                                    <div v-if="group.category" class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            {{ group.category }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ formatChannelsCount(group.channels_count) }}
                                </div>
                            </div>
                            
                            <!-- Описание группы -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ group.description && group.description.length > 120 ? 
                                   group.description.substring(0, 120) + '...' : 
                                   group.description || 'Описание отсутствует' }}
                            </p>
                            
                            <!-- Теги группы (если есть) -->
                            <div v-if="group.tags && group.tags.length" class="flex flex-wrap gap-2 mt-2 mb-3">
                                <span v-for="tag in group.tags" :key="tag" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ tag }}
                                </span>
                            </div>
                            
                            <!-- Дата создания -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Создано: {{ formatDate(group.created_at) }}
                            </div>
                        </div>
                        
                        <!-- Действия с группой -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3 flex justify-between items-center border-t border-gray-200 dark:border-gray-600">
                            <Link :href="route('channel-groups.show', group.id)" class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Подробнее
                            </Link>
                            <div class="flex space-x-2">
                                <Link :href="route('channel-groups.edit', group.id)" class="inline-flex items-center p-1.5 text-sm text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900 rounded-md hover:bg-blue-200 dark:hover:bg-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>
                                <Link :href="route('channel-groups.destroy', group.id)" method="delete" as="button" 
                                     class="inline-flex items-center p-1.5 text-sm text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900 rounded-md hover:bg-red-200 dark:hover:bg-red-800"
                                     onclick="return confirm('Вы уверены, что хотите удалить эту группу?');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Сообщение об отсутствии групп -->
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-10 text-center">
                        <div class="flex justify-center">
                            <svg class="h-20 w-20 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">У вас пока нет групп каналов</h3>
                        <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            Создайте группу, чтобы объединить ваши каналы для кросс-промо и общего управления.
                        </p>
                        <div class="mt-6">
                            <Link :href="route('channel-groups.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Создать первую группу
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    groups: Array,
});

// Функция для форматирования количества каналов с правильным склонением
const formatChannelsCount = (count) => {
    if (count === 1) return '1 канал';
    if (count >= 2 && count <= 4) return `${count} канала`;
    return `${count} каналов`;
};

// Функция для форматирования даты
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
};

// Получить общее количество каналов
const getTotalChannels = () => {
    return props.groups.reduce((total, group) => total + (group.channels_count || 0), 0);
};

// Получить количество категорий
const getCategoriesCount = () => {
    const categories = new Set();
    props.groups.forEach(group => {
        if (group.category) categories.add(group.category);
    });
    return categories.size;
};

// Получить количество групп, созданных за последнюю неделю
const getRecentGroupsCount = () => {
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
    
    return props.groups.filter(group => {
        const createdAt = new Date(group.created_at);
        return createdAt >= oneWeekAgo;
    }).length;
};
</script> 