<template>
    <Head title="Настройки автопостинга" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Настройки автопостинга: {{ channel.name }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Информация о канале -->
                    <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-lg font-bold mr-4">
                                {{ channel.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ channel.name }}</h3>
                                <p v-if="channel.telegram_username" class="text-sm text-blue-600 dark:text-blue-400">
                                    @{{ channel.telegram_username }}
                                </p>
                            </div>
                        </div>
                        <p v-if="channel.description" class="text-gray-600 dark:text-gray-300">
                            {{ channel.description }}
                        </p>
                    </div>
                    
                    <!-- Форма настроек автопостинга -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="is_active" 
                                    v-model="form.is_active" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded"
                                >
                                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Включить автопостинг
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Если включено, система будет автоматически публиковать контент в этот канал с указанным интервалом
                            </p>
                        </div>

                        <div v-if="form.is_active" class="space-y-6 mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <div>
                                <label for="interval_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Интервал публикации
                                </label>
                                <div class="flex items-center mt-1 space-x-2">
                                    <input 
                                        type="number" 
                                        id="interval_value" 
                                        v-model="form.interval_value" 
                                        min="1" 
                                        max="168" 
                                        class="block w-1/3 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                    <select
                                        id="interval_type"
                                        v-model="form.interval_type"
                                        class="block w-1/3 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="minutes">Минуты</option>
                                        <option value="hours">Часы</option>
                                        <option value="days">Дни</option>
                                        <option value="weeks">Недели</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Определяет, как часто будут публиковаться новые посты
                                </p>
                                <div v-if="form.errors.interval_value" class="text-red-500 text-sm mt-1">{{ form.errors.interval_value }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="schedule_start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Время начала публикаций
                                    </label>
                                    <input 
                                        type="time" 
                                        id="schedule_start_time" 
                                        v-model="form.schedule_start_time" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                    <div v-if="form.errors.schedule_start_time" class="text-red-500 text-sm mt-1">{{ form.errors.schedule_start_time }}</div>
                                </div>

                                <div>
                                    <label for="schedule_end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Время окончания публикаций
                                    </label>
                                    <input 
                                        type="time" 
                                        id="schedule_end_time" 
                                        v-model="form.schedule_end_time" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Публикации будут происходить только в указанный временной интервал
                                    </p>
                                    <div v-if="form.errors.schedule_end_time" class="text-red-500 text-sm mt-1">{{ form.errors.schedule_end_time }}</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="prompt_template" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Промпт для генерации контента
                            </label>
                            <textarea 
                                id="prompt_template" 
                                v-model="form.prompt_template" 
                                rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Опишите, какой контент должен генерироваться для этого канала..."
                            ></textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Используйте этот промпт для более точной настройки генерации контента. Например: "Новости о технологиях, фокус на AI и блокчейн"
                            </p>
                            <div v-if="form.errors.prompt_template" class="text-red-500 text-sm mt-1">{{ form.errors.prompt_template }}</div>
                        </div>

                        <div class="flex items-center space-x-4 pt-4">
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Сохранить настройки
                            </button>
                            <Link 
                                :href="route('channels.show', channel.id)" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Отмена
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    channel: Object,
    settings: Object
});

// Initialize form data from existing settings or with defaults
const formSettings = props.settings || {};
const scheduleStartTime = formSettings.posting_schedule?.start_time || '09:00';
const scheduleEndTime = formSettings.posting_schedule?.end_time || '21:00';

const form = useForm({
    is_active: formSettings.is_active || false,
    interval_value: formSettings.interval_value || 24,
    interval_type: formSettings.interval_type || 'hours',
    schedule_start_time: scheduleStartTime,
    schedule_end_time: scheduleEndTime,
    prompt_template: formSettings.prompt_template || '',
    schedule_days: formSettings.posting_schedule?.days || [1, 2, 3, 4, 5, 6, 7],
});

const submit = () => {
    form.post(route('channels.auto-posting.update', props.channel.id));
};
</script>