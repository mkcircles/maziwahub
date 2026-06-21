<template>
    <div class="space-y-6 pb-16 min-h-full">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-surface-900">Partner Dashboard</h1>
                <p class="text-xs text-surface-500 font-medium">Keep tabs on milk movement, collection center performance, and farmer engagement.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="ynex-btn-secondary py-1.5 px-3 text-xs flex items-center gap-1.5">
                    <Icon icon="mdi:calendar" :size="16" />
                    Today: {{ todayLabel }}
                </button>
                <router-link
                    to="/partner/milk-centers"
                    class="ynex-btn-primary py-1.5 px-3 text-xs flex items-center gap-1.5">
                    Manage Centers
                    <Icon icon="mdi:arrow-right" :size="16" />
                </router-link>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Milk Centers</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            {{ milkCentersCount }}
                        </h3>
                        <p class="text-xs text-primary-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:storefront-outline" :size="14" />
                            <span>Active facilities</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-primary-100 text-primary-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:storefront-outline" :size="24" />
                    </div>
                </div>
            </div>

            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Today's Volume</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            {{ formattedTodayLiters }}
                        </h3>
                        <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:bucket-outline" :size="14" />
                            <span>Delivered today</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:bucket-outline" :size="24" />
                    </div>
                </div>
            </div>

            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Farmers Engaged</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            {{ totalFarmers }}
                        </h3>
                        <p class="text-xs text-amber-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:account-group-outline" :size="14" />
                            <span>Linked to centers</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-amber-100 text-amber-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:account-group-outline" :size="24" />
                    </div>
                </div>
            </div>

            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Pending Claims</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            {{ pendingClaimsCount }}
                        </h3>
                        <p class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:clipboard-text-outline" :size="14" />
                            <span>Awaiting action</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-rose-100 text-rose-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:clipboard-text-outline" :size="24" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart Section -->
        <div class="ynex-card flex flex-col">
            <div class="ynex-card-header">
                <div>
                    <h2 class="text-sm font-bold text-surface-800">Daily Milk Deliveries</h2>
                    <p class="text-[11px] text-surface-400 font-medium mt-0.5">Performance across the past {{ SUMMARY_DAYS }} days.</p>
                </div>
                <router-link
                    to="/partner/milk-centers"
                    class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-0.5"
                >
                    <Icon icon="mdi:storefront-outline" :size="14" /> Go to Centers
                </router-link>
            </div>
            <div class="p-5">
                <DailyDeliveriesBarChart
                    v-if="!summaryLoading && !summaryError && dailySummary.length"
                    :summary="dailySummary"
                />
                <div
                    v-else-if="summaryLoading || initializing"
                    class="flex h-72 items-center justify-center bg-surface-50 rounded text-sm text-surface-400 animate-pulse font-medium"
                >
                    Loading delivery trend…
                </div>
                <div
                    v-else-if="summaryError"
                    class="p-4 border border-red-100 bg-red-50 text-sm text-red-700 rounded"
                >
                    {{ summaryError }}
                </div>
                <div
                    v-else
                    class="flex h-72 items-center justify-center border-2 border-dashed border-surface-200 bg-surface-50 rounded text-sm text-surface-400 font-medium"
                >
                    No milk delivery data yet. Encourage MCC teams to start recording!
                </div>
            </div>
        </div>

        <!-- Output and Claims Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Center Output -->
            <div class="ynex-card lg:col-span-2 flex flex-col">
                <div class="ynex-card-header">
                    <div>
                        <h2 class="text-sm font-bold text-surface-800">Collection Center Output</h2>
                        <p class="text-[11px] text-surface-400 font-medium mt-0.5">Real-time overview for {{ todayHuman }}</p>
                    </div>
                </div>
                <div class="p-5 flex-1">
                    <div v-if="centers.length" class="overflow-x-auto border border-surface-200 rounded">
                        <table class="ynex-table">
                            <thead>
                                <tr>
                                    <th class="ynex-table-th">Center</th>
                                    <th class="ynex-table-th">Location</th>
                                    <th class="ynex-table-th text-right">Today</th>
                                    <th class="ynex-table-th text-right">Farmers</th>
                                    <th class="ynex-table-th"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="center in centers" :key="center.id" class="ynex-table-row">
                                    <td class="ynex-table-td">
                                        <div class="font-bold text-surface-800">{{ center.name }}</div>
                                        <div class="text-xs text-surface-400 mt-0.5">{{ center.physical_address }}</div>
                                    </td>
                                    <td class="ynex-table-td text-surface-600 text-xs">
                                        {{ center.area?.district ?? center.area?.region ?? '—' }}
                                    </td>
                                    <td class="ynex-table-td text-right font-bold text-surface-800">
                                        <span v-if="isCenterLoading(center.id)" class="text-xs font-normal text-surface-400">Updating…</span>
                                        <span v-else>{{ formatLiters(centerTotalFor(center.id)) }}</span>
                                    </td>
                                    <td class="ynex-table-td text-right text-surface-600 font-semibold">
                                        {{ center.farmers_count ?? '—' }}
                                    </td>
                                    <td class="ynex-table-td text-center">
                                        <router-link
                                            class="ynex-btn-secondary py-1 px-2.5 text-xs"
                                            :to="{ name: 'partner-milk-centers', query: { focus: center.id } }"
                                        >
                                            Review
                                        </router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-surface-200 bg-surface-50 rounded text-center"
                    >
                        <p class="text-sm font-bold text-surface-600">No milk collection centers yet.</p>
                        <router-link
                            to="/partner/milk-centers"
                            class="ynex-btn-primary py-1.5 px-3 text-xs mt-3"
                        >
                            <Icon icon="mdi:plus" :size="16" /> Add Center
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Side Cards: Claims & Invitations -->
            <div class="space-y-6">
                <!-- Milk Center Claims -->
                <div class="ynex-card">
                    <div class="ynex-card-header">
                        <h2 class="text-sm font-bold text-surface-800">Milk Center Claims</h2>
                        <span class="text-xs font-semibold bg-primary-100 text-primary-700 px-2 py-0.5 rounded">{{ pendingClaimsCount }} Open</span>
                    </div>
                    <div class="p-5">
                        <ul v-if="pendingClaimsCount" class="space-y-3">
                            <li
                                v-for="claim in pendingClaims"
                                :key="claim.id"
                                class="p-3 bg-surface-50 rounded border border-surface-100"
                            >
                                <div class="flex items-center justify-between text-[10px] text-surface-400 font-bold uppercase tracking-wider">
                                    <span>Center #{{ claim.milk_collection_center_id }}</span>
                                    <span>{{ formatDate(claim.created_at) }}</span>
                                </div>
                                <p class="mt-1.5 font-bold text-xs text-surface-800">
                                    {{ claim.milk_collection_center?.name ?? 'Awaiting assignment' }}
                                </p>
                                <p v-if="claim.message" class="mt-1 text-xs text-surface-500 italic">
                                    “{{ claim.message }}”
                                </p>
                            </li>
                        </ul>
                        <p v-else class="text-xs text-surface-400 font-semibold text-center py-4 bg-surface-50 rounded border border-dashed border-surface-200">
                            No pending claims. All caught up!
                        </p>
                    </div>
                </div>

                <!-- Invitations -->
                <div class="ynex-card">
                    <div class="ynex-card-header">
                        <h2 class="text-sm font-bold text-surface-800">Open Invitations</h2>
                        <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded">{{ pendingInvitationsCount }} Pending</span>
                    </div>
                    <div class="p-5">
                        <ul v-if="pendingInvitationsCount" class="space-y-3">
                            <li
                                v-for="invite in pendingInvitations"
                                :key="invite.id"
                                class="p-3 bg-surface-50 rounded border border-surface-100"
                            >
                                <div class="flex items-center justify-between text-[10px] text-surface-400 font-bold">
                                    <span>{{ invite.email }}</span>
                                    <span>{{ formatDate(invite.created_at) }}</span>
                                </div>
                                <p class="mt-1.5 font-bold text-xs text-surface-800">
                                    {{ invite.name || 'Invitation sent' }}
                                </p>
                                <p class="text-[10px] text-surface-400 font-semibold uppercase tracking-wider mt-0.5 capitalize">
                                    {{ invite.role.replace('_', ' ') }}
                                </p>
                            </li>
                        </ul>
                        <p v-else class="text-xs text-surface-400 font-semibold text-center py-4 bg-surface-50 rounded border border-dashed border-surface-200">
                            No open invitations.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import DailyDeliveriesBarChart from '../../components/dashboard/DailyDeliveriesBarChart.vue';
import { useAuthStore } from '../../stores/authStore';
import { usePartnerStore, type Partner, type PartnerInvitation } from '../../stores/partnerStore';
import { useMilkDeliveriesStore } from '../../stores/milkDeliveriesStore';

const SUMMARY_DAYS = 14;

const authStore = useAuthStore();
const partnerStore = usePartnerStore();
const milkDeliveriesStore = useMilkDeliveriesStore();

const initializing = ref(false);
let lastLoadedPartnerId: number | null = null;

const partnerId = computed(() => partnerStore.activePartner?.id ?? authStore.user?.partner_id ?? null);
const activePartner = computed<Partner | null>(() => partnerStore.activePartner);

const dailySummary = computed(() => milkDeliveriesStore.dailySummary);
const summaryLoading = computed(() => milkDeliveriesStore.dailySummaryLoading);
const summaryError = computed(() => milkDeliveriesStore.dailySummaryError);

const today = computed(() => new Date().toISOString().slice(0, 10));
const todayHuman = computed(() =>
    new Intl.DateTimeFormat('en-US', { weekday: 'long', month: 'long', day: 'numeric' }).format(new Date()),
);
const todayLabel = computed(() =>
    new Intl.DateTimeFormat('en-US', { month: 'long', day: 'numeric', year: 'numeric' }).format(new Date()),
);

const centers = computed(() => activePartner.value?.milk_collection_centers ?? []);

const milkCentersCount = computed(() => centers.value.length);
const totalFarmers = computed(() =>
    centers.value.reduce((sum: number, center: any) => sum + (center.farmers_count ?? 0), 0),
);

const pendingClaims = computed(() => partnerStore.claims.filter(claim => claim.status === 'pending'));
const pendingClaimsCount = computed(() => pendingClaims.value.length);

const pendingInvitations = computed<PartnerInvitation[]>(() =>
    (partnerStore.invitations ?? []).filter(invite => invite.status === 'pending'),
);
const pendingInvitationsCount = computed(() => pendingInvitations.value.length);

const todaysSummary = computed(() => dailySummary.value.find(entry => entry.date === today.value));
const centerTotalsState = milkDeliveriesStore.centerDailyTotals;
const centerTotalsLoadingState = milkDeliveriesStore.centerTotalsLoading;

const centerTotalFor = (centerId: number) => centerTotalsState.value[`${centerId}:${today.value}`] ?? 0;
const isCenterLoading = (centerId: number) => Boolean(centerTotalsLoadingState.value[`${centerId}:${today.value}`]);

const todaysTotalLiters = computed(() => {
    if (todaysSummary.value) {
        return todaysSummary.value.total_volume;
    }

    return centers.value.reduce((sum: number, center: any) => sum + centerTotalFor(center.id), 0);
});

const numberFormatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 1,
});

const formattedTodayLiters = computed(() => `${numberFormatter.format(todaysTotalLiters.value)} L`);

const formatLiters = (value: number | null | undefined) => {
    if (value === null || value === undefined) return '—';
    return `${numberFormatter.format(value)} L`;
};

const formatArea = (area?: Record<string, string>) => {
    if (!area) return [];
    return Object.values(area);
};

const formatDate = (value?: string | null) => {
    if (!value) return '';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
    }).format(new Date(value));
};

const loadCenterTotals = async () => {
    if (!centers.value.length) {
        return;
    }

    await Promise.all(
        centers.value.map(center =>
            milkDeliveriesStore
                .fetchCenterDailyTotal(center.id, { date: today.value })
                .catch(() => {
                    // handled silently; totals default to 0 if request fails
                }),
        ),
    );
};

const initializeDashboard = async (id: number) => {
    initializing.value = true;
    try {
        if (!activePartner.value || activePartner.value.id !== id) {
            await partnerStore.fetchPartner(id);
        }

        await Promise.all([
            milkDeliveriesStore.fetchDailySummary({ days: SUMMARY_DAYS, partner_id: id }),
            partnerStore.fetchClaims(),
        ]);

        await loadCenterTotals();
    } finally {
        initializing.value = false;
    }
};

watch(
    partnerId,
    id => {
        if (!id) return;
        if (lastLoadedPartnerId === id && !initializing.value) {
            return;
        }

        lastLoadedPartnerId = id;
        initializeDashboard(id);
    },
    { immediate: true },
);

watch(
    () => centers.value.map(center => center.id),
    async (ids, prevIds) => {
        if (!ids.length || (prevIds && ids.join(',') === prevIds.join(','))) {
            return;
        }
        await loadCenterTotals();
    },
    { deep: false },
);
</script>

