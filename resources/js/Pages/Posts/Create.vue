<template>
    <Head title="Создать пост" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Создать пост
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Выбор канала -->
                        <div>
                            <label for="channel_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Канал</label>
                            <select 
                                id="channel_id" 
                                v-model="form.channel_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option disabled value="">Выберите канал</option>
                                <option v-for="channel in channels" :key="channel.id" :value="channel.id">
                                    {{ channel.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.channel_id" class="text-red-500 text-sm mt-1">{{ form.errors.channel_id }}</div>
                        </div>

                        <!-- Содержимое поста -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Текст сообщения</label>
                            <textarea 
                                id="content" 
                                v-model="form.content" 
                                rows="8" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Введите текст вашего сообщения..."
                                required
                            ></textarea>
                            <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                        </div>

                        <!-- Тип публикации -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Тип публикации</label>
                            <div class="mt-2 space-y-4">
                                <div class="flex items-center">
                                    <input 
                                        id="publish_now" 
                                        type="radio" 
                                        value="now" 
                                        v-model="form.publish_type" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                    >
                                    <label for="publish_now" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Опубликовать сейчас
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input 
                                        id="publish_scheduled" 
                                        type="radio" 
                                        value="scheduled" 
                                        v-model="form.publish_type" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                    >
                                    <label for="publish_scheduled" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Запланировать на определенное время
                                    </label>
                                </div>
                            </div>
                            <div v-if="form.errors.publish_type" class="text-red-500 text-sm mt-1">{{ form.errors.publish_type }}</div>
                        </div>

                        <!-- Планировщик -->
                        <div v-if="form.publish_type === 'scheduled'" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Дата и время публикации</label>
                            <input 
                                id="scheduled_at" 
                                type="datetime-local" 
                                v-model="form.scheduled_at" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :min="minDate"
                                required
                            >
                            <div v-if="form.errors.scheduled_at" class="text-red-500 text-sm mt-1">{{ form.errors.scheduled_at }}</div>
                        </div>

                        <!-- Кнопки формы -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <Link 
                                :href="route('posts.index')" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Отмена
                            </Link>
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.publish_type === 'now' ? 'Опубликовать' : 'Запланировать' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    channels: Array
});

const form = useForm({
    channel_id: '',
    content: '',
    publish_type: 'now',
    scheduled_at: formatDateTimeLocal(new Date(Date.now() + 3600000)) // Default 1 hour from now
});

const minDate = computed(() => {
    const now = new Date();
    now.setMinutes(now.getMinutes() + 5); // Minimum 5 minutes in the future
    return formatDateTimeLocal(now);
});

function formatDateTimeLocal(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function submit() {
    form.post(route('posts.store'));
}
</script> 