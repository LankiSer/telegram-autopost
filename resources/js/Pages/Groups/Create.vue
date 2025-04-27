<template>
    <Head title="Создание группы каналов" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <Link :href="route('groups.index')" class="mr-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Создание группы каналов
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Group name -->
                            <div class="mb-6">
                                <InputLabel for="name" value="Название группы" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Group description -->
                            <div class="mb-6">
                                <InputLabel for="description" value="Описание группы" />
                                <TextArea
                                    id="description"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                    placeholder="Необязательное описание группы"
                                    rows="4"
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Publish settings -->
                            <div class="mb-6">
                                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Настройки публикации</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox id="auto_publish" v-model:checked="form.auto_publish" />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <InputLabel for="auto_publish" value="Автоматическая публикация" />
                                            <p class="text-gray-500 dark:text-gray-400">Посты будут автоматически публиковаться после создания (без модерации)</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox id="use_signature" v-model:checked="form.use_signature" />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <InputLabel for="use_signature" value="Использовать подпись" />
                                            <p class="text-gray-500 dark:text-gray-400">Добавлять стандартную подпись к каждому посту</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Moderation settings -->
                            <div class="mb-6">
                                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Настройки модерации</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox id="moderation_enabled" v-model:checked="form.moderation_enabled" name="moderation_enabled" />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <InputLabel for="moderation_enabled" value="Включить модерацию" />
                                            <p class="text-gray-500 dark:text-gray-400">Каждый пост будет проходить модерацию перед публикацией</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start" v-if="form.moderation_enabled">
                                        <div class="flex items-center h-5">
                                            <Checkbox id="auto_approve" v-model:checked="form.auto_approve" name="auto_approve" />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <InputLabel for="auto_approve" value="Авто-одобрение" />
                                            <p class="text-gray-500 dark:text-gray-400">Автоматически одобрять посты, не содержащие запрещенных слов</p>
                                        </div>
                                    </div>
                                    
                                    <div v-if="form.moderation_enabled">
                                        <InputLabel for="restricted_words" value="Запрещенные слова (через запятую)" />
                                        <TextInput
                                            id="restricted_words"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.restricted_words"
                                            placeholder="слово1, слово2, слово3"
                                        />
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Посты, содержащие эти слова, будут отмечены для модерации</p>
                                        <InputError class="mt-2" :message="form.errors.restricted_words" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <SecondaryButton as="a" :href="route('groups.index')" class="mr-3">
                                    Отмена
                                </SecondaryButton>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const form = useForm({
    name: '',
    description: '',
    auto_publish: false,
    use_signature: true,
    moderation_enabled: true,
    auto_approve: false,
    restricted_words: '',
});

const submit = () => {
    form.post(route('groups.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script> 