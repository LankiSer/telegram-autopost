<template>
    <Head title="Тарифы" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Тарифы и подписки
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Текущая подписка -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ваша текущая подписка</h3>
                    
                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h4 class="text-xl font-bold text-blue-800 dark:text-blue-200">{{ currentPlan?.name || 'Базовый' }}</h4>
                                <p class="text-blue-700 dark:text-blue-300 mt-1">Активна до: {{ formatDate(currentPlan?.expires_at) || 'Бессрочно' }}</p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <button v-if="currentPlan?.id && currentPlan?.id !== 'free'" 
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Отменить подписку
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Доступные тарифы -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Доступные тарифы</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 dark:bg-gray-900 p-4 border-b border-gray-200 dark:border-gray-700">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Базовый</h4>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Бесплатно</p>
                            </div>
                            <div class="p-4">
                                <ul class="space-y-2">
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        До 3 каналов
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        До 10 постов в день
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Нет планирования постов
                                    </li>
                                </ul>
                                <div class="mt-6">
                                    <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded opacity-50 cursor-not-allowed">
                                        Текущий тариф
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border border-blue-500 dark:border-blue-700 rounded-lg overflow-hidden shadow-md">
                            <div class="bg-blue-500 dark:bg-blue-700 p-4">
                                <h4 class="text-lg font-bold text-white">Стандарт</h4>
                                <p class="text-2xl font-bold text-white mt-2">499 ₽/мес</p>
                            </div>
                            <div class="p-4">
                                <ul class="space-y-2">
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        До 10 каналов
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        До 50 постов в день
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Планирование постов
                                    </li>
                                </ul>
                                <div class="mt-6">
                                    <button class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Выбрать тариф
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border border-purple-500 dark:border-purple-700 rounded-lg overflow-hidden shadow-md">
                            <div class="bg-purple-500 dark:bg-purple-700 p-4">
                                <h4 class="text-lg font-bold text-white">Профессиональный</h4>
                                <p class="text-2xl font-bold text-white mt-2">1499 ₽/мес</p>
                            </div>
                            <div class="p-4">
                                <ul class="space-y-2">
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Неограниченное количество каналов
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Неограниченное количество постов
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Расширенная аналитика
                                    </li>
                                </ul>
                                <div class="mt-6">
                                    <button class="w-full px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Выбрать тариф
                                    </button>
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
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    plans: {
        type: Array,
        default: () => []
    },
    currentPlan: {
        type: Object,
        default: null
    }
});

function formatDate(dateString) {
    if (!dateString) return 'Не указано';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}
</script> 