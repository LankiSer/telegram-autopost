<template>
    <Head title="Редактирование поста" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Редактирование поста
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.error }}
                </div>
                
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
                                :disabled="post.status === 'pending'" 
                            >
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

                        <!-- Планировщик -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <div class="flex items-center mb-4">
                                <input 
                                    id="schedule_enabled" 
                                    type="checkbox" 
                                    v-model="scheduleEnabled" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded"
                                    :disabled="post.status === 'pending'"
                                >
                                <label for="schedule_enabled" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Запланировать публикацию
                                </label>
                            </div>
                            
                            <div v-if="scheduleEnabled">
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
                        </div>

                        <!-- Статус поста -->
                        <div v-if="post.status === 'pending'" class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg text-yellow-800 dark:text-yellow-300">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p>Пост находится в очереди на публикацию и не может быть полностью отредактирован. Вы можете изменить только содержимое поста.</p>
                            </div>
                        </div>

                        <!-- Кнопки формы -->
                        <div class="flex items-center justify-between pt-4">
                            <div>
                                <Link 
                                    :href="route('posts.show', post.id)" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Отмена
                                </Link>
                            </div>
                            <div class="flex space-x-2">
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
                                <button 
                                    v-if="post.status !== 'published' && post.status !== 'pending'" 
                                    type="button" 
                                    @click="publishNow" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Опубликовать
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    post: Object,
    channels: Array
});

// Определяем, включено ли планирование
const scheduleEnabled = ref(!!props.post.scheduled_at);

// Формируем данные формы
const form = useForm({
    channel_id: props.post.channel_id,
    content: props.post.content,
    scheduled_at: props.post.scheduled_at ? formatDateTimeLocal(new Date(props.post.scheduled_at)) : formatDateTimeLocal(new Date(Date.now() + 3600000))
});

const minDate = computed(() => {
    const now = new Date();
    now.setMinutes(now.getMinutes() + 5); // Минимум 5 минут в будущем
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
    // Если планирование не включено, обнуляем scheduled_at
    const formData = {...form};
    if (!scheduleEnabled.value) {
        formData.scheduled_at = null;
    }
    
    form.put(route('posts.update', props.post.id));
}

function publishNow() {
    if (confirm('Вы уверены, что хотите опубликовать этот пост сейчас?')) {
        // Сначала сохраняем изменения, затем публикуем
        form.put(route('posts.update', props.post.id), {
            onSuccess: () => {
                router.post(route('posts.publish', props.post.id));
            }
        });
    }
}
</script> 