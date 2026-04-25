<template>
    <div class="max-w-3xl mx-auto px-4 py-8">
        <a href="/" class="text-blue-600 hover:underline text-sm">&larr; Назад к каталогу</a>

        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="flex justify-between items-start">
                <h1 class="text-2xl font-bold">{{ product.name }}</h1>
                <span class="text-2xl font-bold text-blue-600">{{ product.price }} ₽</span>
            </div>

            <p class="text-sm text-gray-500 mt-2">
                Категория: {{ product.category?.name }}
            </p>

            <p class="text-gray-700 mt-4 leading-relaxed">{{ product.description }}</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const product = props.product?.data || props.product;
</script>
