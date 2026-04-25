<template>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <a href="/admin/products" class="text-blue-600 hover:underline text-sm">&larr; Назад к списку</a>

        <h1 class="text-2xl font-bold mt-4 mb-6">
            {{ isEditing ? 'Редактирование товара' : 'Новый товар' }}
        </h1>

        <div v-if="error" class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            {{ error }}
        </div>

        <form class="bg-white rounded-lg shadow p-6" @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Название
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Категория
                </label>
                <select
                    id="category"
                    v-model="form.category_id"
                    required
                    class="w-full border rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="" disabled>Выберите категорию</option>
                    <option
                        v-for="cat in categories"
                        :key="cat.id"
                        :value="cat.id"
                    >
                        {{ cat.name }}
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Описание
                </label>
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>

            <div class="mb-6">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                    Цена
                </label>
                <input
                    id="price"
                    v-model="form.price"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="flex justify-end gap-3">
                <a
                    href="/admin/products"
                    class="px-4 py-2 text-gray-600 bg-gray-100 rounded hover:bg-gray-200"
                >
                    Отмена
                </a>
                <button
                    type="submit"
                    :disabled="loading"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ loading ? 'Сохранение...' : 'Сохранить' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/Composables/useAuth.js';
import { useProductApi } from '@/Composables/useProductApi.js';

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const { isAuthenticated } = useAuth();
const { loading, error, createProduct, updateProduct } = useProductApi();

const isEditing = computed(() => !!props.product);

const form = ref({
    name: props.product?.name || '',
    description: props.product?.description || '',
    price: props.product?.price || '',
    category_id: props.product?.category?.id || '',
});

onMounted(() => {
    if (!isAuthenticated()) {
        window.location.href = '/login';
    }
});

async function handleSubmit() {
    try {
        if (isEditing.value) {
            await updateProduct(props.product.id, form.value);
        } else {
            await createProduct(form.value);
        }
        window.location.href = '/admin/products';
    } catch (e) {
        // ошибка уже записана в error через composable
    }
}
</script>
