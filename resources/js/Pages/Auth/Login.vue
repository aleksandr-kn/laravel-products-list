<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white rounded-lg shadow p-8 max-w-sm w-full mx-4">
            <h1 class="text-xl font-bold text-center mb-6">Вход в админку</h1>

            <div v-if="error" class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
                {{ error }}
            </div>

            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="email"
                        type="email"
                        required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="admin@example.com"
                    />
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Пароль
                    </label>
                    <input
                        id="password"
                        v-model="password"
                        type="password"
                        required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите пароль"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-blue-600 text-white rounded py-2 font-medium hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ loading ? 'Вход...' : 'Войти' }}
                </button>
            </form>

            <a href="/" class="block text-center text-sm text-gray-500 hover:underline mt-4">
                Вернуться в каталог
            </a>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '@/Composables/useAuth.js';

const email = ref('');
const password = ref('');

const { error, loading, login } = useAuth();

function handleLogin() {
    login(email.value, password.value);
}
</script>
