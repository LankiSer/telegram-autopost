<template>
    <Head :title="`Группа: ${group.name}`" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    {{ group.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link 
                        :href="route('channel-groups.edit', group.id)" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Редактировать
                    </Link>
                    <Link 
                        :href="route('channel-groups.index')" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Назад к списку
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Общая информация -->
                            <div class="col-span-1 lg:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Информация о группе</h3>
                                
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">Название</h4>
                                            <p class="mt-1">{{ group.name }}</p>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">Категория</h4>
                                            <p class="mt-1">{{ group.category || 'Не указана' }}</p>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">Дата создания</h4>
                                            <p class="mt-1">{{ formatDate(group.created_at) }}</p>
                                        </div>

                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">Последнее обновление</h4>
                                            <p class="mt-1">{{ formatDate(group.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="group.description" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700">Описание</h4>
                                    <div class="mt-1 bg-gray-50 p-4 rounded-lg whitespace-pre-line">{{ group.description }}</div>
                                </div>
                            </div>

                            <!-- Статистика -->
                            <div class="col-span-1">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Статистика группы</h3>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="space-y-4">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">Каналы</h4>
                                            <p class="mt-1 text-2xl font-semibold">
                                                {{ formatChannelsCount(channels ? channels.length : 0) }}
                                            </p>
                                        </div>
                                        
                                        <div v-if="statistics">
                                            <h4 class="text-sm font-medium text-gray-700">Последние посты</h4>
                                            <p class="mt-1 text-2xl font-semibold">{{ statistics.posts_count || 0 }}</p>
                                        </div>
                                        
                                        <div v-if="statistics">
                                            <h4 class="text-sm font-medium text-gray-700">Общий охват</h4>
                                            <p class="mt-1 text-2xl font-semibold">{{ statistics.total_reach || 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Каналы в группе -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Каналы в группе</h3>
                        
                        <div v-if="channels && channels.length > 0">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div 
                                    v-for="channel in channels" 
                                    :key="channel.id"
                                    class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                                >
                                    <div class="flex justify-between mb-2">
                                        <h4 class="font-medium">{{ channel.name }}</h4>
                                        <span v-if="channel.type" class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">{{ channel.type }}</span>
                                    </div>
                                    
                                    <div v-if="channel.telegram_username" class="text-sm text-gray-600 mb-1">
                                        @{{ channel.telegram_username }}
                                    </div>
                                    
                                    <div v-if="channel.description" class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ channel.description }}
                                    </div>
                                    
                                    <div class="mt-3 text-xs text-gray-500 flex justify-between">
                                        <div v-if="channel.members_count">
                                            <span class="font-medium">{{ channel.members_count }}</span> подписчиков
                                        </div>
                                        <div>
                                            <Link 
                                                :href="route('channels.show', channel.id)" 
                                                class="text-blue-600 hover:underline"
                                            >
                                                Подробнее
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-4 bg-yellow-50 text-yellow-800 rounded-lg">
                            <p>В этой группе пока нет каналов. <Link :href="route('channel-groups.edit', group.id)" class="text-blue-600 hover:underline">Добавьте каналы</Link> в группу.</p>
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

const props = defineProps({
    group: Object,
    channels: Array,
    statistics: Object,
});

// Форматирование даты
const formatDate = (date) => {
    if (!date) return 'Нет данных';
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString('ru-RU');
};

// Форматирование числа каналов с правильным склонением
const formatChannelsCount = (count) => {
    if (count === 0) return 'Нет каналов';
    
    const lastDigit = count % 10;
    const lastTwoDigits = count % 100;
    
    if (lastDigit === 1 && lastTwoDigits !== 11) {
        return `${count} канал`;
    } else if ([2, 3, 4].includes(lastDigit) && ![12, 13, 14].includes(lastTwoDigits)) {
        return `${count} канала`;
    } else {
        return `${count} каналов`;
    }
};
</script> 