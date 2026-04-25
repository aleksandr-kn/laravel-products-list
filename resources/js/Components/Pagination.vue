<template>
    <div v-if="meta && meta.last_page > 1" class="flex justify-center gap-1 mt-6">
        <button
            v-for="page in meta.last_page"
            :key="page"
            class="px-3 py-1 rounded text-sm"
            :class="page === meta.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            @click="$emit('page-change', page)"
        >
            {{ page }}
        </button>
    </div>
</template>

<script setup>
defineProps({
    meta: {
        type: Object,
        default: null,
    },
});

defineEmits(['page-change']);
</script>
