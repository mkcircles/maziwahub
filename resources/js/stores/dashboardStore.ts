import { defineStore } from 'pinia';
import axios from 'axios';
import { ref } from 'vue';

export const useDashboardStore = defineStore('dashboard', () => {
    const adminSummary = ref<{
        active_users: number | string;
        geography_layers: number | string;
        milk_centers: number | string;
        cows_monitored: number | string;
    } | null>(null);

    const loading = ref(false);
    const error = ref<string | null>(null);

    const fetchAdminSummary = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get('/dashboard/admin-summary');
            adminSummary.value = response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch dashboard summary';
            console.error('Error fetching admin summary:', err);
        } finally {
            loading.value = false;
        }
    };

    return {
        adminSummary,
        loading,
        error,
        fetchAdminSummary,
    };
});
