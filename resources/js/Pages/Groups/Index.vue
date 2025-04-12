<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Группы каналов') }}
                </h2>
                <Link :href="route('groups.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Создать группу
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.error }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Карточка для создания новой группы -->
                    <div class="bg-white rounded-lg shadow-md border border-dashed border-gray-300 flex flex-col items-center justify-center p-6 h-64">
                        <Link :href="route('groups.create')" class="flex flex-col items-center text-center">
                            <svg class="w-12 h-12 text-blue-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Создать новую группу</h3>
                            <p class="text-sm text-gray-500">Объедините каналы для кросс-продвижения</p>
                        </Link>
                    </div>

                    <!-- Карточки групп -->
                    <div v-for="group in groups.data" :key="group.id" class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <Link :href="route('groups.show', group.id)" class="hover:text-blue-600">
                                        {{ group.name }}
                                    </Link>
                                </h3>
                                <div class="flex items-center">
                                    <Link :href="route('groups.edit', group.id)" class="text-blue-600 hover:text-blue-900 mr-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </Link>
                                    <button @click="confirmDelete(group.id)" class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">
                                {{ truncateText(group.description || 'Нет описания', 100) }}
                            </p>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">
                                        <span class="font-semibold">{{ group.channels_count }}</span> каналов
                                    </div>
                                    <div>
                                        <Link :href="route('groups.cross-promote', group.id)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                            Запустить кросс-пост
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Пагинация -->
                <div class="mt-6" v-if="groups.links && groups.links.length > 3">
                    <div class="flex flex-wrap justify-center">
                        <Link v-for="(link, i) in groups.links" 
                                :key="i" 
                                :href="link.url" 
                                :class="{'bg-blue-500 text-white': link.active, 'text-gray-700': !link.active}" 
                                class="mx-1 px-3 py-1 rounded text-sm" 
                                v-html="link.label">
                        </Link>
                    </div>
                </div>

                <!-- Сообщение, если групп нет -->
                <div v-if="groups.data && groups.data.length === 0" class="bg-white shadow-md rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Нет групп каналов</h3>
                    <p class="mt-1 text-gray-500">Создайте свою первую группу каналов для кросс-промо.</p>
                    <div class="mt-6">
                        <Link :href="route('groups.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Создать группу
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    groups: Object
});

const deleteForm = useForm({});

function confirmDelete(groupId) {
    if (confirm('Вы уверены, что хотите удалить эту группу каналов? Каналы в группе не будут удалены.')) {
        deleteForm.delete(route('groups.destroy', groupId));
    }
}

function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}
</script> 