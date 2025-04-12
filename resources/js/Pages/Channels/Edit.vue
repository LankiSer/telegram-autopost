<template>
    <Head title="Редактирование канала" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Редактирование канала
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Форма редактирования канала -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Название канала</label>
                            <input 
                                type="text" 
                                id="name" 
                                v-model="form.name" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Описание</label>
                            <textarea 
                                id="description" 
                                v-model="form.description" 
                                rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Тип канала</label>
                            <select 
                                id="type" 
                                v-model="form.type" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="telegram">Telegram</option>
                                <option value="vk">ВКонтакте</option>
                            </select>
                            <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
                        </div>

                        <!-- Для Telegram -->
                        <div v-if="form.type === 'telegram'">
                            <label for="telegram_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Имя пользователя Telegram канала</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 bg-gray-50 text-gray-500">@</span>
                                <input 
                                    type="text" 
                                    id="telegram_username" 
                                    v-model="form.telegram_username" 
                                    class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="имя_канала"
                                >
                            </div>
                            <div v-if="form.errors.telegram_username" class="text-red-500 text-sm mt-1">{{ form.errors.telegram_username }}</div>
                            
                            <div class="mt-4">
                                <label for="telegram_channel_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID канала в Telegram (опционально)</label>
                                <input 
                                    type="text" 
                                    id="telegram_channel_id" 
                                    v-model="form.telegram_channel_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="-100123456789"
                                >
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Оставьте пустым, если не знаете ID канала</p>
                                <div v-if="form.errors.telegram_channel_id" class="text-red-500 text-sm mt-1">{{ form.errors.telegram_channel_id }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="content_prompt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Промпт для генерации контента</label>
                            <textarea 
                                id="content_prompt" 
                                v-model="form.content_prompt" 
                                rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Опишите, какой контент должен генерироваться для этого канала..."
                            ></textarea>
                            <div v-if="form.errors.content_prompt" class="text-red-500 text-sm mt-1">{{ form.errors.content_prompt }}</div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Сохранить
                            </button>
                            <Link 
                                :href="route('channels.index')" 
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
    channel: Object
});

const form = useForm({
    name: props.channel.name,
    description: props.channel.description || '',
    type: props.channel.type || 'telegram',
    telegram_username: props.channel.telegram_username || '',
    telegram_channel_id: props.channel.telegram_channel_id || '',
    content_prompt: props.channel.content_prompt || '',
});

const submit = () => {
    form.put(route('channels.update', props.channel.id));
};
</script> 