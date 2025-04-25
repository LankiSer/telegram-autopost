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
                <!-- Уведомления -->
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>
                
                <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>

                <!-- Текущая подписка -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ваша текущая подписка</h3>
                    
                    <div v-if="currentSubscription" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h4 class="text-xl font-bold text-blue-800 dark:text-blue-200">{{ currentSubscription.name }}</h4>
                                <p class="text-blue-700 dark:text-blue-300 mt-1">Активна до: {{ formatDate(currentSubscription.ends_at) || 'Бессрочно' }}</p>
                                <div class="mt-2">
                                    <p class="text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Лимит каналов:</span> {{ currentSubscription.channel_limit }}
                                    </p>
                                    <p class="text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Постов в месяц:</span> {{ currentSubscription.posts_per_month }}
                                    </p>
                                    <p class="text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Планирование постов:</span> 
                                        {{ currentSubscription.scheduling_enabled ? 'Доступно' : 'Недоступно' }}
                                    </p>
                                    <p class="text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Аналитика:</span> 
                                        {{ currentSubscription.analytics_enabled ? 'Доступна' : 'Недоступна' }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <form @submit.prevent="cancelSubscription">
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Отменить подписку
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                        <p class="text-yellow-700 dark:text-yellow-300">
                            У вас нет активной подписки. Выберите тарифный план ниже.
                        </p>
                    </div>
                </div>
                
                <!-- Отладочная информация -->
                <div v-if="plans.length === 0" class="bg-red-50 dark:bg-red-900 p-4 rounded-lg mb-6">
                    <p class="text-red-700 dark:text-red-300">
                        Тарифные планы не загружены. Пожалуйста, обратитесь к администратору.
                    </p>
                </div>
                
                <!-- Доступные тарифы -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Доступные тарифы</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="plan in plans" :key="plan.id" 
                            :class="[
                                'border rounded-lg overflow-hidden', 
                                currentSubscription && currentSubscription.plan_id === plan.id 
                                    ? 'border-green-500 dark:border-green-700 shadow-md' 
                                    : plan.price > 0 
                                        ? 'border-blue-500 dark:border-blue-700 shadow-md' 
                                        : 'border-gray-200 dark:border-gray-700'
                            ]">
                            <div :class="[
                                'p-4',
                                currentSubscription && currentSubscription.plan_id === plan.id 
                                    ? 'bg-green-500 dark:bg-green-700' 
                                    : plan.price > 0 
                                        ? 'bg-blue-500 dark:bg-blue-700' 
                                        : 'bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700'
                            ]">
                                <h4 class="text-lg font-bold" :class="[
                                    currentSubscription && currentSubscription.plan_id === plan.id 
                                        ? 'text-white' 
                                        : plan.price > 0 
                                            ? 'text-white' 
                                            : 'text-gray-900 dark:text-white'
                                ]">{{ plan.name }}</h4>
                                <p class="text-2xl font-bold mt-2" :class="[
                                    currentSubscription && currentSubscription.plan_id === plan.id 
                                        ? 'text-white' 
                                        : plan.price > 0 
                                            ? 'text-white' 
                                            : 'text-gray-900 dark:text-white'
                                ]">{{ plan.price > 0 ? `${plan.price} ₽/мес` : 'Бесплатно' }}</p>
                            </div>
                            <div class="p-4">
                                <p class="text-gray-700 dark:text-gray-300 mb-3">{{ plan.description }}</p>
                                <ul class="space-y-2">
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ plan.channel_limit > 100 ? 'Неограниченное количество каналов' : `До ${plan.channel_limit} ${getNounForm(plan.channel_limit, ['канала', 'каналов', 'каналов'])}` }}
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ plan.posts_per_month > 1000 ? 'Неограниченное количество постов' : `До ${plan.posts_per_month} ${getNounForm(plan.posts_per_month, ['поста', 'постов', 'постов'])} в месяц` }}
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg v-if="plan.scheduling_enabled" class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        {{ plan.scheduling_enabled ? 'Планирование постов' : 'Нет планирования постов' }}
                                    </li>
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg v-if="plan.analytics_enabled" class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        {{ plan.analytics_enabled ? 'Расширенная аналитика' : 'Базовая аналитика' }}
                                    </li>
                                </ul>
                                <div class="mt-6">
                                    <form v-if="!currentSubscription || currentSubscription.plan_id !== plan.id" @submit.prevent="subscribeToPlan(plan.id)">
                                        <button type="submit" :class="[
                                            'w-full px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-offset-2',
                                            plan.price > 0 
                                                ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500' 
                                                : 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500'
                                        ]">
                                            Выбрать тариф
                                        </button>
                                    </form>
                                    <button v-else disabled class="w-full px-4 py-2 bg-green-300 text-green-700 rounded opacity-70 cursor-not-allowed">
                                        Текущий тариф
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
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    plans: {
        type: Array,
        default: () => []
    },
    currentSubscription: {
        type: Object,
        default: null
    }
});

const subscribeForm = useForm({
    plan_id: null
});

const cancelForm = useForm({});

function subscribeToPlan(planId) {
    subscribeForm.plan_id = planId;
    subscribeForm.post(route('subscription.subscribe'));
}

function cancelSubscription() {
    if (confirm('Вы уверены, что хотите отменить текущую подписку?')) {
        cancelForm.post(route('subscription.cancel'));
    }
}

function formatDate(dateString) {
    if (!dateString) return 'Бессрочно';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getNounForm(number, titles) {
    const cases = [2, 0, 1, 1, 1, 2];
    return titles[ (number%100>4 && number%100<20) ? 2 : cases[(number%10<5) ? number%10 : 5] ];
}
</script> 