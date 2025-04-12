<template>
    <Head title="Создание группы каналов" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Создание новой группы каналов
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Основная информация -->
                            <div class="mb-6">
                                <div>
                                    <InputLabel for="name" value="Название группы" />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        autofocus
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <div>
                                    <InputLabel for="description" value="Описание группы" />
                                    <TextArea
                                        id="description"
                                        v-model="form.description"
                                        class="mt-1 block w-full"
                                        rows="3"
                                    />
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <div>
                                    <InputLabel for="category" value="Категория" />
                                    <TextInput
                                        id="category"
                                        v-model="form.category"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.category" class="mt-2" />
                                </div>
                            </div>

                            <!-- Выбор каналов -->
                            <div class="mb-6">
                                <div>
                                    <InputLabel value="Выберите каналы для группы" />

                                    <div v-if="channels && channels.length > 0" class="mt-3 space-y-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <DatasetChannel 
                                                v-for="channel in channels" 
                                                :key="channel.id"
                                                :channel="channel"
                                                v-model="selectedChannels"
                                            />
                                        </div>
                                    </div>
                                    <div v-else class="p-3 bg-yellow-50 text-yellow-800 rounded mt-2">
                                        <p>У вас еще нет каналов. <Link :href="route('channels.create')" class="text-blue-600 hover:underline">Создать новый канал</Link></p>
                                    </div>
                                    <InputError :message="form.errors.channels" class="mt-2" />
                                </div>
                            </div>

                            <!-- Кнопки формы -->
                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('channel-groups.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3"
                                >
                                    Отмена
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Создать группу
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import DatasetChannel from '@/Components/DatasetChannel.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    channels: Array,
});

// Создаем форму для группы
const form = useForm({
    name: '',
    description: '',
    category: '',
    channels: [],
});

// Управление выбранными каналами
const selectedChannels = ref([]);

// Отправка формы
const submit = () => {
    // Синхронизируем selectedChannels с form.channels
    form.channels = selectedChannels.value;
    form.post(route('channel-groups.store'));
};
</script> 