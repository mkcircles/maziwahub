<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <router-link
                    to="/admin/milk-collection-centers"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to centers
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-200">{{ center?.name ?? 'Milk Collection Center' }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ center?.registration_number ?? 'Unregistered center' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    @click="refresh"
                    :disabled="loading"
                >
                    <Icon icon="mdi:refresh" :size="18" />
                    Refresh
                </button>
            </div>
        </div>

        <div v-if="loading" class="rounded-lg bg-white p-8 text-center text-gray-600 shadow">
            Loading center details...
        </div>
        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            {{ error }}
        </div>
        <div v-else-if="!center" class="rounded-lg bg-white p-8 text-center text-gray-600 shadow">
            Milk collection center not found.
        </div>

        <template v-else>
            <div class="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 bg-white/70 p-1">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition"
                    :class="[
                        activeTab === tab.id
                            ? 'bg-blue-600 text-white shadow'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                    ]"
                    @click="selectTab(tab.id)"
                >
                    <span>{{ tab.label }}</span>
                </button>
            </div>

            <section v-if="activeTab === 'overview'" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <StatisticalCard icon="mdi:warehouse" icon-class="text-emerald-600">
                        <template #title>Cooling Capacity</template>
                        <template #default>{{ formatNumber(center.cooler_capacity_liters) }} L</template>
                        <template #caption>Maximum storage per day</template>
                    </StatisticalCard>
                    <StatisticalCard icon="mdi:account-group" icon-class="text-blue-600">
                        <template #title>Registered Farmers</template>
                        <template #default>{{ farmersCount ?? '—' }}</template>
                        <template #caption>Farmers attached to this center</template>
                    </StatisticalCard>
                    <StatisticalCard icon="mdi:bucket-outline" icon-class="text-amber-500">
                        <template #title>Average Daily Volume</template>
                        <template #default>{{ averageDailyVolume }}</template>
                        <template #caption>Based on recent deliveries</template>
                    </StatisticalCard>
                    <StatisticalCard icon="mdi:flash" icon-class="text-purple-500">
                        <template #title>Power Source</template>
                        <template #default>{{ center.power_source ?? 'Not specified' }}</template>
                        <template #caption>Primary facility power</template>
                    </StatisticalCard>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <section class="space-y-4 rounded-lg bg-white p-6 shadow">
                        <h2 class="text-lg font-semibold text-gray-900">Facility Details</h2>
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ center.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Registration #</dt>
                                <dd class="text-sm text-gray-700">{{ center.registration_number ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Manager</dt>
                                <dd class="text-sm text-gray-700">{{ center.manager_name ?? 'Not assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Manager Phone</dt>
                                <dd class="text-sm text-gray-700">{{ center.manager_phone ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Established</dt>
                                <dd class="text-sm text-gray-700">{{ formatDate(center.established_date) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Staff Count</dt>
                                <dd class="text-sm text-gray-700">{{ center.staff_count ?? '0' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Address</dt>
                                <dd class="text-sm text-gray-700">{{ center.physical_address }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">Location</dt>
                                <dd class="text-sm text-gray-700">{{ formatLocation(center) }}</dd>
                            </div>
                        </dl>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-if="center.has_testing_equipment"
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium uppercase tracking-wide text-emerald-700"
                            >
                                <Icon icon="mdi:test-tube" :size="14" /> Testing Equipment
                            </span>
                            <span
                                v-if="center.has_washing_bay"
                                class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium uppercase tracking-wide text-blue-700"
                            >
                                <Icon icon="mdi:water" :size="14" /> Washing Bay
                            </span>
                        </div>
                    </section>

                    <section class="space-y-4 rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Deliveries</h2>
                            <div class="flex items-center gap-2">
                                <button
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                                    @click="fetchDeliveries"
                                    :disabled="deliveriesLoading"
                                >
                                    <Icon icon="mdi:refresh" :size="16" />
                                    Refresh
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    @click="openCreateDeliveryModal"
                                    :disabled="deliveriesLoading"
                                >
                                    <Icon icon="mdi:plus" :size="16" />
                                    Record Delivery
                                </button>
                            </div>
                        </div>
                        <div v-if="deliveriesLoading" class="rounded-md border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                            Loading recent deliveries...
                        </div>
                        <div v-else-if="recentDeliveries.length === 0" class="rounded-md border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                            No recent deliveries recorded.
                        </div>
                        <ul v-else class="divide-y divide-gray-200">
                            <li v-for="delivery in recentDeliveries" :key="delivery.id" class="py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ formatDate(delivery.delivery_date) }} • {{ formatLiters(delivery.volume_liters) }} L
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Farmer #{{ delivery.farmer_id }} • Grade {{ delivery.quality_grade ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">
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
                            <h2 class="text-lg font-semibold text-gray-900">Staff &amp; Infrastructure</h2>
                            <p class="text-sm text-gray-500">Overview of operational readiness.</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Staff Count</p>
                            <p class="text-sm font-semibold text-gray-900">{{ center.staff_count ?? '0' }}</p>
                            <p class="text-xs text-gray-500">Team members on site</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Testing Equipment</p>
                            <p class="text-sm font-semibold text-gray-900">{{ center.has_testing_equipment ? 'Available' : 'Not available' }}</p>
                            <p class="text-xs text-gray-500">On-site milk quality testing</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Washing Bay</p>
                            <p class="text-sm font-semibold text-gray-900">{{ center.has_washing_bay ? 'Available' : 'Not available' }}</p>
                            <p class="text-xs text-gray-500">Cleaning infrastructure</p>
                        </div>
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Power Source</p>
                            <p class="text-sm font-semibold text-gray-900">{{ center.power_source ?? 'Not specified' }}</p>
                            <p class="text-xs text-gray-500">Primary energy supply</p>
                        </div>
                    </div>
                </section>
            </section>

            <section v-else class="space-y-6">
                <MilkDeliveriesTable
                    :deliveries="allDeliveries"
                    :loading="allDeliveriesLoading"
                    :format-date="formatDate"
                    :format-liters="formatLiters"
                    :format-currency="formatCurrency"
                    @refresh="fetchAllDeliveries"
                    @create="openCreateDeliveryModal"
                />
            </section>
        </template>
    </div>
    <CreateMilkDeliveryModal
        :is-open="showCreateDeliveryModal"
        :center-id="center?.id ?? null"
        @close="closeCreateDeliveryModal"
        @created="handleDeliveryCreated"
    />
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import type { MilkCollectionCenter } from '../../stores/geographyStore';
import MilkDeliveriesTable from '../../components/milk-centers/MilkDeliveriesTable.vue';
import CreateMilkDeliveryModal from '../../components/milk-centers/CreateMilkDeliveryModal.vue';

interface MilkDeliverySummary {
    id: number;
    farmer_id: number;
    delivery_date: string;
    volume_liters: number;
    quality_grade?: string | null;
    total_amount?: number | null;
    price_per_liter?: number | null;
}

type TabKey = 'overview' | 'deliveries';

const route = useRoute();
const centerIdParam = route.params.id;
const centerId = Number(centerIdParam);

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

const activeTab = ref<TabKey>('overview');
const tabs = [
    { id: 'overview' as TabKey, label: 'Overview' },
    { id: 'deliveries' as TabKey, label: 'Milk Deliveries' },
];

const fetchCenter = async () => {
    if (Number.isNaN(centerId)) {
        error.value = 'Invalid center identifier.';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get<MilkCollectionCenter>(`/milk-collection-centers/${centerId}`);
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

const refresh = async () => {
    await fetchCenter();
    await Promise.all([fetchMetrics(), fetchDeliveries()]);
    if (activeTab.value === 'deliveries') {
        await fetchAllDeliveries();
    }
};

const selectTab = async (tab: TabKey) => {
    activeTab.value = tab;
    if (tab === 'deliveries' && !allDeliveries.value.length) {
        await fetchAllDeliveries();
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
    if (activeTab.value === 'deliveries') {
        await fetchAllDeliveries();
    }
};

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
    await Promise.all([fetchMetrics(), fetchDeliveries()]);
});
</script>

