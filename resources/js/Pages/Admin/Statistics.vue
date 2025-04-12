<template>
    <Head title="Админ панель - Статистика" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Статистика системы
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
                                <p class="text-sm text-gray-500 dark:text-gray-400">Пользователи</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalUsers) }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    {{ formatNumber(stats.activeUsers) }} активных за месяц
                                </p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Каналы</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalChannels) }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    {{ formatNumber(stats.activeChannels) }} активных за месяц
                                </p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Посты</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalPosts) }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    {{ formatNumber(stats.publishedPosts) }} опубликовано
                                </p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Подписчики</p>
                                <h3 class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">{{ formatNumber(stats.totalSubscribers) }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    {{ formatNumber(stats.totalViews) }} просмотров
                                </p>
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
                
                <!-- Графики статистики -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Регистрации пользователей</h3>
                        <div class="h-64">
                            <canvas ref="registrationsChartRef"></canvas>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Публикация постов</h3>
                        <div class="h-64">
                            <canvas ref="postsChartRef"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Топ пользователей -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Топ пользователей</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователь</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Постов</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата регистрации</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in stats.topUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ user.posts_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ formatDate(user.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Топ каналов -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Топ каналов</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Канал</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователь</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Постов</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-900 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Подписчиков</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="channel in stats.topChannels" :key="channel.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ channel.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ channel.user ? channel.user.name : 'Нет данных' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ channel.posts_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ formatNumber(channel.subscribers) }}</td>
                                </tr>
                            </tbody>
                        </table>
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
            totalUsers: 0,
            activeUsers: 0,
            totalChannels: 0,
            activeChannels: 0,
            totalPosts: 0,
            publishedPosts: 0,
            scheduledPosts: 0,
            failedPosts: 0,
            totalSubscribers: 0,
            totalViews: 0,
            registrationsData: [],
            postsData: [],
            topUsers: [],
            topChannels: [],
            channelsStats: [],
            hourlyStats: []
        })
    }
});

// Refs для графиков
const registrationsChartRef = ref(null);
const postsChartRef = ref(null);

// Инстансы графиков
let charts = {
    registrations: null,
    posts: null
};

// Форматирование чисел
function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    
    return new Intl.NumberFormat('ru-RU').format(num);
}

// Форматирование даты
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU').format(date);
}

// Преобразование данных для графиков
function prepareChartData(data, label) {
    const labels = data.map(item => formatDate(item.date));
    const values = data.map(item => item.count);
    
    return {
        labels: labels,
        datasets: [{
            label: label,
            data: values,
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };
}

// Инициализация графиков
function initCharts() {
    // Уничтожаем предыдущие экземпляры, если они существуют
    Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
    });
    
    // График регистраций
    if (registrationsChartRef.value && props.stats.registrationsData.length > 0) {
        const registrationsData = prepareChartData(props.stats.registrationsData, 'Регистрации');
        
        charts.registrations = new Chart(registrationsChartRef.value, {
            type: 'line',
            data: registrationsData,
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
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    
    // График постов
    if (postsChartRef.value && props.stats.postsData.length > 0) {
        const postsData = prepareChartData(props.stats.postsData, 'Посты');
        
        charts.posts = new Chart(postsChartRef.value, {
            type: 'bar',
            data: postsData,
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
                            precision: 0
                        }
                    }
                }
            }
        });
    }
}

// При монтировании компонента
onMounted(() => {
    setTimeout(() => {
        initCharts();
    }, 200);
});
</script> 