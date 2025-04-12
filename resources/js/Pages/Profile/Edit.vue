<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Профиль" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Профиль
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8 border border-gray-200 dark:border-gray-700">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8 border border-gray-200 dark:border-gray-700">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8 border border-gray-200 dark:border-gray-700">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
