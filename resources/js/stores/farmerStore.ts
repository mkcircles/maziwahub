import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import axios from 'axios';
import type { Farmer } from './geographyStore';

interface FarmerFilters {
    search: string;
    status: string;
    is_farmer_insured: string;
    reg_type: string;
    per_page: number;
    page: number;
}

interface PaginationState {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface FarmerMetrics {
    total: number;
    active: number;
    pending: number;
    insured: number;
}

const defaultFilters = (): FarmerFilters => ({
    search: '',
    status: '',
    is_farmer_insured: '',
    reg_type: '',
    per_page: 15,
    page: 1,
});

const initialPagination = (): PaginationState => ({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
});

const initialMetrics = (): FarmerMetrics => ({
    total: 0,
    active: 0,
    pending: 0,
    insured: 0,
});

export const useFarmerStore = defineStore('farmers', () => {
    const farmers = ref<Farmer[]>([]);
    const selectedFarmer = ref<Farmer | null>(null);
    const pagination = reactive<PaginationState>(initialPagination());
    const filters = reactive<FarmerFilters>(defaultFilters());

    const loading = ref(false);
    const metricsLoading = ref(false);
    const detailLoading = ref(false);
    const error = ref<string | null>(null);
    const detailError = ref<string | null>(null);
    const metrics = reactive<FarmerMetrics>(initialMetrics());
    const creating = ref(false);
    const createError = ref<string | null>(null);
    const updating = ref(false);
    const updateError = ref<string | null>(null);
    const creatingCow = ref(false);
    const createCowError = ref<string | null>(null);

    const applyPagination = (payload: any) => {
        const meta = payload.meta ?? {};
        pagination.current_page = payload.current_page ?? meta.current_page ?? filters.page ?? 1;
        pagination.last_page = payload.last_page ?? meta.last_page ?? 1;
        pagination.per_page = payload.per_page ?? meta.per_page ?? filters.per_page ?? 15;
        pagination.total = payload.total ?? meta.total ?? (payload.data?.length ?? 0);
    };

    const extractData = (payload: any): Farmer[] => {
        if (Array.isArray(payload)) {
            return payload;
        }

        if (Array.isArray(payload.data)) {
            return payload.data;
        }

        return [];
    };

    async function fetchFarmers(extra: Partial<FarmerFilters> = {}) {
        loading.value = true;
        error.value = null;

        const params = {
            ...filters,
            ...extra,
        };

        filters.page = params.page ?? filters.page;
        filters.per_page = params.per_page ?? filters.per_page;

        try {
            const response = await axios.get('/farmers', {
                params,
            });

            const payload = response.data ?? {};
            farmers.value = extractData(payload);
            applyPagination(payload);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to load farmers.';
            farmers.value = [];
            applyPagination(initialPagination());
        } finally {
            loading.value = false;
        }
    }

    async function getCount(extraParams: Record<string, any>) {
        const response = await axios.get('/farmers', {
            params: {
                per_page: 1,
                page: 1,
                ...extraParams,
            },
        });

        const payload = response.data ?? {};
        const meta = payload.meta ?? {};

        if (typeof payload.total === 'number') {
            return payload.total;
        }
        if (typeof meta.total === 'number') {
            return meta.total;
        }

        if (Array.isArray(payload.data)) {
            return payload.data.length;
        }

        if (Array.isArray(payload)) {
            return payload.length;
        }

        return 0;
    }

    async function fetchMetrics() {
        metricsLoading.value = true;
        error.value = null;

        try {
            const [total, active, pending, insured] = await Promise.all([
                getCount({}),
                getCount({ status: 'active' }),
                getCount({ status: 'pending' }),
                getCount({ is_farmer_insured: true }),
            ]);

            metrics.total = total;
            metrics.active = active;
            metrics.pending = pending;
            metrics.insured = insured;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch farmer metrics.';
            Object.assign(metrics, initialMetrics());
        } finally {
            metricsLoading.value = false;
        }
    }

    async function fetchFarmer(id: number | string) {
        detailLoading.value = true;
        detailError.value = null;
        selectedFarmer.value = null;

        try {
            const response = await axios.get<Farmer>(`/farmers/${id}`);
            selectedFarmer.value = response.data;
            return selectedFarmer.value;
        } catch (err: any) {
            detailError.value = err.response?.data?.message || 'Failed to load farmer.';
            selectedFarmer.value = null;
            throw err;
        } finally {
            detailLoading.value = false;
        }
    }

    async function createFarmer(payload: Record<string, any>) {
        creating.value = true;
        createError.value = null;

        try {
            const response = await axios.post<Farmer>('/farmers', payload);
            const farmer = response.data;

            selectedFarmer.value = farmer;

            await fetchMetrics();
            await fetchFarmers({ page: filters.page });

            return farmer;
        } catch (err: any) {
            createError.value = err.response?.data?.message || 'Failed to create farmer.';
            throw err;
        } finally {
            creating.value = false;
        }
    }

    async function updateFarmer(id: number | string, payload: Record<string, any>) {
        updating.value = true;
        updateError.value = null;

        try {
            const response = await axios.put<Farmer>(`/farmers/${id}`, payload);
            const farmer = response.data;

            selectedFarmer.value = farmer;

            await fetchMetrics();
            await fetchFarmers({ page: filters.page });

            return farmer;
        } catch (err: any) {
            updateError.value = err.response?.data?.message || 'Failed to update farmer.';
            throw err;
        } finally {
            updating.value = false;
        }
    }

    async function createCow(payload: Record<string, any>) {
        creatingCow.value = true;
        createCowError.value = null;

        try {
            const response = await axios.post('/cows', payload);
            const cow = response.data;

            if (payload.farmer_id) {
                await fetchFarmer(payload.farmer_id);
            }

            return cow;
        } catch (err: any) {
            createCowError.value = err.response?.data?.message || 'Failed to create cow.';
            throw err;
        } finally {
            creatingCow.value = false;
        }
    }

    function setFilter<K extends keyof FarmerFilters>(key: K, value: FarmerFilters[K]) {
        filters[key] = value;
    }

    function resetFilters() {
        Object.assign(filters, defaultFilters());
    }

    return {
        farmers,
        selectedFarmer,
        pagination,
        filters,
        loading,
        metricsLoading,
        creating,
        createError,
        updating,
        updateError,
        creatingCow,
        createCowError,
        detailLoading,
        error,
        detailError,
        metrics,
        fetchFarmers,
        fetchMetrics,
        fetchFarmer,
        createFarmer,
        updateFarmer,
        createCow,
        setFilter,
        resetFilters,
    };
});


