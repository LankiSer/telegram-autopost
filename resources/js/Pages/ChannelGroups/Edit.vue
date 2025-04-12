<template>
    <Head title="Редактирование группы каналов" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Редактирование группы каналов
                </h2>
                <div class="flex space-x-2" v-if="group && group.id">
                    <Link :href="route('channel-groups.show', group.id)" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Просмотр
                    </Link>
                    <Link :href="route('channel-groups.index')" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Назад к списку
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-4">Основная информация</h3>
                                
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Название группы</label>
                                    <input type="text" id="name" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="category" class="block text-sm font-medium text-gray-700">Категория (опционально)</label>
                                    <input type="text" id="category" v-model="form.category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Описание (опционально)</label>
                                    <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-4">Выбор каналов</h3>
                                <div v-if="channels.length > 0" class="bg-gray-50 p-4 rounded-md">
                                    <div class="mb-2 text-sm text-gray-600">Выберите каналы, которые хотите добавить в группу:</div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                        <DatasetChannel 
                                            v-for="channel in channels" 
                                            :key="channel.id" 
                                            :channel="channel" 
                                            v-model="form.channels"
                                        />
                                    </div>
                                </div>
                                <div v-else class="bg-gray-50 p-4 rounded-md">
                                    <p class="text-gray-500">У вас пока нет каналов. 
                                        <Link :href="route('channels.create')" class="text-blue-600 hover:underline">
                                            Создайте канал
                                        </Link> 
                                        перед добавлением в группу.
                                    </p>
                                </div>
                                <div v-if="form.errors.channels" class="text-red-500 text-sm mt-1">{{ form.errors.channels }}</div>
                            </div>
                            
                            <div class="flex items-center justify-end mt-6">
                                <Button :disabled="form.processing" :class="{ 'opacity-25': form.processing }" class="ml-4">
                                    <span v-if="form.processing">Сохранение...</span>
                                    <span v-else>Сохранить изменения</span>
                                </Button>
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
import Button from '@/Components/PrimaryButton.vue';
import DatasetChannel from '@/Components/DatasetChannel.vue';

const props = defineProps({
    group: Object,
    channels: Array,
    selectedChannels: Array
});

const form = useForm({
    name: props.group?.name || '',
    description: props.group?.description || '',
    category: props.group?.category || '',
    channels: props.selectedChannels || []
});

function submit() {
    if (!props.group?.id) {
        console.error('Channel group ID is missing');
        return;
    }
    form.put(route('channel-groups.update', props.group.id));
}
</script> 