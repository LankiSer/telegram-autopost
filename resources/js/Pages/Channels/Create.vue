<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    isModal: {
        type: Boolean,
        default: false
    }
});

const form = useForm({
    name: '',
    description: '',
    type: 'telegram',
    telegram_username: '',
    content_prompt: '',
});

const submit = () => {
    form.post(route('channels.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Создание канала" />

    <AuthenticatedLayout :hideTopbar="isModal">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Создание нового канала
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Основная информация -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="name" value="Название канала" />
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

                                <div>
                                    <InputLabel for="type" value="Тип канала" />
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="telegram">Telegram</option>
                                        <option value="vk">ВКонтакте</option>
                                    </select>
                                    <InputError :message="form.errors.type" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="description" value="Описание канала" />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div v-if="form.type === 'telegram'" class="mb-6">
                                <InputLabel for="telegram_username" value="Username канала в Telegram" />
                                <TextInput
                                    id="telegram_username"
                                    v-model="form.telegram_username"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="@channel_name"
                                />
                                <p class="mt-1 text-sm text-gray-500">Добавьте бота @your_bot_name в качестве администратора канала.</p>
                                <InputError :message="form.errors.telegram_username" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="content_prompt" value="Подсказка для генерации контента (опционально)" />
                                <TextArea
                                    id="content_prompt"
                                    v-model="form.content_prompt"
                                    class="mt-1 block w-full"
                                    rows="4"
                                    placeholder="Например: Этот канал о новостях технологий и IT. Посты должны быть информативными с акцентом на последние инновации, новости стартапов и технологических гигантов."
                                />
                                <p class="mt-1 text-sm text-gray-500">Эта подсказка будет использоваться для настройки генерации контента для данного канала.</p>
                                <InputError :message="form.errors.content_prompt" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :disabled="form.processing" class="ml-4">
                                    <span v-if="form.processing">Сохранение...</span>
                                    <span v-else>Создать канал</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 