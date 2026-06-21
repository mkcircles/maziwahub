import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import axios from 'axios';

export interface User {
    id: number;
    name: string;
    email: string;
    user_type: 'super_admin' | 'admin' | 'partner' | 'mcc' | 'user' | 'agent';
    milk_collection_center_id?: number | null;
    partner_id?: number | null;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
    milk_collection_center?: any;
    partner?: any;
    agent?: any;
}

interface UserFilters {
    search: string;
    user_type: string;
    is_active: string;
}

interface Pagination {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

const defaultPagination = (): Pagination => ({
    current_page: 1,
    per_page: 15,
    total: 0,
    last_page: 1,
});

export const useUserStore = defineStore('users', () => {
    const users = ref<User[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const pagination = reactive<Pagination>(defaultPagination());

    const filters = reactive<UserFilters>({
        search: '',
        user_type: '',
        is_active: '',
    });

    const creating = ref(false);
    const createError = ref<string | null>(null);

    const updating = ref(false);
    const updateError = ref<string | null>(null);

    const selectedUser = ref<User | null>(null);

    const applyFiltersToParams = (page?: number) => {
        const params: Record<string, any> = {};
        if (filters.search) params.search = filters.search;
        if (filters.user_type) params.user_type = filters.user_type;
        if (filters.is_active !== '') params.is_active = filters.is_active === 'true';
        if (page) params.page = page;
        return params;
    };

    const normalizePagination = (payload: any) => {
        const meta = payload?.meta ?? payload;
        pagination.current_page = Number(meta?.current_page ?? 1);
        pagination.per_page = Number(meta?.per_page ?? 15);
        pagination.total = Number(meta?.total ?? payload?.total ?? 0);
        pagination.last_page = Number(meta?.last_page ?? payload?.last_page ?? 1);
    };

    const normalizeData = (payload: any): User[] => {
        if (Array.isArray(payload)) {
            normalizePagination({ total: payload.length, current_page: 1, per_page: payload.length, last_page: 1 });
            return payload;
        }

        if (Array.isArray(payload?.data)) {
            normalizePagination(payload);
            return payload.data;
        }

        normalizePagination(defaultPagination());
        return [];
    };

    const fetchUsers = async (page = 1) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get('/users', {
                params: applyFiltersToParams(page),
            });
            const records = normalizeData(response.data);
            users.value = records;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to load users.';
            users.value = [];
            normalizePagination(defaultPagination());
        } finally {
            loading.value = false;
        }
    };

    const getUser = async (id: number | string) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`/users/${id}`);
            selectedUser.value = response.data as User;
            return selectedUser.value;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch user details.';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const createUser = async (payload: Partial<User> & { password?: string }) => {
        creating.value = true;
        createError.value = null;
        try {
            const response = await axios.post('/users', payload);
            await fetchUsers();
            return response.data as User;
        } catch (err: any) {
            createError.value = err.response?.data?.message || 'Failed to create user.';
            throw err;
        } finally {
            creating.value = false;
        }
    };

    const updateUser = async (id: number | string, payload: Partial<User> & { password?: string }) => {
        updating.value = true;
        updateError.value = null;
        try {
            const response = await axios.put(`/users/${id}`, payload);
            await fetchUsers(pagination.current_page);
            if (selectedUser.value?.id === Number(id)) {
                selectedUser.value = response.data as User;
            }
            return response.data as User;
        } catch (err: any) {
            updateError.value = err.response?.data?.message || 'Failed to update user.';
            throw err;
        } finally {
            updating.value = false;
        }
    };

    const toggleUserActive = async (user: User) => {
        updating.value = true;
        updateError.value = null;
        try {
            const response = await axios.post(`/users/${user.id}/toggle-active`);
            await fetchUsers(pagination.current_page);
            if (selectedUser.value?.id === user.id) {
                selectedUser.value = response.data.user as User;
            }
            return response.data.user as User;
        } catch (err: any) {
            updateError.value = err.response?.data?.message || 'Failed to toggle user status.';
            throw err;
        } finally {
            updating.value = false;
        }
    };

    const setFilter = <K extends keyof UserFilters>(key: K, value: UserFilters[K]) => {
        filters[key] = value;
    };

    const resetFilters = async () => {
        filters.search = '';
        filters.user_type = '';
        filters.is_active = '';
        await fetchUsers();
    };

    return {
        users,
        loading,
        error,
        filters,
        pagination,
        creating,
        createError,
        updating,
        updateError,
        selectedUser,
        fetchUsers,
        getUser,
        createUser,
        updateUser,
        toggleUserActive,
        setFilter,
        resetFilters,
    };
});
