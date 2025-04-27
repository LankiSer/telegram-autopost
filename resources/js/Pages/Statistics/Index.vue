<template>
    <Head title="Статистика" />
    
    <AppLayout title="Статистика">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Статистика
                </h2>
                <button 
                    @click="refreshStatistics" 
                    class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 flex items-center"
                    :disabled="loading"
                >
                    <span v-if="loading">
                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span v-else>
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </span>
                    {{ loading ? 'Обновление...' : 'Обновить данные' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="loader"></div>
                    <p class="ml-4 text-gray-600">Загрузка статистики...</p>
                            </div>

                <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <p>{{ error }}</p>
                    </div>

                <div v-else>
                    <!-- Last Update Time -->
                    <div class="mb-4 text-right text-sm text-gray-500">
                        Последнее обновление: {{ lastUpdateTime ? formatDateTime(lastUpdateTime) : 'Неизвестно' }}
                    </div>

                    <!-- Summary cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <!-- Total channels -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 truncate">Каналов</p>
                                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ statistics.totalChannels }}</p>
                        </div>
                    </div>
                            </div>
                        </div>
                        
                        <!-- Total posts -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 truncate">Постов</p>
                                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ statistics.totalPosts }}</p>
                        </div>
                    </div>
                            </div>
                        </div>
                        
                        <!-- Posts by status -->
                        <div class="col-span-1 md:col-span-2">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
                                <div class="p-6 bg-white h-full">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Статус постов</h3>
                                    <div class="flex flex-wrap gap-4">
                                        <div class="flex items-center bg-green-50 rounded-lg px-4 py-2">
                                            <div class="rounded-full h-8 w-8 flex items-center justify-center bg-green-500 text-white mr-2">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Отправлено</p>
                                                <p class="text-lg font-semibold text-gray-900">{{ statistics.sentPosts }}</p>
                        </div>
                    </div>
                    
                                        <div class="flex items-center bg-yellow-50 rounded-lg px-4 py-2">
                                            <div class="rounded-full h-8 w-8 flex items-center justify-center bg-yellow-500 text-white mr-2">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Запланировано</p>
                                                <p class="text-lg font-semibold text-gray-900">{{ statistics.scheduledPosts }}</p>
                        </div>
                    </div>
                    
                                        <div class="flex items-center bg-red-50 rounded-lg px-4 py-2">
                                            <div class="rounded-full h-8 w-8 flex items-center justify-center bg-red-500 text-white mr-2">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                <div>
                                                <p class="text-sm text-gray-600">Ошибки</p>
                                                <p class="text-lg font-semibold text-gray-900">{{ statistics.failedPosts }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                
                    <!-- Charts section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Activity by day of week -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Активность по дням недели</h3>
                                <div style="height: 300px; max-height: 300px;">
                                    <canvas ref="weekdayChart"></canvas>
                        </div>
                                <div class="mt-4 text-center">
                                    <Link :href="route('statistics.posts')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        Подробный анализ постов →
                                    </Link>
                    </div>
                            </div>
                        </div>
                        
                        <!-- Content type distribution -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Типы контента</h3>
                                <div style="height: 300px; max-height: 300px;">
                                    <canvas ref="contentTypeChart"></canvas>
                                </div>
                        </div>
                    </div>
                </div>
                
                    <!-- Top channels -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Топ каналов по активности</h3>
                            
                            <div v-if="statistics.topChannels.length === 0" class="text-center py-8 text-gray-500">
                                У вас еще нет активных каналов
                            </div>
                            
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Канал</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Посты</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Последняя активность</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="channel in statistics.topChannels" :key="channel.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                                                        {{ channel.name.charAt(0).toUpperCase() }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ channel.name }}</div>
                                                        <div class="text-sm text-gray-500">@{{ channel.username }}</div>
                            </div>
                        </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ channel.post_count }} постов</div>
                                                <div class="text-xs text-gray-500">{{ Math.round(channel.post_count / statistics.totalPosts * 100) || 0 }}% от общего числа</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(channel.last_activity) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <Link :href="route('statistics.channel.detail', { channel: channel.id })" class="text-indigo-600 hover:text-indigo-900">
                                                    Детали
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent activity -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Недавняя активность</h3>
                            
                            <div v-if="statistics.recentActivity.length === 0" class="text-center py-8 text-gray-500">
                                Нет недавней активности
                </div>
                
                            <div v-else class="space-y-4">
                                <div v-for="activity in statistics.recentActivity" :key="activity.id" class="border-l-4 pl-4" :class="getActivityBorderClass(activity.type)">
                                    <div class="flex items-start">
                                        <div :class="getActivityIconClass(activity.type)" class="rounded-full p-2 mr-3 mt-1">
                                            <svg class="h-4 w-4 text-white" :viewBox="getActivityIcon(activity.type).viewBox" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActivityIcon(activity.type).path"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-700">{{ activity.message }}</p>
                                            <div class="mt-1 flex items-center text-xs text-gray-500">
                                                <span>{{ formatDate(activity.created_at) }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ activity.channel_name }}</span>
                        </div>
                    </div>
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import Chart from 'chart.js/auto';

const loading = ref(false);
const error = ref(null);
const lastUpdateTime = ref(null);
const statistics = ref({
            totalChannels: 0,
            totalPosts: 0,
    sentPosts: 0,
    scheduledPosts: 0,
    failedPosts: 0,
    weekdayDistribution: {},
    contentTypeDistribution: {},
    topChannels: [],
    recentActivity: []
});

// Chart references
const weekdayChart = ref(null);
const contentTypeChart = ref(null);

// Chart instances
let weekdayChartInstance = null;
let contentTypeChartInstance = null;

onMounted(async () => {
    fetchStatistics(false);
});

// Cleanup charts when component is unmounted
onUnmounted(() => {
    cleanupCharts();
});

const cleanupCharts = () => {
    if (weekdayChartInstance) {
        weekdayChartInstance.destroy();
        weekdayChartInstance = null;
    }
    
    if (contentTypeChartInstance) {
        contentTypeChartInstance.destroy();
        contentTypeChartInstance = null;
    }
};

const refreshStatistics = () => {
    // First cleanup existing charts
    cleanupCharts();
    fetchStatistics(true);
};

const fetchStatistics = async (forceRefresh = false) => {
    loading.value = true;
    error.value = null;
    
    try {
        // Если требуется принудительное обновление, добавляем параметр refresh=true
        const url = forceRefresh 
            ? route('statistics', { refresh: true }) 
            : route('statistics');
            
        const response = await axios.get(url);
        
        // Установим время последнего обновления
        lastUpdateTime.value = response.data.last_updated_at || new Date().toISOString();
        
        // Transform the data to match our component structure
        if (response.data) {
            statistics.value = {
                totalChannels: response.data.total_channels || 0,
                totalPosts: response.data.total_posts || 0,
                sentPosts: response.data.sent_posts || 0,
                scheduledPosts: response.data.scheduled_posts || 0,
                failedPosts: response.data.failed_posts || 0,
                weekdayDistribution: response.data.weekday_distribution || { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0, 6: 0, 7: 0 },
                contentTypeDistribution: response.data.content_type_distribution || {
                    text_only: 70,
                    with_image: 20,
                    with_video: 5,
                    with_file: 5
                },
                topChannels: (response.data.top_channels || []).map(channel => ({
                    id: channel.id,
                    name: channel.name || 'Неизвестно',
                    username: channel.username || channel.telegram_id || 'unknown',
                    post_count: channel.post_count || 0,
                    last_activity: channel.last_activity || new Date().toISOString()
                })),
                recentActivity: response.data.recent_activity || []
            };
        }
        
        loading.value = false;
        
        // Create charts after data is loaded and DOM is updated
        nextTick(() => {
            // First check if chart elements exist in DOM
            if (weekdayChart.value) {
                createWeekdayChart();
            }
            
            if (contentTypeChart.value) {
                createContentTypeChart();
            }
        });
    } catch (err) {
        console.error('Failed to load statistics:', err);
        error.value = 'Не удалось загрузить статистику. Пожалуйста, попробуйте позже.';
        loading.value = false;
    }
};

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return 'Нет данных';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

const formatDateTime = (dateString) => {
    if (!dateString) return 'Неизвестно';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Create weekday distribution chart
const createWeekdayChart = () => {
    if (!weekdayChart.value) return;
    
    const ctx = weekdayChart.value.getContext('2d');
    
    // Cleanup old instance if exists
    if (weekdayChartInstance) {
        weekdayChartInstance.destroy();
    }
    
    // Prepare data for weekday chart
    const weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    const weekdayData = weekdays.map((_, index) => {
        // Converting from JS 0-indexed (Sunday = 0) to 1-indexed (Monday = 1)
        const dayNum = index + 1;
        return statistics.value.weekdayDistribution[dayNum] || 0;
    });
    
    weekdayChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: weekdays,
            datasets: [{
                label: 'Количество постов',
                data: weekdayData,
                backgroundColor: 'rgba(79, 70, 229, 0.5)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
            }]
        },
            options: {
                responsive: true,
            maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                        precision: 0
                    }
                    }
                }
            }
        });
};

// Create content type distribution chart
const createContentTypeChart = () => {
    if (!contentTypeChart.value) return;
    
    const ctx = contentTypeChart.value.getContext('2d');
    const distributionData = statistics.value.contentTypeDistribution;
    
    // Cleanup old instance if exists
    if (contentTypeChartInstance) {
        contentTypeChartInstance.destroy();
    }
    
    contentTypeChartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(distributionData).map(key => {
                switch(key) {
                    case 'text_only': return 'Только текст';
                    case 'with_image': return 'С изображениями';
                    case 'with_video': return 'С видео';
                    case 'with_file': return 'С файлами';
                    default: return key;
                }
            }),
            datasets: [{
                data: Object.values(distributionData),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
            options: {
                responsive: true,
            maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                        label: (context) => {
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                    }
                }
            }
        });
};

// Get activity border class based on activity type
const getActivityBorderClass = (type) => {
    switch (type) {
        case 'post_sent': return 'border-green-500';
        case 'post_scheduled': return 'border-yellow-500';
        case 'post_failed': return 'border-red-500';
        case 'channel_created': return 'border-blue-500';
        case 'channel_updated': return 'border-purple-500';
        default: return 'border-gray-500';
    }
};

// Get activity icon class based on activity type
const getActivityIconClass = (type) => {
    switch (type) {
        case 'post_sent': return 'bg-green-500';
        case 'post_scheduled': return 'bg-yellow-500';
        case 'post_failed': return 'bg-red-500';
        case 'channel_created': return 'bg-blue-500';
        case 'channel_updated': return 'bg-purple-500';
        default: return 'bg-gray-500';
    }
};

// Get activity icon based on activity type
const getActivityIcon = (type) => {
    switch (type) {
        case 'post_sent':
            return {
                viewBox: '0 0 24 24',
                path: 'M5 13l4 4L19 7'
            };
        case 'post_scheduled':
            return {
                viewBox: '0 0 24 24',
                path: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
            };
        case 'post_failed':
            return {
                viewBox: '0 0 24 24',
                path: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            };
        case 'channel_created':
            return {
                viewBox: '0 0 24 24',
                path: 'M12 4v16m8-8H4'
            };
        case 'channel_updated':
            return {
                viewBox: '0 0 24 24',
                path: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'
            };
        default:
            return {
                viewBox: '0 0 24 24',
                path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            };
    }
};
</script> 

<style scoped>
.loader {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    border-top: 4px solid #3498db;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style> 