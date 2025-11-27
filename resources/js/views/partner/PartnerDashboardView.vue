<template>
    <div class="space-y-10">
        <section
            class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-8 py-10 text-white"
        >
            <div class="absolute inset-0 opacity-60">
                <div class="absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_top,rgba(76,201,240,0.35),transparent_65%)]"></div>
            </div>

            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        {{ todayLabel }}
                    </p>
                    <h2 class="text-3xl font-semibold tracking-tight lg:text-4xl">
                        Daily Insight Board
                    </h2>
                    <p class="text-sm text-white/70">
                        Keep tabs on milk movement, collection center performance, and farmer engagement without leaving
                        your partner workspace.
                    </p>
                    <div class="flex flex-wrap gap-3 text-xs">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:storefront-outline" :size="16" />
                            {{ milkCentersCount }} centers active
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:account-group-outline" :size="16" />
                            {{ totalFarmers }} farmers engaged
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:beaker-outline" :size="16" />
                            {{ formattedTodayLiters }} L delivered today
                        </span>
                    </div>
                </div>
                <div class="grid w-full max-w-sm gap-3 rounded-3xl border border-white/10 bg-white/5 p-6 text-sm">
                    <div class="flex items-center justify-between text-white/80">
                        <span>Pending claims</span>
                        <span class="text-base font-semibold text-white">{{ pendingClaimsCount }}</span>
                    </div>
                    <div class="flex items-center justify-between text-white/80">
                        <span>Open invitations</span>
                        <span class="text-base font-semibold text-white">{{ pendingInvitationsCount }}</span>
                    </div>
                    <router-link
                        to="/partner/milk-centers"
                        class="mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                    >
                        Manage centers
                        <Icon icon="mdi:arrow-right" :size="16" />
                    </router-link>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard icon="mdi:storefront-outline" icon-class="text-sky-500">
                <template #title>Milk Centers</template>
                <template #default>{{ milkCentersCount }}</template>
                <template #caption>Active facilities under your partner</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:bucket-outline" icon-class="text-emerald-500">
                <template #title>Today's Volume</template>
                <template #default>{{ formattedTodayLiters }}</template>
                <template #caption>Liters delivered across all centers</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:account-group-outline" icon-class="text-indigo-500">
                <template #title>Farmers</template>
                <template #default>{{ totalFarmers }}</template>
                <template #caption>Farmers linked to your centers</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:clipboard-text-outline" icon-class="text-amber-500">
                <template #title>Pending Claims</template>
                <template #default>{{ pendingClaimsCount }}</template>
                <template #caption>Awaiting admin action</template>
            </StatisticalCard>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">
                        Daily Milk Deliveries
                    </h3>
                    <p class="text-sm text-slate-500">
                        Performance across the past {{ SUMMARY_DAYS }} days. Use this to spot production dips early.
                    </p>
                </div>
                <router-link
                    to="/partner/milk-centers"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                >
                    <Icon icon="mdi:storefront-outline" :size="18" />
                    Go to centers
                </router-link>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200/70 bg-white p-6">
                <DailyDeliveriesBarChart
                    v-if="!summaryLoading && !summaryError && dailySummary.length"
                    :summary="dailySummary"
                />
                <div
                    v-else-if="summaryLoading || initializing"
                    class="flex h-72 items-center justify-center rounded-xl bg-slate-100 text-sm text-slate-500"
                >
                    Loading delivery trend…
                </div>
                <div
                    v-else-if="summaryError"
                    class="rounded-xl border border-rose-200 bg-rose-50/80 p-6 text-sm text-rose-700"
                >
                    {{ summaryError }}
                </div>
                <div
                    v-else
                    class="flex h-72 items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50/80 text-sm text-slate-500"
                >
                    No milk delivery data yet. Encourage MCC teams to start recording!
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">
                        Collection Center Output (Today)
                    </h3>
                    <p class="text-sm text-slate-500">
                        Real-time overview of each center’s contributions and on-ground contacts.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <Icon icon="mdi:calendar-today" :size="18" />
                    {{ todayHuman }}
                </div>
            </div>

            <div v-if="centers.length" class="mt-6 overflow-hidden rounded-2xl border border-slate-200/70">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr class="text-left uppercase tracking-wide">
                            <th class="px-6 py-3 font-semibold">Center</th>
                            <th class="px-6 py-3 font-semibold">Location</th>
                            <th class="px-6 py-3 font-semibold text-right">Today (L)</th>
                            <th class="px-6 py-3 font-semibold text-right">Farmers</th>
                            <th class="px-6 py-3 font-semibold text-right">Pending Claims</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="center in centers" :key="center.id" class="hover:bg-slate-50/60">
                            <td class="px-6 py-4 align-top">
                                <div class="font-semibold text-slate-900">{{ center.name }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ center.physical_address }}
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1 text-[11px] uppercase tracking-wide text-slate-400">
                                    <span v-for="chip in formatArea(center.area)" :key="chip" class="rounded-full bg-slate-100 px-2 py-0.5">
                                        {{ chip }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-slate-600">
                                <div class="flex items-center gap-2">
                                    <Icon icon="mdi:map-marker-radius" :size="18" class="text-sky-500" />
                                    <span>{{ center.area?.district ?? center.area?.region ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-right font-semibold text-slate-900">
                                <span v-if="isCenterLoading(center.id)" class="text-xs font-normal text-slate-500">
                                    Updating…
                                </span>
                                <span v-else>
                                    {{ formatLiters(centerTotalFor(center.id)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top text-right text-slate-600">
                                {{ center.farmers_count ?? '—' }}
                            </td>
                            <td class="px-6 py-4 align-top text-right text-slate-600">
                                {{ center.pending_claims_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <router-link
                                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                                    :to="{ name: 'partner-milk-centers', query: { focus: center.id } }"
                                >
                                    Review
                                    <Icon icon="mdi:arrow-right" :size="16" />
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else
                class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-10 text-center text-sm text-slate-500"
            >
                No milk collection centers yet. Get started by registering one below.
                <div class="mt-4">
                    <router-link
                        to="/partner/milk-centers"
                        class="inline-flex items-center gap-2 rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600"
                    >
                        <Icon icon="mdi:plus" :size="16" />
                        Add milk collection center
                    </router-link>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">
                        Pending Claims & Invitations
                    </h3>
                    <p class="text-sm text-slate-500">
                        Track requests awaiting admin approval and team members who still need to join.
                    </p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-800">Milk center claims</h4>
                        <span class="text-xs font-medium text-slate-500">{{ pendingClaimsCount }} open</span>
                    </div>
                    <ul v-if="pendingClaimsCount" class="mt-4 space-y-3 text-sm">
                        <li
                            v-for="claim in pendingClaims"
                            :key="claim.id"
                            class="rounded-xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 leading-relaxed text-slate-600"
                        >
                            <div class="flex items-center justify-between text-xs uppercase tracking-wide text-slate-400">
                                <span>Center #{{ claim.milk_collection_center_id }}</span>
                                <span>{{ formatDate(claim.created_at) }}</span>
                            </div>
                            <p class="mt-2 font-semibold text-slate-900">
                                {{ claim.milk_collection_center?.name ?? 'Awaiting assignment' }}
                            </p>
                            <p v-if="claim.message" class="mt-1 text-xs text-slate-500">
                                “{{ claim.message }}”
                            </p>
                        </li>
                    </ul>
                    <p v-else class="mt-4 rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-3 text-sm text-slate-500">
                        No pending claims. You’re all caught up!
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200/80 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-800">Open invitations</h4>
                        <span class="text-xs font-medium text-slate-500">{{ pendingInvitationsCount }} pending</span>
                    </div>
                    <ul v-if="pendingInvitationsCount" class="mt-4 space-y-3 text-sm">
                        <li
                            v-for="invite in pendingInvitations"
                            :key="invite.id"
                            class="rounded-xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 leading-relaxed text-slate-600"
                        >
                            <div class="flex items-center justify-between text-xs uppercase tracking-wide text-slate-400">
                                <span>{{ invite.email }}</span>
                                <span>{{ formatDate(invite.created_at) }}</span>
                            </div>
                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ invite.name || 'Invitation sent' }}
                            </p>
                            <p class="text-xs text-slate-500 capitalize">
                                {{ invite.role.replace('_', ' ') }}
                            </p>
                        </li>
                    </ul>
                    <p v-else class="mt-4 rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-3 text-sm text-slate-500">
                        No open invitations. Invite teammates from the Milk Centers page.
                    </p>
                </div>
            </div>
        </section>
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

