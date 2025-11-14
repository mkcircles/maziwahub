import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import authService, { type LoginCredentials, type RegisterData } from '../services/authService';

interface User {
    id: number;
    name: string;
    email: string;
    user_type: 'super_admin' | 'admin' | 'partner' | 'mcc' | 'user';
    milk_collection_center_id?: number;
    partner_id?: number;
    is_active: boolean;
    milk_collection_center?: any;
    partner?: any;
    agent?: any;
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(authService.getUserFromStorage());
    const token = ref<string | null>(authService.getAuthToken());
    const loading = ref(false);
    const error = ref<string | null>(null);

    const isAuthenticated = computed(() => !!token.value && !!user.value);

    async function login(credentials: LoginCredentials) {
        loading.value = true;
        error.value = null;
        try {
            const response = await authService.login(credentials);
            user.value = response.user;
            token.value = response.token;
            return response;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Login failed';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function register(data: RegisterData) {
        loading.value = true;
        error.value = null;
        try {
            const response = await authService.register(data);
            user.value = response.user;
            token.value = response.token;
            return response;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Registration failed';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        loading.value = true;
        try {
            await authService.logout();
        } finally {
            user.value = null;
            token.value = null;
            loading.value = false;
        }
    }

    async function fetchUser() {
        loading.value = true;
        try {
            const fetchedUser = await authService.getUser();
            if (fetchedUser) {
                user.value = fetchedUser;
            } else {
                user.value = null;
                token.value = null;
            }
        } catch (err) {
            user.value = null;
            token.value = null;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    return {
        user,
        token,
        loading,
        error,
        isAuthenticated,
        login,
        register,
        logout,
        fetchUser,
        clearError,
    };
});

