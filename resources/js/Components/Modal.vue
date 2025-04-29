<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div v-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" scroll-region>
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="show" class="fixed inset-0 transform transition-all" @click="close">
                        <div class="absolute inset-0 bg-gray-500 opacity-75" />
                    </div>
                </transition>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="show"
                        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto"
                        :class="maxWidthClass"
                    >
                        <div v-if="!hideHeader" class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <slot name="header">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <slot name="title" />
                                    </h3>
                                </slot>
                                <button
                                    v-if="closeable"
                                    type="button"
                                    class="text-gray-400 hover:text-gray-500"
                                    @click="close"
                                >
                                    <span class="sr-only">Close</span>
                                    <svg
                                        class="h-6 w-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <slot />
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
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
        hideHeader: {
            type: Boolean,
            default: false,
        },
    },

    emits: ['close'],

    watch: {
        show: {
            immediate: true,
            handler: function(show) {
                if (show) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = null;
                }
            },
        },
    },

    computed: {
        maxWidthClass() {
            return {
                sm: 'sm:max-w-sm',
                md: 'sm:max-w-md',
                lg: 'sm:max-w-lg',
                xl: 'sm:max-w-xl',
                '2xl': 'sm:max-w-2xl',
                '3xl': 'sm:max-w-3xl',
                '4xl': 'sm:max-w-4xl',
                '5xl': 'sm:max-w-5xl',
                '6xl': 'sm:max-w-6xl',
                '7xl': 'sm:max-w-7xl',
            }[this.maxWidth];
        },
    },

    methods: {
        close() {
            if (this.closeable) {
                this.$emit('close');
            }
        },
    },
});
</script>
