<template>
    <AppLayout title="Создание рекламной рассылки">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Создание рекламной рассылки для группы «{{ group.name }}»
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Информация о группе -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Информация о группе</h3>
                            <p class="text-gray-600 mb-1">Название: {{ group.name }}</p>
                            <p class="text-gray-600 mb-1">Описание: {{ group.description || 'Не указано' }}</p>
                            <p class="text-gray-600">Количество каналов: {{ channels.length }}</p>
                        </div>

                        <!-- Форма создания рекламной рассылки -->
                        <form @submit.prevent="submitForm">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-2">Текст рекламного объявления</h3>
                                <div class="mb-4">
                                    <label for="original_content" class="block text-sm font-medium text-gray-700 mb-1">
                                        Исходный текст рекламы
                                    </label>
                                    <textarea
                                        id="original_content"
                                        v-model="form.original_content"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                        rows="6"
                                        placeholder="Введите исходный текст рекламного объявления"
                                        required
                                    ></textarea>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Этот текст будет улучшен с помощью GigaChat и разослан по выбранным каналам.
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <label for="advertisement_link" class="block text-sm font-medium text-gray-700 mb-1">
                                        Ссылка для рекламы
                                    </label>
                                    <input
                                        id="advertisement_link"
                                        type="url"
                                        v-model="form.advertisement_link"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="https://example.com"
                                        required
                                    />
                                    <p class="text-sm text-gray-500 mt-1">
                                        Эта ссылка будет добавлена в конец рекламного поста.
                                    </p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-2">Выберите каналы для рассылки</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="channel in channels" :key="channel.id" class="p-3 border border-gray-200 rounded-md">
                                        <div class="flex items-start">
                                            <input
                                                type="checkbox"
                                                :id="`channel-${channel.id}`"
                                                v-model="form.selected_channels"
                                                :value="channel.id"
                                                class="h-4 w-4 mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                            />
                                            <label :for="`channel-${channel.id}`" class="ml-3">
                                                <span class="block text-sm font-medium text-gray-700">{{ channel.name }}</span>
                                                <span class="block text-xs text-gray-500">{{ channel.telegram_username || 'Нет имени пользователя' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="channels.length === 0" class="text-gray-500 italic">
                                    В этой группе нет каналов.
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <Link :href="route('channel-groups.show', group.id)" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Отмена
                                </Link>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    :disabled="form.processing || !form.selected_channels.length"
                                >
                                    Создать рекламные посты
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default defineComponent({
    components: {
        AppLayout,
        Link
    },
    props: {
        group: Object,
        channels: Array,
    },
    setup(props) {
        const form = useForm({
            original_content: '',
            advertisement_link: '',
            selected_channels: []
        });

        const submitForm = () => {
            if (!form.selected_channels.length) {
                alert('Выберите хотя бы один канал для рассылки');
                return;
            }

            form.post(route('channel-groups.advertising.store', props.group.id));
        };

        return {
            form,
            submitForm
        };
    }
});
</script> 