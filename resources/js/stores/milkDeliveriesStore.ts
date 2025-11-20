import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

interface Delivery {
    id: number;
    delivery_date: string;
    farmer_id: number;
    milk_collection_center_id: number;
    volume_liters: number;
    quality_grade?: string | null;
    price_per_liter?: number | null;
    total_amount?: number | null;
    recorded_by?: string | null;
    notes?: string | null;
    milk_collection_center?: { id: number; name: string } | null;
}

interface DeliveryFilters {
    search: string;
    center_id: number | null;
    quality_grade: string;
}

interface DailySummary {
    date: string;
    total_volume: number;
}

export const useMilkDeliveriesStore = defineStore('milkDeliveries', () => {
    const deliveries = ref<Delivery[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const dailySummary = ref<DailySummary[]>([]);
    const dailySummaryLoading = ref(false);
    const dailySummaryError = ref<string | null>(null);

    const filters = ref<DeliveryFilters>({
        search: '',
        center_id: null,
        quality_grade: '',
    });

    const fetchDeliveries = async (override: Partial<DeliveryFilters> = {}) => {
        loading.value = true;
        error.value = null;

        const params = {
            ...filters.value,
            ...override,
        };

        filters.value = { ...params };

        try {
            const response = await axios.get<Delivery[]>('milk-deliveries', {
                params: {
                    search: params.search || undefined,
                    milk_collection_center_id: params.center_id || undefined,
                    quality_grade: params.quality_grade || undefined,
                },
            });
            deliveries.value = response.data ?? [];
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch milk deliveries.';
            deliveries.value = [];
        } finally {
            loading.value = false;
        }
    };

    const resetFilters = async () => {
        filters.value = {
            search: '',
            center_id: null,
            quality_grade: '',
        };
        await fetchDeliveries();
    };

    const fetchDailySummary = async (params: { days?: number; from?: string; to?: string; center_id?: number | null } = {}) => {
        dailySummaryLoading.value = true;
        dailySummaryError.value = null;

        try {
            const response = await axios.get<DailySummary[]>('milk-deliveries/summary/daily', {
                params: {
                    days: params.days ?? undefined,
                    from: params.from ?? undefined,
                    to: params.to ?? undefined,
                    milk_collection_center_id: params.center_id ?? undefined,
                },
            });

            dailySummary.value = Array.isArray(response.data) ? response.data : [];
        } catch (err: any) {
            dailySummaryError.value = err.response?.data?.message || 'Failed to load milk delivery summary.';
            dailySummary.value = [];
        } finally {
            dailySummaryLoading.value = false;
        }
    };

    return {
        deliveries,
        loading,
        error,
        filters,
        fetchDeliveries,
        resetFilters,
        dailySummary,
        dailySummaryLoading,
        dailySummaryError,
        fetchDailySummary,
    };
});
