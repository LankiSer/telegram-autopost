<template>
    <Head title="Аналитика постов" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Аналитика постов
                </h2>
                <div class="flex space-x-2">
                    <button 
                        @click="refreshAnalytics" 
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
                    <Link :href="route('statistics')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Назад к общей статистике
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="flex justify-center my-8">
                    <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ error }}</p>
                </div>
                
                <div v-else>
                    <!-- Last Update Time -->
                    <div class="mb-4 text-right text-sm text-gray-500 dark:text-gray-400">
                        Последнее обновление: {{ lastUpdateTime ? formatDateTime(lastUpdateTime) : 'Неизвестно' }}
                    </div>
                    
                    <!-- Overview Cards -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Общие показатели</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                                    <div class="text-sm text-blue-600 dark:text-blue-400">Всего постов</div>
                                    <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ analytics.totalPosts }}</div>
                                </div>
                                
                                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                                    <div class="text-sm text-green-600 dark:text-green-400">Постов в среднем в день</div>
                                    <div class="text-2xl font-bold text-green-800 dark:text-green-300">{{ analytics.avgPostsPerDay }}</div>
                                </div>
                                
                                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4">
                                    <div class="text-sm text-purple-600 dark:text-purple-400">Среднее кол-во знаков</div>
                                    <div class="text-2xl font-bold text-purple-800 dark:text-purple-300">{{ analytics.avgCharCount }}</div>
                                </div>
                                
                                <div class="bg-amber-50 dark:bg-amber-900/30 rounded-lg p-4">
                                    <div class="text-sm text-amber-600 dark:text-amber-400">Посты с медиа</div>
                                    <div class="text-2xl font-bold text-amber-800 dark:text-amber-300">{{ analytics.mediaPostsPercent }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Day of Week Distribution -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Распределение по дням недели</h3>
                                <div style="height: 300px; max-height: 300px; position: relative;">
                                    <canvas ref="dayOfWeekChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Post Length Distribution -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Распределение по длине</h3>
                                <div style="height: 300px; max-height: 300px; position: relative;">
                                    <canvas ref="lengthChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Media vs Text Posts -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Медиа vs текстовые посты</h3>
                                <div style="height: 300px; max-height: 300px; position: relative;">
                                    <canvas ref="mediaVsTextChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance by Hour -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Активность по часам</h3>
                                <div style="height: 300px; max-height: 300px; position: relative;">
                                    <canvas ref="hourlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Performance Over Time -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Динамика постов</h3>
                            <div style="height: 300px; max-height: 300px; position: relative;">
                                <canvas ref="timelineChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommendations -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Рекомендации по улучшению</h3>
                            
                            <div class="space-y-4">
                                <div v-for="(tip, index) in analytics.recommendations" :key="index" class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ tip.title }}</h4>
                                            <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                                <p>{{ tip.description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="analytics.recommendations.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    Недостаточно данных для формирования рекомендаций
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
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Chart from 'chart.js/auto';
import axios from 'axios';

const loading = ref(false);
const error = ref(null);
const lastUpdateTime = ref(null);
const analytics = ref({
    totalPosts: 0,
    avgPostsPerDay: 0,
    avgCharCount: 0,
    mediaPostsPercent: 0,
    
    dayOfWeek: {
        labels: [],
        data: []
    },
    
    postLength: {
        labels: [],
        data: []
    },
    
    mediaVsText: {
        labels: [],
        data: []
    },
    
    hourlyActivity: {
        labels: [],
        data: []
    },
    
    timeline: {
        labels: [],
        data: []
    },
    
    recommendations: []
});

const dayOfWeekChart = ref(null);
const lengthChart = ref(null);
const mediaVsTextChart = ref(null);
const hourlyChart = ref(null);
const timelineChart = ref(null);
let charts = {};

onMounted(() => {
    fetchAnalytics(false);
});

onUnmounted(() => {
    // Cleanup all charts when component is unmounted
    destroyAllCharts();
});

const destroyAllCharts = () => {
    Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
    });
    charts = {};
};

const refreshAnalytics = () => {
    // Cleanup first before fetching new data
    destroyAllCharts();
    fetchAnalytics(true);
};

const fetchAnalytics = async (forceRefresh = false) => {
    loading.value = true;
    error.value = null;
    
    try {
        // Если требуется принудительное обновление, добавляем параметр refresh=true
        const url = forceRefresh 
            ? route('statistics.posts', { refresh: true }) 
            : route('statistics.posts');
            
        const response = await axios.get(url);
        console.log("Raw post analytics data:", response.data);
        
        // Установим время последнего обновления
        lastUpdateTime.value = response.data.last_updated_at || new Date().toISOString();
        
        // Transform the API data to the structure our component expects
        analytics.value = {
            totalPosts: response.data.total_posts || 0,
            avgPostsPerDay: response.data.avg_posts_per_day || 0,
            avgCharCount: response.data.avg_char_count || 0,
            mediaPostsPercent: response.data.media_posts_percent || 0,
            
            dayOfWeek: {
                labels: Object.keys(response.data.weekday_stats || {}),
                data: Object.values(response.data.weekday_stats || {})
            },
            
            postLength: {
                labels: ['Короткие', 'Средние', 'Длинные'],
                data: response.data.length_distribution ? [
                    response.data.length_distribution.short || 0,
                    response.data.length_distribution.medium || 0,
                    response.data.length_distribution.long || 0
                ] : [0, 0, 0]
            },
            
            mediaVsText: {
                labels: ['С медиа', 'Только текст'],
                data: response.data.media_vs_text ? [
                    response.data.media_vs_text.with_media || 0,
                    response.data.media_vs_text.text_only || 0
                ] : [0, 0]
            },
            
            hourlyActivity: {
                labels: response.data.hourly_activity?.labels || Array.from({length: 24}, (_, i) => `${i}:00`),
                data: response.data.hourly_activity?.data || Array(24).fill(0)
            },
            
            timeline: {
                labels: response.data.timeline?.labels || [],
                data: response.data.timeline?.data || []
            },
            
            recommendations: response.data.recommendations || []
        };
        
        loading.value = false;
        
        // Create charts after data is loaded
        nextTick(() => {
            createCharts();
        });
    } catch (err) {
        console.error('Failed to fetch analytics:', err);
        error.value = 'Не удалось загрузить аналитику. Пожалуйста, попробуйте позже.';
        loading.value = false;
    }
};

const createCharts = () => {
    // Destroy any existing charts to prevent memory leaks and visual bugs
    Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
    });
    charts = {};
    
    if (dayOfWeekChart.value) createDayOfWeekChart();
    if (lengthChart.value) createLengthChart();
    if (mediaVsTextChart.value) createMediaVsTextChart();
    if (hourlyChart.value) createHourlyChart();
    if (timelineChart.value) createTimelineChart();
};

const createDayOfWeekChart = () => {
    const ctx = dayOfWeekChart.value.getContext('2d');
    
    charts.dayOfWeek = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: analytics.value.dayOfWeek.labels,
            datasets: [{
                label: 'Количество постов',
                data: analytics.value.dayOfWeek.data,
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
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
                    beginAtZero: true
                }
            }
        }
    });
};

const createLengthChart = () => {
    const ctx = lengthChart.value.getContext('2d');
    
    charts.length = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: analytics.value.postLength.labels,
            datasets: [{
                label: 'Количество постов',
                data: analytics.value.postLength.data,
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: 'rgba(16, 185, 129, 1)',
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
                    beginAtZero: true
                }
            }
        }
    });
};

const createMediaVsTextChart = () => {
    const ctx = mediaVsTextChart.value.getContext('2d');
    
    charts.mediaVsText = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: analytics.value.mediaVsText.labels,
            datasets: [{
                data: analytics.value.mediaVsText.data,
                backgroundColor: [
                    'rgba(248, 113, 113, 0.6)',
                    'rgba(96, 165, 250, 0.6)'
                ],
                borderColor: [
                    'rgba(248, 113, 113, 1)',
                    'rgba(96, 165, 250, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
};

const createHourlyChart = () => {
    const ctx = hourlyChart.value.getContext('2d');
    
    charts.hourly = new Chart(ctx, {
        type: 'line',
        data: {
            labels: analytics.value.hourlyActivity.labels,
            datasets: [{
                label: 'Количество постов',
                data: analytics.value.hourlyActivity.data,
                backgroundColor: 'rgba(244, 114, 182, 0.2)',
                borderColor: 'rgba(244, 114, 182, 1)',
                borderWidth: 2,
                tension: 0.3,
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
                    beginAtZero: true
                }
            }
        }
    });
};

const createTimelineChart = () => {
    const ctx = timelineChart.value.getContext('2d');
    
    charts.timeline = new Chart(ctx, {
        type: 'line',
        data: {
            labels: analytics.value.timeline.labels,
            datasets: [{
                label: 'Количество постов',
                data: analytics.value.timeline.data,
                backgroundColor: 'rgba(251, 191, 36, 0.2)',
                borderColor: 'rgba(251, 191, 36, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
};

const formatDateTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};
</script> 