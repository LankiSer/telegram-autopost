<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Без навигационной шапки, только содержимое -->
        <main>
            <slot />
        </main>
    </div>
</template> 