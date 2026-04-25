import { ref } from 'vue';
import axios from 'axios';

export function useProductApi() {
    const products = ref([]);
    const product = ref(null);
    const pagination = ref(null);
    const loading = ref(false);
    const error = ref(null);

    function authHeaders() {
        const token = localStorage.getItem('token');
        return token ? { Authorization: `Bearer ${token}` } : {};
    }

    async function getProducts(params = {}) {
        loading.value = true;
        error.value = null;

        try {
            const res = await axios.get('/api/products', { params });
            products.value = res.data.data;
            pagination.value = res.data.meta;
        } catch (e) {
            error.value = e.response?.data?.message || 'Ошибка загрузки товаров';
        } finally {
            loading.value = false;
        }
    }

    async function getProduct(id) {
        loading.value = true;
        error.value = null;

        try {
            const res = await axios.get(`/api/products/${id}`);
            product.value = res.data.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Товар не найден';
        } finally {
            loading.value = false;
        }
    }

    async function createProduct(data) {
        loading.value = true;
        error.value = null;

        try {
            const res = await axios.post('/api/products', data, {
                headers: authHeaders(),
            });
            return res.data.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Ошибка создания товара';
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function updateProduct(id, data) {
        loading.value = true;
        error.value = null;

        try {
            const res = await axios.put(`/api/products/${id}`, data, {
                headers: authHeaders(),
            });
            return res.data.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Ошибка обновления товара';
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function deleteProduct(id) {
        loading.value = true;
        error.value = null;

        try {
            await axios.delete(`/api/products/${id}`, {
                headers: authHeaders(),
            });
        } catch (e) {
            error.value = e.response?.data?.message || 'Ошибка удаления товара';
            throw e;
        } finally {
            loading.value = false;
        }
    }

    return {
        products,
        product,
        pagination,
        loading,
        error,
        getProducts,
        getProduct,
        createProduct,
        updateProduct,
        deleteProduct,
    };
}
