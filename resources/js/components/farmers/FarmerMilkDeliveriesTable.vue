<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Milk Deliveries</h2>
                <p class="text-sm text-gray-500">Deliveries recorded for this farmer.</p>
            </div>
            <slot name="actions" />
        </div>
        
        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Center</th>
                        <th class="px-6 py-3">Volume (L)</th>
                        <th class="px-6 py-3">Grade</th>
                        <th class="px-6 py-3">Recorded By</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    <tr v-if="deliveries.length === 0" class="hover:bg-gray-50">
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                            No milk deliveries available for this farmer.
                        </td>
                    </tr>
                    <tr v-for="delivery in deliveries" :key="delivery.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ formatDate(delivery.delivery_date) }}</td>
                        <td class="px-6 py-4">
                           <router-link :to="`/admin/milk-collection-centers/${delivery.milk_collection_center?.id}`">
                            {{ delivery.milk_collection_center?.name }}
                           </router-link>
                        </td>
                        <td class="px-6 py-4">{{ formatLiters(delivery.volume_liters) }}</td>
                        <td class="px-6 py-4">{{ delivery.quality_grade ?? '—' }}</td>
                        <td class="px-6 py-4">{{ delivery.recorded_by ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            {{ formatCurrency(delivery.total_amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PropType } from 'vue';
import type { MilkDelivery } from '../../stores/geographyStore';

const props = defineProps({
    deliveries: {
        type: Array as PropType<MilkDelivery[]>,
        default: () => [],
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
</script>
