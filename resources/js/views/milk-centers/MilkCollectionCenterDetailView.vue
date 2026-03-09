<template>
    <div class="space-y-8 pb-16">
        <div v-if="loading" class="rounded-md border border-surface-200 bg-white p-5 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <router-link to="/admin/milk-collection-centers"
                    class="inline-flex items-center gap-2 self-start rounded-full bg-surface-100 px-4 py-2 text-sm font-medium text-surface-700 transition hover:bg-surface-200">
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to centers
                </router-link>
                <div class="flex items-center gap-3 text-surface-500">
                    <Icon icon="mdi:loading" :size="22" class="animate-spin" />
                    <span class="text-sm font-medium">Loading details…</span>
                </div>
            </div>
        </div>
        <div v-else-if="error"
            class="rounded-md border border-red-200/60 bg-red-50/80 p-8 text-red-700 shadow-sm shadow-red-100">
            <div class="flex flex-col gap-4">
                <router-link to="/admin/milk-collection-centers"
                    class="inline-flex items-center gap-2 self-start rounded-full bg-red-100/80 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-200/80">
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to centers
                </router-link>
                <p class="text-sm font-medium">{{ error }}</p>
            </div>
        </div>
        <div v-else-if="!center"
            class="rounded-md border border-surface-200 bg-white p-10 text-center text-surface-600 shadow-sm shadow-slate-100">
            <div class="mx-auto flex max-w-xl flex-col items-center gap-4">
                <router-link to="/admin/milk-collection-centers"
                    class="inline-flex items-center gap-2 rounded-full bg-surface-100 px-4 py-2 text-sm font-medium text-surface-700 transition hover:bg-surface-200">
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to centers
                </router-link>
                <p class="text-base font-medium">Milk collection center not found.</p>
            </div>
        </div>

        <template v-else>
            <!-- Rich Header Banner -->
            <div class="relative overflow-hidden rounded-md bg-[#0F172A] p-5 text-white shadow-xl shadow-blue-900/40">
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80">
                </div>
                <div class="relative flex flex-col gap-8">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <router-link to="/admin/milk-collection-centers"
                            class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25">
                            <Icon icon="mdi:arrow-left" :size="18" />
                            Back to centers
                        </router-link>
                        <div class="flex items-center gap-2">
                            <button
                                class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1 text-sm font-medium text-white backdrop-blur transition hover:bg-white/30 disabled:cursor-not-allowed disabled:opacity-60"
                                @click="openEditModal" :disabled="loading || !center">
                                <Icon icon="mdi:pencil" :size="18" />
                                Edit Center
                            </button>
                            <button
                                class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1 text-sm font-medium text-white backdrop-blur transition hover:bg-white/30 disabled:cursor-not-allowed disabled:opacity-60"
                                @click="refresh" :disabled="loading">
                                <Icon icon="mdi:refresh" :size="18" />
                                Refresh data
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-white/60">
                                Milk Collection Center
                            </p>
                            <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">
                                {{ center?.name ?? 'Collection Center' }}
                            </h1>
                            <p class="mt-2 text-xs text-white/80 md:text-sm">
                                Reg # {{ center?.registration_number ?? 'N/A' }} • Registered {{
                                    formatDate(center?.established_date) }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2 text-[11px] md:text-xs">
                                <span v-if="center.has_testing_equipment"
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white/90">
                                    <Icon icon="mdi:test-tube" :size="16" class="text-emerald-400" />
                                    Testing Equipment
                                </span>
                                <span v-if="center.has_washing_bay"
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white/90">
                                    <Icon icon="mdi:water" :size="16" class="text-sky-400" />
                                    Washing Bay
                                </span>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white/90">
                                    <Icon icon="mdi:flash" :size="16" class="text-amber-400" />
                                    {{ center.power_source ?? 'Unknown Power' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 text-sm text-white/85">
                            <div class="inline-flex items-center gap-2">
                                <Icon icon="mdi:map-marker-radius" :size="18" class="text-white/80" />
                                <span>{{ center.physical_address }}</span>
                            </div>
                            <div class="inline-flex items-center gap-2">
                                <Icon icon="mdi:account-tie" :size="18" class="text-white/80" />
                                <span>Manager: {{ center.manager_name ?? 'Not assigned' }}</span>
                            </div>
                            <div class="inline-flex items-center gap-2">
                                <Icon icon="mdi:phone-outline" :size="18" class="text-white/80" />
                                <span>{{ center.manager_phone ?? 'No phone' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatisticalCard icon="mdi:warehouse" icon-class="text-emerald-500"
                    class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                    <template #title>Cooling Capacity</template>
                    <template #default>{{ formatNumber(center.cooler_capacity_liters) }} L</template>
                    <template #caption>Maximum storage per day</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:account-group" icon-class="text-primary-500"
                    class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                    <template #title>Farmers</template>
                    <template #default>{{ farmersCount ?? '—' }}</template>
                    <template #caption>Registered to this center</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:bucket-outline" icon-class="text-amber-500"
                    class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                    <template #title>Daily Volume</template>
                    <template #default>{{ averageDailyVolume }}</template>
                    <template #caption>30-day trailing average</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:account-hard-hat" icon-class="text-purple-500"
                    class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                    <template #title>Staff Count</template>
                    <template #default>{{ center.staff_count ?? '0' }}</template>
                    <template #caption>Team members on site</template>
                </StatisticalCard>
            </div>

            <div class="rounded-lg border border-surface-200 bg-white p-2 shadow-sm shadow-slate-100">
                <nav class="flex flex-wrap gap-2" aria-label="Center detail tabs">
                    <button v-for="tab in tabs" :key="tab.id" type="button"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 focus-visible:ring-offset-2"
                        :class="[
                            activeTab === tab.id
                                ? 'bg-primary-600 text-white shadow-lg shadow-slate-400/40'
                                : 'text-surface-600 hover:text-surface-900 hover:bg-surface-100',
                        ]" @click="selectTab(tab.id)">
                        <Icon :icon="tab.icon || 'mdi:information-outline'" :size="16" />
                        <span>{{ tab.label }}</span>
                    </button>
                </nav>
            </div>

            <section v-if="activeTab === 'overview'" class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-2">
                    <section
                        class="space-y-4 rounded-lg border border-surface-100 bg-white p-6 shadow-sm border border-surface-200 lg:col-span-2">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
                            <div>
                                <h2 class="text-lg font-semibold text-surface-900">30-Day Delivery Trend</h2>
                                <p class="text-sm text-surface-500">Total volume collected daily over the last 30 days.
                                </p>
                            </div>
                        </div>
                        <div v-if="trendLoading"
                            class="rounded-md border border-surface-200 bg-surface-50 p-6 text-center text-sm text-surface-600">
                            Loading trend data...
                        </div>
                        <DailyDeliveriesLineChart v-else-if="deliveryTrend.length > 0" :summary="deliveryTrend" />
                        <div v-else
                            class="rounded-md border border-surface-200 bg-surface-50 p-6 text-center text-sm text-surface-600">
                            No recent deliveries to display.
                        </div>
                    </section>

                    <section
                        class="space-y-4 rounded-lg border border-surface-100 bg-white p-6 shadow-sm border border-surface-200">
                        <h2 class="text-lg font-semibold text-surface-900">Facility Details</h2>
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-surface-500">Name</dt>
                                <dd class="text-sm font-medium text-surface-900">{{ center.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-surface-500">Registration #</dt>
                                <dd class="text-sm text-surface-700">{{ center.registration_number ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-surface-500">Established</dt>
                                <dd class="text-sm text-surface-700">{{ formatDate(center.established_date) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-surface-500">Location Area</dt>
                                <dd class="text-sm text-surface-700">{{ formatLocation(center) }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section
                        class="space-y-4 rounded-lg border border-surface-100 bg-white p-6 shadow-sm border border-surface-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-surface-900">Recent Deliveries</h2>
                            <div class="flex items-center gap-2">
                                <button
                                    class="inline-flex items-center gap-2 rounded-lg border border-surface-200 bg-surface-50 px-3 py-1.5 text-xs font-medium text-surface-700 hover:bg-surface-100 hover:text-surface-900 transition"
                                    @click="fetchDeliveries" :disabled="deliveriesLoading">
                                    <Icon icon="mdi:refresh" :size="16" />
                                    Refresh
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold tracking-wide text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    @click="openCreateDeliveryModal" :disabled="deliveriesLoading">
                                    <Icon icon="mdi:plus" :size="16" />
                                    Record Delivery
                                </button>
                            </div>
                        </div>
                        <div v-if="deliveriesLoading"
                            class="rounded-md border border-surface-200 bg-surface-50 p-4 text-sm text-surface-600">
                            Loading recent deliveries...
                        </div>
                        <div v-else-if="recentDeliveries.length === 0"
                            class="rounded-md border border-surface-200 bg-surface-50 p-4 text-sm text-surface-600">
                            No recent deliveries recorded.
                        </div>
                        <ul v-else class="divide-y divide-surface-200">
                            <li v-for="delivery in recentDeliveries" :key="delivery.id" class="py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-surface-900">
                                            {{ formatDate(delivery.delivery_date) }} • {{
                                                formatLiters(delivery.volume_liters) }} L
                                        </p>
                                        <p class="text-xs text-surface-500">
                                            Farmer #{{ delivery.farmer_id }} • Grade {{ delivery.quality_grade ?? 'N/A'
                                            }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-medium text-surface-900">
                                        {{ formatCurrency(delivery.total_amount) }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </section>
                </div>

                <section class="space-y-4 rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-surface-900">Staff &amp; Infrastructure</h2>
                            <p class="text-sm text-surface-500">Overview of operational readiness.</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border border-surface-100 bg-surface-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-surface-500">Staff Count</p>
                            <p class="text-sm font-semibold text-surface-900">{{ center.staff_count ?? '0' }}</p>
                            <p class="text-xs text-surface-500">Team members on site</p>
                        </div>
                        <div class="rounded-lg border border-surface-100 bg-surface-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-surface-500">Testing Equipment</p>
                            <p class="text-sm font-semibold text-surface-900">{{ center.has_testing_equipment ? 'Available' : 'Not available' }}</p>
                            <p class="text-xs text-surface-500">On-site milk quality testing</p>
                        </div>
                        <div class="rounded-lg border border-surface-100 bg-surface-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-surface-500">Washing Bay</p>
                            <p class="text-sm font-semibold text-surface-900">{{ center.has_washing_bay ? 'Available' : 'Not available' }}</p>
                            <p class="text-xs text-surface-500">Cleaning infrastructure</p>
                        </div>
                        <div class="rounded-lg border border-surface-100 bg-surface-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-surface-500">Power Source</p>
                            <p class="text-sm font-semibold text-surface-900">{{ center.power_source ?? 'Not specified' }}</p>
                            <p class="text-xs text-surface-500">Primary energy supply</p>
                        </div>
                    </div>
                </section>
            </section>

            <section v-else-if="activeTab === 'agents'" class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-surface-100">Assigned Agents</h2>
                        <p class="text-sm text-surface-500">Agents managing this collection center.</p>
                    </div>
                    <button
                        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="openCreateAgentModal" :disabled="loading">
                        <Icon icon="mdi:account-plus" :size="16" />
                        Create Agent
                    </button>
                </div>
                <AgentsTable :agents="mccAgents" :loading="mccAgentsLoading" @edit="openAgentEditModal" />
            </section>

            <section v-else class="space-y-6">
                <MilkCenterDeliveryTrend v-if="allDeliveries.length > 0" :deliveries="allDeliveries" />
                <MilkDeliveriesTable :deliveries="allDeliveries" :loading="allDeliveriesLoading"
                    :format-date="formatDate" :format-liters="formatLiters" :format-currency="formatCurrency"
                    @refresh="fetchAllDeliveries" @create="openCreateDeliveryModal" />
            </section>
        </template>
    </div>
    <CreateMilkDeliveryModal :is-open="showCreateDeliveryModal" :center-id="center?.id ?? null"
        @close="closeCreateDeliveryModal" @created="handleDeliveryCreated" />
    <MilkCollectionCenterFormModal :is-open="showEditModal" :center-id="center?.id" :initial-data="center ?? undefined"
        @close="closeEditModal" @updated="handleCenterUpdated" />
    <CreateAgentModal :is-open="showCreateAgentModal" :preselected-mcc-id="center?.id" @close="closeCreateAgentModal"
        @created="handleAgentCreated" />
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import type { MilkCollectionCenter } from '../../stores/geographyStore';
import MilkDeliveriesTable from '../../components/milk-centers/MilkDeliveriesTable.vue';
import MilkCenterDeliveryTrend from '../../components/milk-centers/MilkCenterDeliveryTrend.vue';
import DailyDeliveriesLineChart from '../../components/dashboard/DailyDeliveriesLineChart.vue';
import CreateMilkDeliveryModal from '../../components/milk-centers/CreateMilkDeliveryModal.vue';
import MilkCollectionCenterFormModal from '../../components/milk-centers/MilkCollectionCenterFormModal.vue';
import CreateAgentModal from '../../components/agents/CreateAgentModal.vue';
import AgentsTable from '../../components/agents/AgentsTable.vue';
import { useAgentStore } from '../../stores/agentStore';
import { storeToRefs } from 'pinia';

interface MilkDeliverySummary {
    id: number;
    farmer_id: number;
    delivery_date: string;
    volume_liters: number;
    quality_grade?: string | null;
    total_amount?: number | null;
    price_per_liter?: number | null;
}

type TabKey = 'overview' | 'deliveries' | 'agents';

const route = useRoute();
const centerId = computed(() => Number(route.params.id));

const agentStore = useAgentStore();
const { agents: mccAgents, loading: mccAgentsLoading } = storeToRefs(agentStore);

const center = ref<MilkCollectionCenter | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const farmersCount = ref<number | null>(null);
const averageDailyVolume = ref('—');

const deliveriesLoading = ref(false);
const recentDeliveries = ref<MilkDeliverySummary[]>([]);

const allDeliveriesLoading = ref(false);
const allDeliveries = ref<MilkDeliverySummary[]>([]);
const showCreateDeliveryModal = ref(false);
const showCreateAgentModal = ref(false);

const activeTab = ref<TabKey>('overview');
const tabs = [
    { id: 'overview' as TabKey, label: 'Overview', icon: 'mdi:information-outline' },
    { id: 'deliveries' as TabKey, label: 'Milk Deliveries', icon: 'mdi:truck-delivery-outline' },
    { id: 'agents' as TabKey, label: 'Agents', icon: 'mdi:account-tie-outline' },
];

const fetchCenter = async () => {
    if (Number.isNaN(centerId.value)) {
        error.value = 'Invalid center identifier.';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get<MilkCollectionCenter>(`/milk-collection-centers/${centerId.value}`);
        center.value = response.data;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load milk collection center.';
        center.value = null;
    } finally {
        loading.value = false;
    }
};

const fetchMetrics = async () => {
    if (!center.value) return;

    try {
        const metricsResponse = await axios.get(`/milk-collection-centers/${center.value.id}/metrics`);
        const metrics = metricsResponse.data ?? {};
        farmersCount.value = metrics.farmers_count ?? null;
        averageDailyVolume.value = formatLiters(metrics.average_daily_volume);
    } catch {
        farmersCount.value = null;
        averageDailyVolume.value = '—';
    }
};

const fetchDeliveries = async () => {
    if (!center.value) return;

    deliveriesLoading.value = true;
    try {
        const response = await axios.get<MilkDeliverySummary[]>(`/milk-collection-centers/${center.value.id}/deliveries`, {
            params: { limit: 5 },
        });
        recentDeliveries.value = response.data ?? [];
    } catch {
        recentDeliveries.value = [];
    } finally {
        deliveriesLoading.value = false;
    }
};

const fetchAllDeliveries = async () => {
    if (!center.value) return;

    allDeliveriesLoading.value = true;
    try {
        const response = await axios.get<MilkDeliverySummary[]>(`/milk-collection-centers/${center.value.id}/deliveries`);
        allDeliveries.value = response.data ?? [];
    } catch {
        allDeliveries.value = [];
    } finally {
        allDeliveriesLoading.value = false;
    }
};

const trendLoading = ref(false);
const deliveryTrend = ref<{ date: string; total_volume: number }[]>([]);

const fetchTrend = async () => {
    if (!center.value) return;

    trendLoading.value = true;
    try {
        const response = await axios.get<{ date: string; total_volume: number }[]>(`/milk-collection-centers/${center.value.id}/delivery-trend`);
        deliveryTrend.value = response.data ?? [];
    } catch {
        deliveryTrend.value = [];
    } finally {
        trendLoading.value = false;
    }
};

const refresh = async () => {
    await fetchCenter();
    await Promise.all([fetchMetrics(), fetchDeliveries(), fetchTrend()]);
    if (activeTab.value === 'deliveries') {
        await fetchAllDeliveries();
    } else if (activeTab.value === 'agents') {
        await agentStore.fetchAgents({ milk_collection_center_id: centerId.value });
    }
};

const selectTab = async (tab: TabKey) => {
    activeTab.value = tab;
    if (tab === 'deliveries' && !allDeliveries.value.length) {
        await fetchAllDeliveries();
    } else if (tab === 'agents') {
        await agentStore.fetchAgents({ milk_collection_center_id: centerId.value });
    }
};

const openCreateDeliveryModal = () => {
    showCreateDeliveryModal.value = true;
};

const closeCreateDeliveryModal = () => {
    showCreateDeliveryModal.value = false;
};

const handleDeliveryCreated = async () => {
    await fetchDeliveries();
    await fetchTrend();
    if (activeTab.value === 'deliveries') {
        await fetchAllDeliveries();
    }
};

const openAgentEditModal = (agent: any) => {
    console.log('Edit agent', agent);
};

const showEditModal = ref(false);

const openEditModal = () => {
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const handleCenterUpdated = async () => {
    await fetchCenter();
    showEditModal.value = false;
};

const openCreateAgentModal = () => {
    showCreateAgentModal.value = true;
};

const closeCreateAgentModal = () => {
    showCreateAgentModal.value = false;
};

const handleAgentCreated = async () => {
    await agentStore.fetchAgents({ milk_collection_center_id: centerId.value });
};

watch(
    () => centerId.value,
    (newId, oldId) => {
        if (newId !== oldId && !Number.isNaN(newId)) {
            refresh();
        }
    }
);

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    try {
        return new Date(value).toLocaleDateString();
    } catch {
        return value;
    }
};

const formatNumber = (value?: number | null) => {
    if (value === null || value === undefined) return '—';
    return Number(value).toLocaleString();
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
    return value.toLocaleString(undefined, { style: 'currency', currency: 'USD' });
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

onMounted(async () => {
    await fetchCenter();
    await Promise.all([fetchMetrics(), fetchDeliveries(), fetchTrend()]);
});
</script>
