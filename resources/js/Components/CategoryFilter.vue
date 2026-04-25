<template>
    <div class="flex items-center gap-2">
        <label for="category" class="text-sm text-gray-600">Категория:</label>
        <select
            id="category"
            :value="modelValue"
            class="border rounded px-3 py-1.5 text-sm bg-white"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">Все категории</option>
            <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
            >
                {{ category.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['update:modelValue']);
</script>
