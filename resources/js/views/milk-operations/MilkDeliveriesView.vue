<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-3xl bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Milk Operations
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Milk Deliveries</h1>
                    <p class="text-sm text-white/70">
                        Monitor every milk delivery across the network, understand quality and value trends, and keep collection centers aligned.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20 disabled:opacity-60"
                        @click="refresh"
                        :disabled="loading"
                    >
                        <Icon icon="mdi:refresh" :size="18" />
                        Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard
                icon="mdi:bucket-outline"
                icon-class="text-blue-600"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Total Deliveries</template>
                <template #default>{{ loading ? '•••' : deliveries.length }}</template>
                <template #caption>Completed deliveries in the current view</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:counter"
                icon-class="text-emerald-600"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Total Volume</template>
                <template #default>{{ loading ? '•••' : formatLiters(totalVolume) }}</template>
                <template #caption>Aggregate milk delivered</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:currency-usd"
                icon-class="text-amber-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Total Value</template>
                <template #default>{{ loading ? '•••' : formatCurrency(totalAmount) }}</template>
                <template #caption>Sum of payments recorded</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:filter"
                icon-class="text-purple-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Active Filters</template>
                <template #default>{{ activeFiltersLabel }}</template>
                <template #caption>Filters currently applied</template>
            </StatisticalCard>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white/95 p-8 shadow-lg shadow-slate-100 backdrop-blur space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="delivery-search">Search</label>
                    <div class="relative mt-1">
                        <input
                            id="delivery-search"
                            v-model="searchTerm"
                            type="search"
                            placeholder="Search by farmer ID, center name, or recorder"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        />
                        <Icon icon="mdi:magnify" :size="18" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="delivery-center">Center</label>
                    <input
                        id="delivery-center"
                        v-model="centerIdInput"
                        type="number"
                        min="1"
                        placeholder="Filter by center ID"
                        class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="delivery-grade">Quality Grade</label>
                    <select
                        id="delivery-grade"
                        v-model="qualityFilter"
                        class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Any</option>
                        <option value="A">Grade A</option>
                        <option value="B">Grade B</option>
                        <option value="C">Grade C</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
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

        <div class="rounded-3xl border border-slate-100 bg-white shadow-lg shadow-slate-100 backdrop-blur">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Center</th>
                            <th class="px-6 py-3">Farmer</th>
                            <th class="px-6 py-3">Volume (L)</th>
                            <th class="px-6 py-3">Grade</th>
                            <th class="px-6 py-3">Recorder</th>
                            <th class="px-6 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <tr v-if="loading" class="hover:bg-slate-50">
                            <td colspan="7" class="px-6 py-6 text-center text-sm text-slate-500">
                                Loading milk deliveries...
                            </td>
                        </tr>
                        <tr v-else-if="deliveries.length === 0" class="hover:bg-slate-50">
                            <td colspan="7" class="px-6 py-6 text-center text-sm text-slate-500">
                                No deliveries match the selected filters.
                            </td>
                        </tr>
                        <tr v-for="delivery in deliveries" :key="delivery.id" class="transition hover:bg-slate-50/60">
                            <td class="px-6 py-4">{{ formatDate(delivery.delivery_date) }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ delivery.milk_collection_center?.name ?? `Center #${delivery.milk_collection_center_id}` }}
                                </div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">
                                    ID: {{ delivery.milk_collection_center_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">
                                    <router-link :to="`/admin/farmers/${delivery.farmer.id}`" class="hover:underline">{{ delivery.farmer.first_name + ' ' + delivery.farmer.last_name }}</router-link>
                                </div>
                                <div class="text-xs uppercase tracking-wide text-slate-400">
                                    ID: {{ delivery.farmer.farmer_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ formatLiters(delivery.volume_liters) }}</td>
                            <td class="px-6 py-4">{{ delivery.quality_grade ?? '—' }}</td>
                            <td class="px-6 py-4">{{ delivery.recorded_by ?? '—' }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-900">
                                {{ formatCurrency(delivery.total_amount) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import { useMilkDeliveriesStore } from '../../stores/milkDeliveriesStore';

const store = useMilkDeliveriesStore();
const { deliveries, loading, error, filters } = storeToRefs(store);

const searchTerm = ref(filters.value.search);
const centerIdInput = ref(filters.value.center_id ? String(filters.value.center_id) : '');
const qualityFilter = ref(filters.value.quality_grade);

const totalVolume = computed(() =>
    deliveries.value.reduce((sum, delivery) => sum + Number(delivery.volume_liters ?? 0), 0)
);
const totalAmount = computed(() =>
    deliveries.value.reduce((sum, delivery) => sum + Number(delivery.total_amount ?? 0), 0)
);

const activeFiltersLabel = computed(() => {
    const active: string[] = [];
    if (searchTerm.value.trim()) active.push('Search');
    if (centerIdInput.value.trim()) active.push(`Center #${centerIdInput.value.trim()}`);
    if (qualityFilter.value) active.push(`Grade ${qualityFilter.value}`);
    return active.length ? active.join(', ') : 'None';
});

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    try {
        return new Date(value).toLocaleDateString();
    } catch {
        return value;
    }
};

const formatLiters = (value?: number | string | null) => {
    if (value === null || value === undefined || value === '') return '—';
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '—';
    return `${numeric.toLocaleString(undefined, {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    })} L`;
};

const formatCurrency = (value?: number | null) => {
    if (value === null || value === undefined) return '—';
    return value.toLocaleString(undefined, { style: 'currency', currency: 'UGX' });
};

const refresh = async () => {
    await store.fetchDeliveries();
};

const clearFilters = () => {
    searchTerm.value = '';
    centerIdInput.value = '';
    qualityFilter.value = '';
    store.resetFilters();
};

let searchDebounce: ReturnType<typeof setTimeout> | null = null;
watch(searchTerm, (value) => {
    if (searchDebounce) clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
        store.fetchDeliveries({ search: value.trim() });
    }, 250);
});

watch(centerIdInput, (value) => {
    const trimmed = value.trim();
    const parsed = trimmed ? Number(trimmed) : null;
    store.fetchDeliveries({ center_id: parsed && !Number.isNaN(parsed) ? parsed : null });
});

watch(qualityFilter, (value) => {
    store.fetchDeliveries({ quality_grade: value });
});

onMounted(async () => {
    await store.fetchDeliveries();
});
</script>

