<template>

    <div v-if="!cows.length" class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500">
        No cows registered for this farmer yet.
    </div>
    <div v-else class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3">Tag Number</th>
                    <th class="px-4 py-3">Breed</th>
                    <th class="px-4 py-3">DOB</th>
                    <th class="px-4 py-3">Milk Capacity (L)</th>
                    <th class="px-4 py-3">Health Status</th>
                    <th class="px-4 py-3">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                <tr v-for="cow in cows" :key="cow.id" class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-900">
                        <router-link :to="`/admin/cows/${cow.id}`" class="hover:underline">
                                    {{ cow.tag_number }}
                                </router-link>
                    </td>
                    <td class="px-4 py-3">
                        {{ cow.breed ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ formatDate(cow.date_of_birth) }}
                    </td>
                    <td class="px-4 py-3">
                        {{ formatLiters(cow.milk_capacity_liters) }}
                    </td>
                    <td class="px-4 py-3">
                        {{ cow.health_status ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ cow.notes ?? '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { Cow } from '../../stores/geographyStore';

const props = defineProps<{
    cows: Cow[];
    formatDate: (value?: string | null) => string;
    formatLiters: (value?: number | string | null) => string;
}>();
</script>

