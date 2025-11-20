<template>
    <div class="rounded-lg bg-white p-6 shadow space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Milk Deliveries</h2>
                <p class="text-sm text-gray-500">All recorded deliveries for this center.</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    @click="onRefresh"
                    :disabled="loading"
                >
                    <Icon icon="mdi:refresh" :size="16" />
                    Refresh
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="onCreate"
                    :disabled="loading"
                >
                    <Icon icon="mdi:plus" :size="16" />
                    Record Delivery
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Farmer</th>
                        <th class="px-6 py-3">Volume (L)</th>
                        <th class="px-6 py-3">Grade</th>
                        <th class="px-6 py-3">Price/L</th>
                        <th class="px-6 py-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    <tr v-if="loading" class="hover:bg-gray-50">
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                            Loading deliveries...
                        </td>
                    </tr>
                    <tr v-else-if="deliveries.length === 0" class="hover:bg-gray-50">
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                            No deliveries recorded for this center.
                        </td>
                    </tr>
                    <tr v-for="delivery in deliveries" :key="delivery.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ formatDate(delivery.delivery_date) }}</td>
                        <td class="px-6 py-4">Farmer #{{ delivery.farmer_id }}</td>
                        <td class="px-6 py-4">{{ formatLiters(delivery.volume_liters) }}</td>
                        <td class="px-6 py-4">{{ delivery.quality_grade ?? '—' }}</td>
                        <td class="px-6 py-4">{{ formatCurrency(delivery.price_per_liter) }}</td>
                        <td class="px-6 py-4">{{ formatCurrency(delivery.total_amount) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '../../components/shared/Icon.vue';
import type { PropType } from 'vue';

interface MilkDeliverySummary {
    id: number;
    farmer_id: number;
    delivery_date: string;
    volume_liters: number;
    quality_grade?: string | null;
    total_amount?: number | null;
    price_per_liter?: number | null;
}

const props = defineProps({
    deliveries: {
        type: Array as PropType<MilkDeliverySummary[]>,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    formatDate: {
        type: Function as PropType<(value?: string | null) => string>,
        required: true,
    },
    formatLiters: {
        type: Function as PropType<(value?: number | string | null) => string>,
        required: true,
    },
    formatCurrency: {
        type: Function as PropType<(value?: number | null) => string>,
        required: true,
    },
});

const emit = defineEmits<{ (e: 'refresh'): void; (e: 'create'): void }>();

const onRefresh = () => {
    emit('refresh');
};

const onCreate = () => {
    emit('create');
};
</script>
