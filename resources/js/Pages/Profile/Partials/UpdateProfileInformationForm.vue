<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    openai_api_key: user.openai_api_key || '',
    telegram_bot_token: user.telegram_bot_token || '',
    telegram_bot_name: user.telegram_bot_name || '',
    telegram_bot_username: user.telegram_bot_username || '',
    telegram_bot_description: user.telegram_bot_description || '',
    telegram_bot_link: user.telegram_bot_link || '',
});

const isAdmin = user.is_admin || false;
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Информация профиля
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Обновите информацию профиля и адрес электронной почты вашей учетной записи.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Имя" class="text-gray-700 dark:text-gray-300" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Настройки для администратора -->
            <div v-if="isAdmin" class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Настройки интеграций
                </h3>
                
                <!-- OpenAI API Key -->
                <div class="mb-4">
                    <InputLabel for="openai_api_key" value="OpenAI API Key (ChatGPT)" class="text-gray-700 dark:text-gray-300" />
                    
                    <TextInput
                        id="openai_api_key"
                        type="password"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        v-model="form.openai_api_key"
                        placeholder="sk-..."
                    />
                    
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Ключ API для использования ChatGPT в автопостинге
                    </p>
                    
                    <InputError class="mt-1" :message="form.errors.openai_api_key" />
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Настройки Telegram-бота
                </h3>
                
                <!-- Telegram Bot Token -->
                <div class="mb-4">
                    <InputLabel for="telegram_bot_token" value="Токен Telegram-бота" class="text-gray-700 dark:text-gray-300" />
                    
                    <TextInput
                        id="telegram_bot_token"
                        type="password"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        v-model="form.telegram_bot_token"
                        placeholder="1234567890:ABCDEFGHIJKLMNOPQRSTUVWXYZ..."
                    />
                    
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Токен бота, полученный у @BotFather
                    </p>
                    
                    <InputError class="mt-1" :message="form.errors.telegram_bot_token" />
                </div>
                
                <!-- Telegram Bot Name -->
                <div class="mb-4">
                    <InputLabel for="telegram_bot_name" value="Имя бота" class="text-gray-700 dark:text-gray-300" />
                    
                    <TextInput
                        id="telegram_bot_name"
                        type="text"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        v-model="form.telegram_bot_name"
                    />
                    
                    <InputError class="mt-1" :message="form.errors.telegram_bot_name" />
                </div>
                
                <!-- Telegram Bot Username -->
                <div class="mb-4">
                    <InputLabel for="telegram_bot_username" value="Username бота" class="text-gray-700 dark:text-gray-300" />
                    
                    <TextInput
                        id="telegram_bot_username"
                        type="text"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        v-model="form.telegram_bot_username"
                        placeholder="your_bot_username"
                    />
                    
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Имя пользователя бота без символа '@'
                    </p>
                    
                    <InputError class="mt-1" :message="form.errors.telegram_bot_username" />
                </div>
                
                <!-- Telegram Bot Description -->
                <div class="mb-4">
                    <InputLabel for="telegram_bot_description" value="Описание бота" class="text-gray-700 dark:text-gray-300" />
                    
                    <textarea
                        id="telegram_bot_description"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        v-model="form.telegram_bot_description"
                        rows="3"
                    ></textarea>
                    
                    <InputError class="mt-1" :message="form.errors.telegram_bot_description" />
                </div>
                
                <!-- Telegram Bot Link -->
                <div class="mb-4">
                    <InputLabel for="telegram_bot_link" value="Ссылка на бота" class="text-gray-700 dark:text-gray-300" />
                    
                    <TextInput
                        id="telegram_bot_link"
                        type="url"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        v-model="form.telegram_bot_link"
                        placeholder="https://t.me/your_bot_username"
                    />
                    
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Полная ссылка на бота в Telegram
                    </p>
                    
                    <InputError class="mt-1" :message="form.errors.telegram_bot_link" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-300">
                    Ваш адрес электронной почты не подтвержден.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 dark:text-gray-400 underline hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        Нажмите здесь, чтобы повторно отправить письмо для подтверждения.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    Новая ссылка для подтверждения была отправлена на ваш адрес электронной почты.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">Сохранить</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Сохранено.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
