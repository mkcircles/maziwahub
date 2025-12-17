<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Milk Collection Centers</h1>
                <p class="text-sm text-gray-500">
                    Monitor registered centers, infrastructure, and geographical footprint.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                    @click="openCreateModal"
                >
                    <Icon icon="mdi:plus" :size="18" />
                    Add Center
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    @click="refresh"
                    :disabled="loading"
                >
                    <Icon icon="mdi:refresh" :size="18" />
                    Refresh
                </button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard icon="mdi:warehouse" icon-class="text-emerald-600">
                <template #title>Total Centers</template>
                <template #default>{{ loading ? '•••' : centers.length }}</template>
                <template #caption>Registered milk collection facilities</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:grid" icon-class="text-blue-600">
                <template #title>Average Cooler Capacity</template>
                <template #default>{{ loading ? '•••' : averageCapacity }}</template>
                <template #caption>Daily cooling potential in liters</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:flash" icon-class="text-amber-500">
                <template #title>With Power Backup</template>
                <template #default>{{ loading ? '•••' : powerBackupCount }}</template>
                <template #caption>Centers citing reliable power source</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:test-tube" icon-class="text-purple-500">
                <template #title>Testing Equipment</template>
                <template #default>{{ loading ? '•••' : testingEquipmentCount }}</template>
                <template #caption>Facilities equipped for quality checks</template>
            </StatisticalCard>
        </div>

        <div class="rounded-lg bg-white p-6 shadow space-y-4">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700" for="mcc-search">Search</label>
                    <div class="relative mt-1">
                        <input
                            id="mcc-search"
                            v-model="searchTerm"
                            type="search"
                            placeholder="Search by name or registration number"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        />
                        <Icon icon="mdi:magnify" :size="18" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700" for="mcc-power">Power Source</label>
                    <select
                        id="mcc-power"
                        v-model="powerFilter"
                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Any</option>
                        <option value="grid">Grid</option>
                        <option value="solar">Solar</option>
                        <option value="generator">Generator</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700" for="mcc-testing">Testing Equipment</label>
                    <select
                        id="mcc-testing"
                        v-model="testingFilter"
                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Any</option>
                        <option value="true">Available</option>
                        <option value="false">Not available</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    @click="clearFilters"
                    :disabled="loading"
                >
                    <Icon icon="mdi:filter-remove-outline" :size="18" />
                    Clear filters
                </button>
                <p v-if="error" class="text-sm text-red-600">
                    {{ error }}
                </p>
            </div>
        </div>

        <div class="rounded-lg bg-white shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <th class="px-6 py-3">Center</th>
                            <th class="px-6 py-3">Location</th>
                            <th class="px-6 py-3">Capacity (L)</th>
                            <th class="px-6 py-3">Power Source</th>
                            <th class="px-6 py-3">Features</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                        <tr v-if="loading" class="hover:bg-gray-50">
                            <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                Loading milk collection centers...
                            </td>
                        </tr>
                        <tr v-else-if="filteredCenters.length === 0" class="hover:bg-gray-50">
                            <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                No milk collection centers match the selected filters.
                            </td>
                        </tr>
                        <tr v-for="center in filteredCenters" :key="center.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ center.name }}</div>
                                <div class="text-xs uppercase tracking-wide text-gray-400">
                                    {{ center.registration_number ?? 'Unregistered' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ formatLocation(center) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formatNumber(center.cooler_capacity_liters) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ center.power_source ?? 'Not specified' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-if="center.has_testing_equipment"
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 text-[11px] font-medium text-emerald-700"
                                    >
                                        <Icon icon="mdi:test-tube" :size="12" /> Testing
                                    </span>
                                    <span
                                        v-if="center.has_washing_bay"
                                        class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-1 text-[11px] font-medium text-blue-700"
                                    >
                                        <Icon icon="mdi:water" :size="12" /> Washing
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <router-link
                                    :to="`/admin/milk-collection-centers/${center.id}`"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700"
                                >
                                    View
                                    <Icon icon="mdi:chevron-right" :size="16" />
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <MilkCollectionCenterFormModal
        :is-open="showCreateModal"
        @close="closeCreateModal"
        @created="handleCenterCreated"
    />
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import MilkCollectionCenterFormModal from '../../components/milk-centers/MilkCollectionCenterFormModal.vue';
import axios from 'axios';
import type { MilkCollectionCenter } from '../../stores/geographyStore';

const centers = ref<MilkCollectionCenter[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

const searchTerm = ref('');
const powerFilter = ref('');
const testingFilter = ref('');
const showCreateModal = ref(false);

const fetchCenters = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get<MilkCollectionCenter[]>('/milk-collection-centers');
        centers.value = response.data ?? [];
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load milk collection centers.';
        centers.value = [];
    } finally {
        loading.value = false;
    }
};

const refresh = async () => {
    await fetchCenters();
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const handleCenterCreated = async () => {
    showCreateModal.value = false;
    await fetchCenters();
};

const clearFilters = () => {
    searchTerm.value = '';
    powerFilter.value = '';
    testingFilter.value = '';
};

const filteredCenters = computed(() => {
    const search = searchTerm.value.trim().toLocaleLowerCase();
    const power = powerFilter.value;
    const testing = testingFilter.value;

    return centers.value.filter((center) => {
        if (
            search &&
            !(
                center.name.toLocaleLowerCase().includes(search) ||
                (center.registration_number ?? '').toLocaleLowerCase().includes(search)
            )
        ) {
            return false;
        }

        if (power) {
            if (!center.power_source || center.power_source.toLocaleLowerCase() !== power) {
                return false;
            }
        }

        if (testing) {
            const boolValue = testing === 'true';
            if (center.has_testing_equipment !== boolValue) {
                return false;
            }
        }

        return true;
    });
});

const averageCapacity = computed(() => {
    if (!filteredCenters.value.length) return '0';
    const total = filteredCenters.value.reduce(
        (sum, center) => sum + Number(center.cooler_capacity_liters ?? 0),
        0
    );
    return formatNumber(Math.round(total / filteredCenters.value.length));
});

const powerBackupCount = computed(() =>
    filteredCenters.value.filter((center) =>
        ['generator', 'solar'].includes((center.power_source ?? '').toLocaleLowerCase())
    ).length
);

const testingEquipmentCount = computed(() =>
    filteredCenters.value.filter((center) => center.has_testing_equipment).length
);

const formatNumber = (value?: number | null) => {
    if (value === null || value === undefined) return '—';
    return Number(value).toLocaleString();
};

const formatLocation = (center: MilkCollectionCenter) => {
    const location = (center.location ?? {}) as Record<string, any>;
    const parts = [
        location.country,
        location.region,
        location.district,
        location.county,
        location.subcounty,
        location.parish,
        location.village,
    ].filter((value): value is string => Boolean(value));
    return parts.length ? parts.join(' • ') : center.physical_address;
};

watch([searchTerm, powerFilter, testingFilter], () => {
    // triggers computed filtering
});

onMounted(fetchCenters);
</script>

