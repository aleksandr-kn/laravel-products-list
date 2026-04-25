import { ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const token = ref(localStorage.getItem('token'));

export function useAuth() {
    const error = ref(null);
    const loading = ref(false);

    function isAuthenticated() {
        return !!token.value;
    }

    function getToken() {
        return token.value;
    }

    async function login(email, password) {
        error.value = null;
        loading.value = true;

        try {
            const res = await axios.post('/api/login', { email, password });
            token.value = res.data.token;
            localStorage.setItem('token', token.value);
            router.visit('/admin/products');
        } catch (e) {
            error.value = e.response?.data?.message || 'Ошибка авторизации';
        } finally {
            loading.value = false;
        }
    }

    function logout() {
        token.value = null;
        localStorage.removeItem('token');
        router.visit('/');
    }

    return { token, error, loading, isAuthenticated, getToken, login, logout };
}
