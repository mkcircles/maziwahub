<template>
    <div v-if="!records.length" class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500">
        No milk production records found for this farmer.
    </div>
    <div v-else class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3">Recorded Date</th>
                    <th class="px-4 py-3">Cow</th>
                    <th class="px-4 py-3 text-right">Morning (L)</th>
                    <th class="px-4 py-3 text-right">Midday (L)</th>
                    <th class="px-4 py-3 text-right">Evening (L)</th>
                    <th class="px-4 py-3 text-right">Total (L)</th>
                    <th class="px-4 py-3">Recorded By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ formatDate(record.recorded_date) }}
                    </td>
                    <td class="px-4 py-3">
                        {{ getCowTag(record.cow_id) }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        {{ formatLiters(record.morning_volume_liters) }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        {{ formatLiters(record.midday_volume_liters) }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        {{ formatLiters(record.evening_volume_liters) }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900 tabular-nums">
                        {{ formatLiters(record.total_volume_liters) }}
                    </td>
                    <td class="px-4 py-3">
                        {{ record.recorded_by ?? '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { CowMilkProduction } from '../../stores/geographyStore';

defineProps<{
    records: CowMilkProduction[];
    formatDate: (value?: string | null) => string;
    formatLiters: (value?: number | string | null) => string;
    getCowTag: (cowId?: number | null) => string;
}>();
</script>

