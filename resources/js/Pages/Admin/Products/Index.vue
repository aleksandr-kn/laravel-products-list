<template>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Управление товарами</h1>
            <div class="flex gap-3">
                <a
                    href="/admin/products/create"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
                >
                    Добавить товар
                </a>
                <button
                    class="px-4 py-2 text-gray-600 bg-gray-100 rounded hover:bg-gray-200 text-sm"
                    @click="handleLogout"
                >
                    Выйти
                </button>
            </div>
        </div>

        <div v-if="productList.length === 0" class="text-center text-gray-500 py-12">
            Товаров пока нет
        </div>

        <table v-else class="w-full bg-white rounded-lg shadow">
            <thead>
                <tr class="border-b text-left text-sm text-gray-500">
                    <th class="px-4 py-3">Название</th>
                    <th class="px-4 py-3">Категория</th>
                    <th class="px-4 py-3">Цена</th>
                    <th class="px-4 py-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="item in productList"
                    :key="item.id"
                    class="border-b last:border-b-0 hover:bg-gray-50"
                >
                    <td class="px-4 py-3">{{ item.name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ item.category?.name }}</td>
                    <td class="px-4 py-3">{{ item.price }} ₽</td>
                    <td class="px-4 py-3 text-right">
                        <a
                            :href="`/admin/products/${item.id}/edit`"
                            class="text-blue-600 hover:underline text-sm mr-3"
                        >
                            Редактировать
                        </a>
                        <button
                            class="text-red-600 hover:underline text-sm"
                            @click="confirmDelete(item)"
                        >
                            Удалить
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :meta="paginationMeta" @page-change="goToPage" />

        <ConfirmModal
            :show="showModal"
            title="Удаление товара"
            :message="`Удалить '${deletingProduct?.name}'?`"
            @confirm="handleDelete"
            @cancel="showModal = false"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '@/Composables/useAuth.js';
import { useProductApi } from '@/Composables/useProductApi.js';
import Pagination from '@/Components/Pagination.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
});

const { logout, isAuthenticated } = useAuth();
const { products: productList, pagination: paginationMeta, getProducts, deleteProduct } = useProductApi();

const showModal = ref(false);
const deletingProduct = ref(null);

// берем данные из Inertia props
productList.value = props.products?.data || [];
paginationMeta.value = props.products?.meta || null;

// если нет токена - редиректим на логин
onMounted(() => {
    if (!isAuthenticated()) {
        window.location.href = '/login';
    }
});

function confirmDelete(item) {
    deletingProduct.value = item;
    showModal.value = true;
}

async function handleDelete() {
    try {
        await deleteProduct(deletingProduct.value.id);

        // если на странице был последний товар - возвращаемся на предыдущую
        const currentPage = paginationMeta.value?.current_page || 1;
        const isLastOnPage = productList.value.length === 1;
        const page = isLastOnPage && currentPage > 1 ? currentPage - 1 : currentPage;

        await getProducts({ page });
        showModal.value = false;
        deletingProduct.value = null;
    } catch (e) {
        showModal.value = false;
    }
}

function handleLogout() {
    logout();
}

function goToPage(page) {
    getProducts({ page });
}
</script>
