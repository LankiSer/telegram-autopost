<template>
    <Head title="Статистика" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Статистика
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Основные карточки со статистикой -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Всего каналов</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalChannels) }}</h3>
                                <p class="text-sm text-green-500 dark:text-green-400 mt-2" v-if="channelTrend > 0">+{{ channelTrend }}% с прошлого месяца</p>
                                <p class="text-sm text-red-500 dark:text-red-400 mt-2" v-else-if="channelTrend < 0">{{ channelTrend }}% с прошлого месяца</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Опубликовано постов</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalPosts) }}</h3>
                                <p class="text-sm text-green-500 dark:text-green-400 mt-2" v-if="postTrend > 0">+{{ postTrend }}% с прошлого месяца</p>
                                <p class="text-sm text-red-500 dark:text-red-400 mt-2" v-else-if="postTrend < 0">{{ postTrend }}% с прошлого месяца</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Подписчиков</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalSubscribers) }}</h3>
                                <p class="text-sm text-green-500 dark:text-green-400 mt-2" v-if="subscriberTrend > 0">+{{ subscriberTrend }}% с прошлого месяца</p>
                                <p class="text-sm text-red-500 dark:text-red-400 mt-2" v-else-if="subscriberTrend < 0">{{ subscriberTrend }}% с прошлого месяца</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Просмотры</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalViews) }}</h3>
                                <p class="text-sm text-green-500 dark:text-green-400 mt-2" v-if="viewTrend > 0">+{{ viewTrend }}% с прошлого месяца</p>
                                <p class="text-sm text-red-500 dark:text-red-400 mt-2" v-else-if="viewTrend < 0">{{ viewTrend }}% с прошлого месяца</p>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Табы для переключения между различными типами данных -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="flex border-b border-gray-200 dark:border-gray-700">
                        <button @click="activeTab = 'general'" 
                                class="px-6 py-3 text-sm font-medium transition-colors"
                                :class="activeTab === 'general' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                            Общее
                        </button>
                        <button @click="activeTab = 'posts'" 
                                class="px-6 py-3 text-sm font-medium transition-colors"
                                :class="activeTab === 'posts' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                            Публикации
                        </button>
                        <button @click="activeTab = 'audience'" 
                                class="px-6 py-3 text-sm font-medium transition-colors"
                                :class="activeTab === 'audience' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                            Аудитория
                        </button>
                        <button @click="activeTab = 'engagement'" 
                                class="px-6 py-3 text-sm font-medium transition-colors"
                                :class="activeTab === 'engagement' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                            Вовлеченность
                        </button>
                    </div>
                </div>

                <!-- Контент главных табов -->
                <div v-show="activeTab === 'general'">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Просмотры за последние 30 дней</h3>
                        
                        <!-- Используем canvas для графика просмотров -->
                        <div class="h-64 mt-4">
                            <canvas ref="viewsChartRef"></canvas>
                        </div>
                    </div>
                    
                    <!-- Дополнительные графики -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Распределение контента -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Распределение контента</h3>
                            <div class="h-64">
                                <canvas ref="contentDistributionRef"></canvas>
                            </div>
                        </div>
                        
                        <!-- Рост аудитории -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Рост аудитории</h3>
                            <div class="h-64">
                                <canvas ref="audienceGrowthRef"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Метрики вовлеченности по каналам -->
                    <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Метрики вовлеченности по каналам</h3>
                        <div class="h-64">
                            <canvas ref="engagementMetricsRef"></canvas>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Недавние публикации</h3>
                        
                        <div v-if="stats.recentPosts && stats.recentPosts.length > 0" class="space-y-4">
                            <div v-for="post in stats.recentPosts" :key="post.id" class="flex items-start justify-between pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ truncateText(post.title || post.content, 50) }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ post.channel ? post.channel.name : 'Канал' }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ post.time || post.published_at }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ formatNumber(post.views || 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Нет данных</h4>
                            <p class="text-gray-500 dark:text-gray-400">Здесь будут отображаться ваши последние публикации</p>
                        </div>
                    </div>
                </div>
                
                <div v-show="activeTab === 'posts'">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Просмотры постов</h3>
                        <div class="h-64">
                            <canvas ref="postsViewsChartRef"></canvas>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Лучшие посты</h3>
                            <div class="h-64">
                                <canvas ref="topPostsChartRef"></canvas>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Время публикации</h3>
                            <div class="h-64">
                                <canvas ref="postsTimeChartRef"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-show="activeTab === 'audience'">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Рост аудитории</h3>
                        <div class="h-64">
                            <canvas ref="audienceGrowthTabRef"></canvas>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Демография аудитории</h3>
                            <div class="h-64">
                                <canvas ref="audienceDemographicsRef"></canvas>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Активность по времени</h3>
                            <div class="h-64">
                                <canvas ref="audienceActivityRef"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-show="activeTab === 'engagement'">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Общая вовлеченность</h3>
                        <div class="h-64">
                            <canvas ref="engagementTabChartRef"></canvas>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Комментарии</h3>
                            <div class="h-64">
                                <canvas ref="commentsChartRef"></canvas>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Реакции</h3>
                            <div class="h-64">
                                <canvas ref="reactionsChartRef"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Chart from 'chart.js/auto';

// Определение пропсов
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalChannels: 0,
            totalPosts: 0,
            totalSubscribers: 0,
            totalViews: 0,
            recentPosts: [],
            topChannels: []
        })
    }
});

// Активная вкладка
const activeTab = ref('general');

// Тренды изменений (хардкодные для примера)
const channelTrend = 12;
const postTrend = 23;
const subscriberTrend = 8;
const viewTrend = 15;

// Refs для графиков
const viewsChartRef = ref(null);
const contentDistributionRef = ref(null);
const audienceGrowthRef = ref(null);
const engagementMetricsRef = ref(null);

// Дополнительные refs для новых графиков
const postsViewsChartRef = ref(null);
const topPostsChartRef = ref(null);
const postsTimeChartRef = ref(null);
const audienceGrowthTabRef = ref(null);
const audienceDemographicsRef = ref(null);
const audienceActivityRef = ref(null);
const engagementTabChartRef = ref(null);
const commentsChartRef = ref(null);
const reactionsChartRef = ref(null);

// Расширяем объект для хранения инстансов графиков
let charts = {
    views: null,
    contentDistribution: null,
    audienceGrowth: null,
    engagementMetrics: null,
    postsViews: null,
    topPosts: null,
    postsTime: null,
    audienceGrowthTab: null,
    audienceDemographics: null,
    audienceActivity: null,
    engagementTab: null,
    comments: null,
    reactions: null
};

// Данные для графиков (хардкодные)
const viewsData = {
    labels: ['1 фев', '5 фев', '10 фев', '15 фев', '20 фев', '25 фев', '1 мар', '5 мар', '10 мар', '15 мар', '20 мар', '25 мар', '1 апр'],
    datasets: [{
        label: 'Просмотры',
        data: [1200, 1900, 3000, 3800, 5000, 5800, 7000, 8300, 9000, 10500, 12000, 13800, 15000],
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
    }]
};

const contentCategories = ['Новости', 'Аналитика', 'Развлечения', 'Образование', 'Технологии'];
const contentDistributionData = {
    labels: contentCategories,
    datasets: [{
        label: 'Распределение контента',
        data: [35, 25, 20, 10, 10],
        backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
    }]
};

const audienceGrowthData = {
    labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
    datasets: [{
        label: 'Подписчики',
        data: [5000, 8000, 12000, 18000, 25000, 35000],
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
    }]
};

const engagementData = {
    labels: ['Канал 1', 'Канал 2', 'Канал 3', 'Канал 4', 'Канал 5'],
    datasets: [{
        label: 'Комментарии',
        data: [120, 190, 150, 180, 220],
        backgroundColor: 'rgba(59, 130, 246, 0.7)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 1
    }, {
        label: 'Репосты',
        data: [80, 120, 90, 140, 170],
        backgroundColor: 'rgba(16, 185, 129, 0.7)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 1
    }, {
        label: 'Лайки',
        data: [250, 320, 290, 350, 400],
        backgroundColor: 'rgba(245, 158, 11, 0.7)',
        borderColor: 'rgba(245, 158, 11, 1)',
        borderWidth: 1
    }]
};

// Данные для графиков в табе Публикации
const postsViewsData = {
    labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
    datasets: [{
        label: 'Просмотры',
        data: [4500, 3800, 5200, 6100, 7800, 8900, 6500],
        backgroundColor: 'rgba(99, 102, 241, 0.4)',
        borderColor: 'rgba(99, 102, 241, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
    }]
};

const topPostsData = {
    labels: ['Пост 1', 'Пост 2', 'Пост 3', 'Пост 4', 'Пост 5'],
    datasets: [{
        label: 'Просмотры',
        data: [8500, 7200, 6800, 5900, 4700],
        backgroundColor: [
            'rgba(59, 130, 246, 0.7)',
            'rgba(16, 185, 129, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(139, 92, 246, 0.7)'
        ],
        borderWidth: 0
    }]
};

const postsTimeData = {
    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
    datasets: [{
        label: 'Публикации',
        type: 'bar',
        data: [5, 12, 28, 35, 42, 18],
        backgroundColor: 'rgba(16, 185, 129, 0.6)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 1
    }, {
        label: 'Просмотры',
        type: 'line',
        data: [1200, 3500, 8200, 12000, 14500, 9800],
        backgroundColor: 'transparent',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        tension: 0.4,
        yAxisID: 'y1'
    }]
};

// Данные для графиков в табе Аудитория
const audienceGrowthTabData = {
    labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг'],
    datasets: [{
        label: 'Подписчики',
        data: [3000, 5000, 8000, 12000, 18000, 25000, 35000, 42000],
        backgroundColor: 'rgba(139, 92, 246, 0.2)',
        borderColor: 'rgba(139, 92, 246, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
    }, {
        label: 'Прирост',
        data: [3000, 2000, 3000, 4000, 6000, 7000, 10000, 7000],
        backgroundColor: 'rgba(244, 114, 182, 0.2)',
        borderColor: 'rgba(244, 114, 182, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
    }]
};

const audienceDemographicsData = {
    labels: ['18-24', '25-34', '35-44', '45-54', '55+'],
    datasets: [{
        label: 'Возрастные группы',
        data: [25, 40, 20, 10, 5],
        backgroundColor: [
            'rgba(59, 130, 246, 0.7)',
            'rgba(16, 185, 129, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(139, 92, 246, 0.7)'
        ],
        borderWidth: 1
    }]
};

const audienceActivityData = {
    labels: ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00'],
    datasets: [{
        label: 'Активность',
        data: [15, 8, 5, 30, 45, 40, 60, 35],
        backgroundColor: 'rgba(14, 165, 233, 0.5)',
        borderColor: 'rgba(14, 165, 233, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
    }]
};

// Данные для графиков в табе Вовлеченность
const engagementTabData = {
    labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
    datasets: [{
        label: 'Просмотры',
        data: [12000, 18000, 24000, 32000, 38000, 45000],
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: false
    }, {
        label: 'Комментарии',
        data: [800, 1200, 1600, 2200, 2800, 3500],
        backgroundColor: 'rgba(16, 185, 129, 0.5)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: false
    }, {
        label: 'Реакции',
        data: [2500, 3800, 5200, 7000, 8500, 10200],
        backgroundColor: 'rgba(245, 158, 11, 0.5)',
        borderColor: 'rgba(245, 158, 11, 1)',
        borderWidth: 2,
        tension: 0.4,
        fill: false
    }]
};

const commentsData = {
    labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
    datasets: [{
        label: 'Комментарии',
        data: [120, 180, 250, 310, 380, 420, 350],
        backgroundColor: 'rgba(16, 185, 129, 0.6)',
        borderColor: 'rgba(16, 185, 129, 1)',
        borderWidth: 2
    }]
};

const reactionsData = {
    labels: ['Лайки', 'Дизлайки', 'Сердечки', 'Смех', 'Удивление'],
    datasets: [{
        label: 'Реакции',
        data: [65, 10, 15, 7, 3],
        backgroundColor: [
            'rgba(59, 130, 246, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(244, 114, 182, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(16, 185, 129, 0.7)'
        ],
        borderWidth: 1
    }]
};

// Функция для форматирования числа (добавление разделителей тысяч)
const formatNumber = (num) => {
    return num ? num.toLocaleString('ru-RU') : '0';
};

// Функция для усечения длинного текста
const truncateText = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

// Инициализация графиков
const initCharts = () => {
    // Уничтожаем предыдущие экземпляры, если они существуют
    Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
    });
    
    // График просмотров
    if (viewsChartRef.value) {
        charts.views = new Chart(viewsChartRef.value, {
            type: 'line',
            data: viewsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Распределение контента (пирог)
    if (contentDistributionRef.value) {
        charts.contentDistribution = new Chart(contentDistributionRef.value, {
            type: 'pie',
            data: contentDistributionData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}%`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Рост аудитории
    if (audienceGrowthRef.value) {
        charts.audienceGrowth = new Chart(audienceGrowthRef.value, {
            type: 'line',
            data: audienceGrowthData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Метрики вовлеченности
    if (engagementMetricsRef.value) {
        charts.engagementMetrics = new Chart(engagementMetricsRef.value, {
            type: 'bar',
            data: engagementData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Инициализация графиков для таба "Публикации"
    if (postsViewsChartRef.value) {
        charts.postsViews = new Chart(postsViewsChartRef.value, {
            type: 'line',
            data: postsViewsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    if (topPostsChartRef.value) {
        charts.topPosts = new Chart(topPostsChartRef.value, {
            type: 'bar',
            data: topPostsData,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    if (postsTimeChartRef.value) {
        charts.postsTime = new Chart(postsTimeChartRef.value, {
            type: 'bar',
            data: postsTimeData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Публикации'
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Просмотры'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Инициализация графиков для таба "Аудитория"
    if (audienceGrowthTabRef.value) {
        charts.audienceGrowthTab = new Chart(audienceGrowthTabRef.value, {
            type: 'line',
            data: audienceGrowthTabData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    if (audienceDemographicsRef.value) {
        charts.audienceDemographics = new Chart(audienceDemographicsRef.value, {
            type: 'doughnut',
            data: audienceDemographicsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}%`;
                            }
                        }
                    }
                }
            }
        });
    }

    if (audienceActivityRef.value) {
        charts.audienceActivity = new Chart(audienceActivityRef.value, {
            type: 'line',
            data: audienceActivityData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Инициализация графиков для таба "Вовлеченность"
    if (engagementTabChartRef.value) {
        charts.engagementTab = new Chart(engagementTabChartRef.value, {
            type: 'line',
            data: engagementTabData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        }
                    }
                }
            }
        });
    }

    if (commentsChartRef.value) {
        charts.comments = new Chart(commentsChartRef.value, {
            type: 'bar',
            data: commentsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    if (reactionsChartRef.value) {
        charts.reactions = new Chart(reactionsChartRef.value, {
            type: 'pie',
            data: reactionsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
};

// Хук жизненного цикла
onMounted(() => {
    // Небольшая задержка для гарантированной загрузки DOM
    setTimeout(() => {
        initCharts();
    }, 200);
});
</script> 