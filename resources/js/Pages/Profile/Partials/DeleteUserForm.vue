<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Удаление аккаунта
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                После удаления вашего аккаунта все его ресурсы и данные будут удалены безвозвратно. 
                Перед удалением загрузите данные или информацию, которую хотите сохранить.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600">Удалить аккаунт</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Вы уверены, что хотите удалить свой аккаунт?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    После удаления вашего аккаунта все его ресурсы и данные будут удалены безвозвратно. 
                    Пожалуйста, введите свой пароль для подтверждения того, что вы хотите навсегда удалить свой аккаунт.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Пароль"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        placeholder="Пароль"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                        Отмена
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Удалить аккаунт
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
