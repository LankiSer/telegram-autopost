<template>
    <div 
        class="border rounded p-4 flex items-start"
        :class="{'border-blue-500 bg-blue-50': modelValue.includes(channel.id), 'border-gray-200': !modelValue.includes(channel.id)}"
    >
        <div class="mr-3 pt-1">
            <input 
                type="checkbox" 
                :id="`channel-${channel.id}`" 
                :value="channel.id" 
                v-model="localValue"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5"
            />
        </div>
        <div>
            <label :for="`channel-${channel.id}`" class="font-medium text-gray-700 cursor-pointer">
                {{ channel.name }}
            </label>
            <p v-if="channel.username" class="text-sm text-gray-500 mt-1">
                @{{ channel.username }}
            </p>
            <p v-else-if="channel.telegram_username" class="text-sm text-gray-500 mt-1">
                @{{ channel.telegram_username }}
            </p>
            <p v-if="channel.members_count" class="text-xs text-gray-500 mt-1">
                Подписчиков: {{ formatNumber(channel.members_count) }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    channel: {
        type: Object,
        required: true
    },
    modelValue: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const localValue = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

const formatNumber = (number) => {
    return new Intl.NumberFormat('ru-RU').format(number);
};
</script> 