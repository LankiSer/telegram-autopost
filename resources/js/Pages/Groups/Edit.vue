<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Редактирование группы каналов') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="form.put(route('groups.update', group.id))">
                            <!-- Название группы -->
                            <div class="mb-6">
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

                            <!-- Описание группы -->
                            <div class="mb-6">
                                <InputLabel for="description" value="Описание (необязательно)" />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Краткое описание для чего предназначена эта группа каналов.
                                </p>
                            </div>

                            <!-- Выбор каналов -->
                            <div class="mb-6">
                                <h3 class="font-medium text-gray-700 mb-2">Выберите каналы для группы</h3>
                                <p class="mb-3 text-sm text-gray-500">
                                    Выберите каналы, которые хотите включить в эту группу для кросс-продвижения контента.
                                </p>
                                
                                <div v-if="channels.length === 0" class="p-4 bg-gray-50 rounded border border-gray-200 text-center">
                                    <p class="text-gray-600">У вас пока нет добавленных каналов.</p>
                                    <Link :href="route('channels.create')" class="mt-2 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Добавить канал
                                    </Link>
                                </div>

                                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <DatasetChannel 
                                        v-for="channel in channels" 
                                        :key="channel.id"
                                        :channel="channel"
                                        v-model="selectedChannels"
                                    />
                                </div>
                                <InputError :message="form.errors.channels" class="mt-2" />
                            </div>

                            <!-- Статистика по группе -->
                            <div v-if="group.stats" class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-medium text-gray-700 mb-2">Статистика группы</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                                    <div class="p-3 bg-white rounded-lg shadow-sm">
                                        <div class="text-2xl font-semibold text-blue-600">{{ group.stats.posts_count }}</div>
                                        <div class="text-sm text-gray-500">Публикаций</div>
                                    </div>
                                    <div class="p-3 bg-white rounded-lg shadow-sm">
                                        <div class="text-2xl font-semibold text-green-600">{{ group.stats.active_channels }}</div>
                                        <div class="text-sm text-gray-500">Активных каналов</div>
                                    </div>
                                    <div class="p-3 bg-white rounded-lg shadow-sm">
                                        <div class="text-2xl font-semibold text-purple-600">{{ group.stats.total_reach }}</div>
                                        <div class="text-sm text-gray-500">Суммарный охват</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Раздел кнопок -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <Link :href="route('groups.index')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                                    Назад
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Сохранить изменения
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import DatasetChannel from '@/Components/DatasetChannel.vue';

const props = defineProps({
    group: Object,
    channels: Array
});

const selectedChannels = ref([]);

const form = useForm({
    name: props.group.name,
    description: props.group.description || '',
    channels: []
});

// Обновляем form.channels когда меняется выбор каналов
watch(selectedChannels, (newSelectedChannels) => {
    form.channels = newSelectedChannels;
});

onMounted(() => {
    // Инициализируем выбранные каналы из текущей группы
    if (props.group && props.group.channels) {
        selectedChannels.value = props.group.channels.map(channel => channel.id);
        form.channels = selectedChannels.value;
    }
});
</script> 