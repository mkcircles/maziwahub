<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-3xl bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Farmer Network
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Farmers</h1>
                    <p class="text-sm text-white/70">
                        Monitor registrations, validation status, and milk center assignments for every farmer captured on
                        the platform.
                    </p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button
                        class="inline-flex items-center gap-2 rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                        @click="openAddModal"
                    >
                        <Icon icon="mdi:account-plus-outline" :size="18" />
                        Register Farmer
                    </button>
                    <button
                        class="inline-flex items-center gap-2 rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                        @click="refresh"
                    >
                        <Icon icon="mdi:refresh" :size="18" />
                        Refresh data
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <StatisticalCard
                icon="mdi:account-multiple-outline"
                icon-class="text-sky-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Total Farmers</template>
                <template #default>{{ metricsLoading ? '•••' : metrics.total }}</template>
                <template #caption>All registered farmers across the platform</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:check-circle-outline"
                icon-class="text-emerald-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Active Farmers</template>
                <template #default>{{ metricsLoading ? '•••' : metrics.active }}</template>
                <template #caption>Farmers currently validated and active</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:clock-outline"
                icon-class="text-amber-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Pending Farmers</template>
                <template #default>{{ metricsLoading ? '•••' : metrics.pending }}</template>
                <template #caption>Awaiting validation or review</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:shield-check-outline"
                icon-class="text-indigo-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Insured Farmers</template>
                <template #default>{{ metricsLoading ? '•••' : metrics.insured }}</template>
                <template #caption>Farmers covered by insurance</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:milk-outline"
                icon-class="text-orange-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Milk Centers</template>
                <template #default>{{ milkCentersLoading ? '•••' : milkCentersCount }}</template>
                <template #caption>
                    <span v-if="!milkCentersError">Centers available to farmers</span>
                    <span v-else class="text-red-600">{{ milkCentersError }}</span>
                </template>
            </StatisticalCard>
        </div>
            

            

            

        <div class="space-y-6 rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-lg shadow-slate-100">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="grid gap-4 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700" for="farmer-search">Search</label>
                        <div class="mt-1 relative">
                            <input
                                id="farmer-search"
                                v-model="searchTerm"
                                type="search"
                                placeholder="Search by name, farmer ID or phone"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2 text-sm text-slate-700 shadow-inner focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                            />
                            <Icon icon="mdi:magnify" :size="18" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700" for="farmer-status">Status</label>
                        <select
                            id="farmer-status"
                            v-model="statusFilter"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                        >
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700" for="farmer-insured">Insured</label>
                        <select
                            id="farmer-insured"
                            v-model="insuredFilter"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                        >
                            <option value="">Any</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700" for="farmer-reg-type">Registration Type</label>
                        <select
                            id="farmer-reg-type"
                            v-model="regTypeFilter"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                        >
                            <option value="">Any type</option>
                            <option value="individual">Individual</option>
                            <option value="cooperative">Cooperative</option>
                            <option value="association">Association</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700" for="farmer-per-page">Rows per page</label>
                        <select
                            id="farmer-per-page"
                            v-model.number="perPage"
                            class="mt-1 rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                        >
                            <option v-for="option in perPageOptions" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>
                    </div>

                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                        @click="resetFilters"
                    >
                        <Icon icon="mdi:filter-remove-outline" :size="18" />
                        Clear filters
                    </button>
                </div>
            </div>

            <div v-if="farmersErrorMessage" class="rounded-md border border-red-200 bg-red-50 p-4 text-red-700">
                {{ farmersErrorMessage }}
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-lg shadow-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Farmer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Contact</th>
                            <!-- <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Milk Collection Center</th> -->
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Herd Size</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                        <tr v-if="loading" class="bg-white">
                            <td colspan="5" class="px-6 py-6 text-center text-sm text-slate-500">
                                Loading farmers...
                            </td>
                        </tr>
                        <tr v-else-if="!loading && farmers.length === 0" class="bg-white">
                            <td colspan="5" class="px-6 py-6 text-center text-sm text-slate-500">
                                No farmers match the selected filters.
                            </td>
                        </tr>
                        <tr v-for="farmer in farmers" :key="farmer.id" class="transition hover:bg-slate-50/60">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">
                                    <router-link :to="`/admin/farmers/${farmer.id}`" class="hover:text-blue-600 hover:underline">
                                        {{ farmer.first_name }} {{ farmer.last_name }}
                                    </router-link>
                                </div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide">
                                    ID: {{ farmer.farmer_id }}
                                </div>
                                
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div><Icon icon="mdi:phone" :size="10" class="inline-block text-emerald-500" /> <span class="text-[12px] text-slate-400">  {{ farmer.phone_number ?? '—' }}</span></div>
                                
                            </td>
                            <!-- <td class="px-6 py-4 text-sm text-slate-600">
                                <div>{{ farmer.milkCollectionCenter?.name ?? 'Not assigned' }}</div>
                                <div v-if="farmer.milkCollectionCenter?.physical_address" class="text-xs text-slate-400">
                                    {{ farmer.milkCollectionCenter.physical_address }}
                                </div>
                            </td> -->
                            <td class="px-6 py-4 text-[12px] text-slate-500">
                                <div>{{ formatFarmerLocation(farmer) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div class="grid gap-1 text-[12px] text-slate-400">
                                    {{ farmer.herd_size ?? '—' }}
                                   
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                    :class="statusChipClass(farmer.status)"
                                >
                                    <Icon :icon="statusChipIcon(farmer.status)" :size="14" />
                                    {{ farmer.status ?? 'Unknown' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="farmers.length > 0"
                class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 text-sm text-slate-500 md:flex-row md:items-center md:justify-between"
            >
                <p>
                    Showing
                    <span class="font-semibold text-slate-700">
                        {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }}
                    </span>
                    to
                    <span class="font-semibold text-slate-700">
                        {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
                    </span>
                    of
                    <span class="font-semibold text-slate-700">{{ pagination.total }}</span>
                    farmers
                </p>

                <div class="flex items-center gap-2">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="pagination.current_page === 1 || loading"
                        @click="changePage(pagination.current_page - 1)"
                    >
                        <Icon icon="mdi:chevron-left" :size="18" />
                        Previous
                    </button>
                    <span class="text-sm text-slate-500">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </span>
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="pagination.current_page >= pagination.last_page || loading"
                        @click="changePage(pagination.current_page + 1)"
                    >
                        Next
                        <Icon icon="mdi:chevron-right" :size="18" />
                    </button>
                </div>
            </div>
        </div>
    </div>
    <AddFarmerModal
        :is-open="showAddModal"
        :milk-centers="milkCenters"
        @close="closeAddModal"
        @created="handleFarmerCreated"
    />
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import AddFarmerModal from '../../components/farmers/AddFarmerModal.vue';
import { useFarmerStore } from '../../stores/farmerStore';
import { useGeographyStore } from '../../stores/geographyStore';
import type { Farmer } from '../../stores/geographyStore';

const farmerStore = useFarmerStore();
const geographyStore = useGeographyStore();

const searchTerm = ref(farmerStore.filters.search);
const statusFilter = ref(farmerStore.filters.status);
const insuredFilter = ref(farmerStore.filters.is_farmer_insured);
const regTypeFilter = ref(farmerStore.filters.reg_type);
const perPage = ref<number>(farmerStore.filters.per_page);

const milkCentersLoading = ref(false);
const milkCentersError = ref<string | null>(null);

const showAddModal = ref(false);

const farmers = computed(() => farmerStore.farmers);
const pagination = computed(() => farmerStore.pagination);
const loading = computed(() => farmerStore.loading);
const farmersErrorMessage = computed(() => farmerStore.error);

const metrics = computed(() => farmerStore.metrics);
const metricsLoading = computed(() => farmerStore.metricsLoading);

const milkCenters = computed(() => geographyStore.milkCenters);
const milkCentersCount = computed(() => milkCenters.value.length);

const perPageOptions = [10, 15, 25, 50];

let searchDebounce: ReturnType<typeof setTimeout> | null = null;

const applyFilters = async (page = 1) => {
    await farmerStore.fetchFarmers({ page });
};

const changePage = (page: number) => {
    if (page < 1 || page > pagination.value.last_page || loading.value) {
        return;
    }
    farmerStore.setFilter('page', page);
    applyFilters(page);
};

const loadMilkCenters = async () => {
    milkCentersLoading.value = true;
    milkCentersError.value = null;
    try {
        await geographyStore.getMilkCollectionCenters();
    } catch (error) {
        milkCentersError.value = geographyStore.error ?? 'Failed to load milk collection centers.';
    } finally {
        milkCentersLoading.value = false;
    }
};

const refresh = async () => {
    await Promise.all([
        farmerStore.fetchMetrics(),
        applyFilters(pagination.value.current_page),
        loadMilkCenters(),
    ]);
};

const openAddModal = () => {
    showAddModal.value = true;
};

const closeAddModal = () => {
    showAddModal.value = false;
};

const handleFarmerCreated = async () => {
    showAddModal.value = false;
    await refresh();
};

const resetFilters = async () => {
    farmerStore.resetFilters();
    searchTerm.value = '';
    statusFilter.value = '';
    insuredFilter.value = '';
    regTypeFilter.value = '';
    perPage.value = farmerStore.filters.per_page;
    await applyFilters(1);
    await farmerStore.fetchMetrics();
};

const formatFarmerLocation = (farmer: Farmer) => {
    const location = (farmer as any)?.location ?? {};
    const segments = [
        location.country,
        location.region,
        location.district ?? farmer.district,
        // location.county ?? farmer.county,
        // location.sub_county ?? farmer.sub_county,
        // location.parish ?? farmer.parish,
        // location.village ?? farmer.village,
    ].filter((segment): segment is string => Boolean(segment));
    return segments.length ? segments.join(' > ') : 'Location details unavailable';
};

const statusChipClass = (status?: string | null) => {
    if (status === 'active') {
        return 'bg-green-100 text-green-700';
    }
    if (status === 'pending') {
        return 'bg-yellow-100 text-yellow-700';
    }
    if (status === 'inactive') {
        return 'bg-gray-100 text-gray-600';
    }
    return 'bg-gray-100 text-gray-600';
};

const statusChipIcon = (status?: string | null) => {
    if (status === 'active') {
        return 'mdi:check-circle-outline';
    }
    if (status === 'pending') {
        return 'mdi:clock-outline';
    }
    if (status === 'inactive') {
        return 'mdi:alert-circle-outline';
    }
    return 'mdi:help-circle-outline';
};

watch(searchTerm, (value) => {
    farmerStore.setFilter('search', value);
    if (searchDebounce) {
        clearTimeout(searchDebounce);
    }
    searchDebounce = setTimeout(() => {
        applyFilters(1);
    }, 300);
});

watch(statusFilter, (value) => {
    farmerStore.setFilter('status', value);
    applyFilters(1);
});

watch(insuredFilter, (value) => {
    farmerStore.setFilter('is_farmer_insured', value);
    applyFilters(1);
});

watch(regTypeFilter, (value) => {
    farmerStore.setFilter('reg_type', value);
    applyFilters(1);
});

watch(perPage, (value) => {
    farmerStore.setFilter('per_page', value);
    applyFilters(1);
});

onMounted(async () => {
    await farmerStore.fetchMetrics();
    await Promise.all([applyFilters(1), loadMilkCenters()]);
});
</script>
