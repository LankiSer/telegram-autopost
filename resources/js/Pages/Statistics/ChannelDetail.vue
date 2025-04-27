<template>
    <Head :title="`Статистика канала ${channel?.name || ''}`" />
    
    <AppLayout :title="`Статистика канала ${channel?.name || ''}`">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Статистика канала {{ channel?.name || '' }}
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
                <div class="mb-5">
                    <Link :href="route('statistics')" class="flex items-center text-indigo-600 hover:text-indigo-900">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Назад к общей статистике
                    </Link>
                </div>

                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="loader"></div>
                    <p class="ml-4 text-gray-600">Загрузка статистики канала...</p>
                </div>

                <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <p>{{ error }}</p>
                </div>

                <div v-else>
                    <!-- Last Update Time -->
                    <div class="mb-4 text-right text-sm text-gray-500">
                        Последнее обновление: {{ lastUpdateTime ? formatDateTime(lastUpdateTime) : 'Неизвестно' }}
                    </div>
                    
                    <!-- Channel info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white">
                            <div class="flex items-center">
                                <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center text-3xl text-indigo-600 font-bold">
                                    {{ channel.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ channel.name }}</h3>
                                    <div class="mt-1 flex items-center space-x-4">
                                        <div class="text-sm text-gray-500">
                                            Telegram ID: {{ channel.telegram_id }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Участников: {{ statistics.subscribers || 'Н/Д' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
                                        <p class="text-sm font-medium text-gray-500 truncate">Всего постов</p>
                                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ statistics.total_posts }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sent posts -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 truncate">Отправлено</p>
                                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ statistics.sent_posts }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Scheduled posts -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 truncate">Запланировано</p>
                                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ statistics.scheduled_posts }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Failed posts -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-red-100 text-red-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 truncate">Ошибки</p>
                                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ statistics.failed_posts }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Hourly distribution -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Распределение постов по часам</h3>
                                <div style="height: 300px; max-height: 300px;">
                                    <canvas ref="hourlyChart"></canvas>
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

                    <!-- Activity over time -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Активность по дням</h3>
                            <div style="height: 200px; max-height: 200px;">
                                <canvas ref="activityChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Last posts -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Последние посты</h3>
                            
                            <div v-if="statistics.recent_posts && statistics.recent_posts.length === 0" class="text-center py-8 text-gray-500">
                                Нет постов для отображения
                            </div>
                            
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Содержимое</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Медиа</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="post in statistics.recent_posts" :key="post.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(post.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusClass(post.status)">
                                                    {{ getStatusText(post.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <div class="max-w-xs truncate">{{ post.content }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div v-if="post.has_media" class="flex items-center text-green-600">
                                                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Да
                                                </div>
                                                <div v-else>Нет</div>
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

<script setup>
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import Chart from 'chart.js/auto';

const props = defineProps({
    channel_id: {
        type: Number,
        required: true
    }
});

const loading = ref(false);
const error = ref(null);
const lastUpdateTime = ref(null);
const channel = ref({
    id: null,
    name: '',
    telegram_id: '',
    username: ''
});
const statistics = ref({
    total_posts: 0,
    sent_posts: 0,
    scheduled_posts: 0,
    failed_posts: 0,
    subscribers: 0,
    hourly_distribution: {},
    content_type_distribution: {},
    activity_data: {},
    recent_posts: []
});

// Chart references
const hourlyChart = ref(null);
const contentTypeChart = ref(null);
const activityChart = ref(null);

// Chart instances
let hourlyChartInstance = null;
let contentTypeChartInstance = null;
let activityChartInstance = null;

onMounted(async () => {
    fetchStatistics(false);
});

// Cleanup charts when component is unmounted
onUnmounted(() => {
    cleanupCharts();
});

const cleanupCharts = () => {
    if (hourlyChartInstance) {
        hourlyChartInstance.destroy();
        hourlyChartInstance = null;
    }
    
    if (contentTypeChartInstance) {
        contentTypeChartInstance.destroy();
        contentTypeChartInstance = null;
    }
    
    if (activityChartInstance) {
        activityChartInstance.destroy();
        activityChartInstance = null;
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
            ? route('statistics.channel.detail', { channel: props.channel_id, refresh: true }) 
            : route('statistics.channel.detail', { channel: props.channel_id });
            
        const response = await axios.get(url);
        
        // Установим время последнего обновления
        lastUpdateTime.value = response.data.last_updated_at || new Date().toISOString();
        
        if (response.data) {
            // Set channel data
            channel.value = response.data.channel || {
                id: props.channel_id,
                name: 'Неизвестный канал',
                telegram_id: 'N/A'
            };
            
            // Set statistics data
            statistics.value = {
                total_posts: response.data.total_posts || 0,
                sent_posts: response.data.sent_posts || 0,
                scheduled_posts: response.data.scheduled_posts || 0,
                failed_posts: response.data.failed_posts || 0,
                subscribers: response.data.subscribers || 0,
                hourly_distribution: response.data.hourly_distribution || {},
                content_type_distribution: response.data.content_type_distribution || {
                    text_only: 70,
                    with_image: 20,
                    with_video: 5,
                    with_file: 5
                },
                activity_data: response.data.activity_data || {},
                recent_posts: response.data.recent_posts || []
            };
        }
        
        loading.value = false;
        
        // Create charts after data is loaded and DOM is updated
        nextTick(() => {
            // First check if chart elements exist in DOM
            if (hourlyChart.value) {
                createHourlyChart();
            }
            
            if (contentTypeChart.value) {
                createContentTypeChart();
            }
            
            if (activityChart.value) {
                createActivityChart();
            }
        });
    } catch (err) {
        console.error('Failed to load channel statistics:', err);
        error.value = 'Не удалось загрузить статистику канала. Пожалуйста, попробуйте позже.';
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

// Get status text based on status code
const getStatusText = (status) => {
    switch (status) {
        case 'sent': return 'Отправлен';
        case 'scheduled': return 'Запланирован';
        case 'failed': return 'Ошибка';
        case 'draft': return 'Черновик';
        case 'pending': return 'В ожидании';
        default: return 'Неизвестно';
    }
};

// Get status class based on status code
const getStatusClass = (status) => {
    switch (status) {
        case 'sent': return 'bg-green-100 text-green-800';
        case 'scheduled': return 'bg-yellow-100 text-yellow-800';
        case 'failed': return 'bg-red-100 text-red-800';
        case 'draft': return 'bg-gray-100 text-gray-800';
        case 'pending': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

// Create hourly distribution chart
const createHourlyChart = () => {
    if (!hourlyChart.value) return;
    
    const ctx = hourlyChart.value.getContext('2d');
    
    // Cleanup old instance if exists
    if (hourlyChartInstance) {
        hourlyChartInstance.destroy();
    }
    
    // Prepare data for hourly chart
    const hours = Array.from({length: 24}, (_, i) => i);
    const hourlyData = hours.map(hour => {
        return statistics.value.hourly_distribution[hour] || 0;
    });
    
    // Format hour labels (0-23)
    const hourLabels = hours.map(hour => {
        return `${hour}:00`;
    });
    
    hourlyChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hourLabels,
            datasets: [{
                label: 'Количество постов',
                data: hourlyData,
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
    const distributionData = statistics.value.content_type_distribution;
    
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

// Create activity over time chart
const createActivityChart = () => {
    if (!activityChart.value) return;
    
    const ctx = activityChart.value.getContext('2d');
    
    // Cleanup old instance if exists
    if (activityChartInstance) {
        activityChartInstance.destroy();
    }
    
    // Get activity data
    const activityData = statistics.value.activity_data;
    const dates = Object.keys(activityData).sort();
    const postCounts = dates.map(date => activityData[date] || 0);
    
    activityChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                // Format the date for display (DD.MM)
                const [year, month, day] = date.split('-');
                return `${day}.${month}`;
            }),
            datasets: [{
                label: 'Количество постов',
                data: postCounts,
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
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