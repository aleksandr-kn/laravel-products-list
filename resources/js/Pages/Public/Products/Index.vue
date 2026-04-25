<template>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Каталог товаров</h1>
            <a href="/login" class="text-sm text-blue-600 hover:underline">Войти</a>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
            <input
                v-model="search"
                type="text"
                placeholder="Поиск по названию..."
                class="border rounded px-3 py-1.5 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <CategoryFilter
                v-model="selectedCategory"
                :categories="categories"
            />
        </div>

        <div v-if="loading" class="text-center text-gray-500 py-12">
            Загрузка...
        </div>

        <div v-else-if="productList.length === 0" class="text-center text-gray-500 py-12">
            Товары не найдены
        </div>

        <div v-else class="grid gap-4">
            <a
                v-for="item in productList"
                :key="item.id"
                :href="`/products/${item.id}`"
                class="block bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-semibold">{{ item.name }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ item.category?.name }}</p>
                        <p class="text-gray-600 mt-2 line-clamp-2">{{ item.description }}</p>
                    </div>
                    <span class="text-lg font-bold text-blue-600 whitespace-nowrap ml-4">
                        {{ item.price }} ₽
                    </span>
                </div>
            </a>
        </div>

        <Pagination :meta="paginationMeta" @page-change="goToPage" />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useProductApi } from '@/Composables/useProductApi.js';
import CategoryFilter from '@/Components/CategoryFilter.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const { products: productList, pagination: paginationMeta, loading, getProducts } = useProductApi();

const selectedCategory = ref('');
const search = ref('');
let debounceTimer = null;

// при первом рендере берем данные из Inertia props
productList.value = props.products?.data || [];
paginationMeta.value = props.products?.meta || null;

function fetchProducts(page = 1) {
    getProducts({
        category_id: selectedCategory.value || undefined,
        search: search.value.length >= 2 ? search.value : undefined,
        page,
    });
}

// при смене категории сбрасываем поиск
// двумя фильтрами одновременно
watch(selectedCategory, () => {
    search.value = '';
    fetchProducts();
});

// поиск с дебаунсом 300мс, минимум 2 символа
watch(search, (val) => {
    clearTimeout(debounceTimer);
    if (val.length >= 2) {
        debounceTimer = setTimeout(() => fetchProducts(), 300);
    }
});

function goToPage(page) {
    fetchProducts(page);
}
</script>
