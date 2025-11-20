import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import axios from 'axios';
import type { Cow } from './geographyStore';

interface CowFilters {
    search: string;
    farmer_id: number | null;
}

export const useCowStore = defineStore('cows', () => {
    const cows = ref<Cow[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const filters = reactive<CowFilters>({
        search: '',
        farmer_id: null,
    });

    const applyFiltersToParams = () => {
        const params: Record<string, any> = {};
        if (filters.search) {
            params.search = filters.search;
        }
        if (filters.farmer_id) {
            params.farmer_id = filters.farmer_id;
        }
        return params;
    };

    const normalizeResponse = (payload: any): Cow[] => {
        if (Array.isArray(payload)) {
            return payload;
        }

        if (Array.isArray(payload?.data)) {
            return payload.data;
        }

        return [];
    };

    async function fetchCows(override: Partial<CowFilters> = {}) {
        loading.value = true;
        error.value = null;

        if (override.search !== undefined) {
            filters.search = override.search;
        }

        if (override.farmer_id !== undefined) {
            filters.farmer_id = override.farmer_id;
        }

        try {
            const response = await axios.get('/cows', {
                params: applyFiltersToParams(),
            });

            cows.value = normalizeResponse(response.data);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to load cows.';
            cows.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchCow(id: number | string) {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(`/cows/${id}`);
            return response.data as Cow;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to load cow.';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchCowMilkRecords(cowId: number | string) {
        try {
            const response = await axios.get(`/cow-milk-productions`, {
                params: { cow_id: cowId },
            });
            const payload = response.data ?? [];
            if (Array.isArray(payload)) {
                return payload;
            }
            if (Array.isArray(payload?.data)) {
                return payload.data;
            }
            return [];
        } catch (err: any) {
            throw err;
        }
    }

    async function fetchCowTreatments(cowId: number | string) {
        try {
            const response = await axios.get(`/cow-treatments`, {
                params: { cow_id: cowId, timeline: true },
            });
            const payload = response.data ?? [];
            if (Array.isArray(payload)) {
                return payload;
            }
            if (Array.isArray(payload?.data)) {
                return payload.data;
            }
            return [];
        } catch (err: any) {
            throw err;
        }
    }

    async function resetFilters() {
        filters.search = '';
        filters.farmer_id = null;
        await fetchCows();
    }

    function setFilter<K extends keyof CowFilters>(key: K, value: CowFilters[K]) {
        filters[key] = value;
    }

    return {
        cows,
        loading,
        error,
        filters,
        fetchCows,
        fetchCow,
        fetchCowMilkRecords,
        fetchCowTreatments,
        resetFilters,
        setFilter,
    };
});


