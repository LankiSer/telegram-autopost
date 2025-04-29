<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="bg-white p-4 sm:px-6 sm:py-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="[iconClasses]">
                    <slot name="icon" />
                </div>

                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-medium text-gray-900">
                        <slot name="title" />
                    </h3>

                    <div class="mt-2">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-100 text-right">
            <slot name="footer" />
        </div>
    </Modal>
</template>

<script>
import { defineComponent } from 'vue';
import Modal from './Modal.vue';

export default defineComponent({
    components: {
        Modal,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        maxWidth: {
            type: String,
            default: '2xl',
        },
        closeable: {
            type: Boolean,
            default: true,
        },
        iconClasses: {
            type: String,
            default: 'bg-red-100 text-red-600',
        },
    },

    emits: ['close'],

    methods: {
        close() {
            this.$emit('close');
        },
    },
});
</script> 