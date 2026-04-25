<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('cancel')"></div>
        <div class="relative bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-2">{{ title }}</h3>
            <p class="text-gray-600 mb-6">{{ message }}</p>
            <div class="flex justify-end gap-3">
                <button
                    class="px-4 py-2 text-gray-600 bg-gray-100 rounded hover:bg-gray-200"
                    @click="$emit('cancel')"
                >
                    Отмена
                </button>
                <button
                    class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700"
                    @click="$emit('confirm')"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Подтверждение',
    },
    message: {
        type: String,
        default: 'Вы уверены?',
    },
    confirmText: {
        type: String,
        default: 'Удалить',
    },
});

defineEmits(['confirm', 'cancel']);
</script>
