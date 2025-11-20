import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import axios from 'axios';
import type { Vet } from './geographyStore';

interface VetFilters {
    search: string;
    active_only: boolean;
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

export const useVetStore = defineStore('vets', () => {
    const vets = ref<Vet[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const pagination = reactive<Pagination>(defaultPagination());

    const filters = reactive<VetFilters>({
        search: '',
        active_only: false,
    });

    const creating = ref(false);
    const createError = ref<string | null>(null);

    const updating = ref(false);
    const updateError = ref<string | null>(null);

    const selectedVet = ref<Vet | null>(null);

    const applyFiltersToParams = (page?: number) => {
        const params: Record<string, any> = {};
        if (filters.search) params.search = filters.search;
        if (filters.active_only) params.active_only = true;
        if (page) params.page = page;
        return params;
    };

    const normalizePagination = (payload: any) => {
        const meta = payload?.meta ?? payload;
        pagination.current_page = Number(meta?.current_page ?? 1);
        pagination.per_page = Number(meta?.per_page ?? meta?.per_page ?? 15);
        pagination.total = Number(meta?.total ?? payload?.total ?? 0);
        pagination.last_page = Number(meta?.last_page ?? payload?.last_page ?? 1);
    };

    const normalizeData = (payload: any): Vet[] => {
        if (Array.isArray(payload)) {
            normalizePagination({ total: payload.length, current_page: 1, per_page: payload.length, last_page: 1 });
            return payload;
        }

        if (Array.isArray(payload?.data)) {
            normalizePagination(payload?.meta ?? payload);
            return payload.data;
        }

        normalizePagination(defaultPagination());
        return [];
    };

    const fetchVets = async (page = 1) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get('/vets', {
                params: applyFiltersToParams(page),
            });
            const records = normalizeData(response.data);
            vets.value = records;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to load vets.';
            vets.value = [];
            normalizePagination(defaultPagination());
        } finally {
            loading.value = false;
        }
    };

    const getVet = async (id: number | string) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`/vets/${id}`);
            selectedVet.value = response.data as Vet;
            return selectedVet.value;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch vet.';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const createVet = async (payload: Partial<Vet>) => {
        creating.value = true;
        createError.value = null;
        try {
            const response = await axios.post('/vets', payload);
            await fetchVets();
            return response.data as Vet;
        } catch (err: any) {
            createError.value = err.response?.data?.message || 'Failed to create vet.';
            throw err;
        } finally {
            creating.value = false;
        }
    };

    const updateVet = async (id: number | string, payload: Partial<Vet>) => {
        updating.value = true;
        updateError.value = null;
        try {
            const response = await axios.put(`/vets/${id}`, payload);
            await fetchVets(pagination.current_page);
            if (selectedVet.value?.id === Number(id)) {
                selectedVet.value = response.data as Vet;
            }
            return response.data as Vet;
        } catch (err: any) {
            updateError.value = err.response?.data?.message || 'Failed to update vet.';
            throw err;
        } finally {
            updating.value = false;
        }
    };

    const toggleVetActive = async (vet: Vet) => {
        return updateVet(vet.id, { is_active: !vet.is_active });
    };

    const setFilter = <K extends keyof VetFilters>(key: K, value: VetFilters[K]) => {
        filters[key] = value;
    };

    const resetFilters = async () => {
        filters.search = '';
        filters.active_only = false;
        await fetchVets();
    };

    const getActiveVets = async () => {
        try {
            const response = await axios.get('/vets', {
                params: { active_only: true, per_page: 1000 },
            });
            const payload = response.data ?? [];
            if (Array.isArray(payload?.data)) {
                return payload.data as Vet[];
            }
            if (Array.isArray(payload)) {
                return payload as Vet[];
            }
            return [];
        } catch {
            return [];
        }
    };

    return {
        vets,
        loading,
        error,
        filters,
        pagination,
        creating,
        createError,
        updating,
        updateError,
        selectedVet,
        fetchVets,
        getVet,
        createVet,
        updateVet,
        toggleVetActive,
        setFilter,
        resetFilters,
        getActiveVets,
    };
});
