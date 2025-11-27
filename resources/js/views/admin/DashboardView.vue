<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-3xl bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Administration
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Command Center</h1>
                    <p class="text-sm text-white/70">
                        Monitor platform activity, jump into key workflows, and keep the dairy network aligned from a single dashboard.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20"
                    >
                        <Icon icon="mdi:lightning-bolt-outline" :size="18" />
                        Quick Actions
                    </button>
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20"
                    >
                        <Icon icon="mdi:file-chart-outline" :size="18" />
                        Export Summary
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard
                icon="mdi:account-multiple-outline"
                icon-class="text-sky-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Active Users</template>
                <template #default>—</template>
                <template #caption>Administrators currently active</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:map-marker-outline"
                icon-class="text-emerald-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Geography Layers</template>
                <template #default>—</template>
                <template #caption>Countries • Regions • Districts</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:milk-outline"
                icon-class="text-amber-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Milk Centers</template>
                <template #default>—</template>
                <template #caption>Collection hubs onboarded</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:cow"
                icon-class="text-rose-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Livestock Records</template>
                <template #default>—</template>
                <template #caption>Cows monitored across farms</template>
            </StatisticalCard>
        </div>

        <div class="space-y-6 rounded-3xl border border-slate-100 bg-white/95 p-8 shadow-lg shadow-slate-100 backdrop-blur">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Navigation Hub</h2>
                    <p class="text-sm text-slate-500">
                        Jump straight into the key areas of the platform. Each card highlights the primary task you can complete there.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-xs font-medium text-slate-600">
                    <Icon icon="mdi:compass-outline" :size="16" />
                    Choose a workspace
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <router-link
                    v-for="link in quickLinks"
                    :key="link.path"
                    :to="link.path"
                    class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/80 p-6 transition hover:-translate-y-1 hover:border-slate-200 hover:bg-white hover:shadow-lg hover:shadow-slate-100"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/40 to-white/0 opacity-0 transition group-hover:opacity-100"></div>
                    <div class="relative z-10 flex h-full flex-col gap-4">
                        <div class="inline-flex items-center gap-2 self-start rounded-full bg-white/90 px-3 py-1 text-xs font-medium text-slate-600 shadow-sm">
                            <Icon :icon="link.icon" :size="16" />
                            {{ link.pill }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ link.label }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ link.description }}</p>
                        </div>
                        <div class="mt-auto inline-flex items-center gap-2 text-sm font-medium text-sky-600 transition group-hover:text-sky-700">
                            Go to {{ link.label }}
                            <Icon icon="mdi:arrow-right" :size="16" />
                        </div>
                    </div>
                </router-link>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white/95 p-8 shadow-lg shadow-slate-100 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daily Milk Deliveries</h2>
                    <p class="text-sm text-slate-500">
                        Aggregated liters collected across all milk collection centers.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-xs font-medium text-slate-600">
                    <Icon icon="mdi:calendar-range" :size="16" />
                    Last {{ SUMMARY_DAYS }} days
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-100 bg-white/90 p-6">
                <DailyDeliveriesBarChart v-if="!summaryLoading && !summaryError && dailySummary.length" :summary="dailySummary" />
                <div
                    v-else-if="summaryLoading"
                    class="flex h-72 items-center justify-center rounded-xl bg-slate-100/60 text-sm text-slate-500"
                >
                    Loading milk delivery trend…
                </div>
                <div
                    v-else-if="summaryError"
                    class="rounded-xl border border-rose-200 bg-rose-50/80 p-6 text-sm text-rose-700"
                >
                    {{ summaryError }}
                </div>
                <div
                    v-else
                    class="flex h-72 items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50/60 text-sm text-slate-500"
                >
                    No delivery data available for the selected window.
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white/95 p-8 shadow-lg shadow-slate-100 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Operational Timeline</h2>
                    <p class="text-sm text-slate-500">
                        Track upcoming tasks and recent updates across the geography and farm management modules.
                    </p>
                </div>
                <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800">
                    <Icon icon="mdi:plus" :size="16" />
                    Add reminder
                </button>
            </div>

            <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-8 text-center text-sm text-slate-500">
                No timeline items yet — sync scheduled tasks or add reminders to see them here.
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import DailyDeliveriesBarChart from '../../components/dashboard/DailyDeliveriesBarChart.vue';
import { useMilkDeliveriesStore } from '../../stores/milkDeliveriesStore';

const SUMMARY_DAYS = 14;

const milkDeliveriesStore = useMilkDeliveriesStore();

const dailySummary = computed(() => milkDeliveriesStore.dailySummary);
const summaryLoading = computed(() => milkDeliveriesStore.dailySummaryLoading);
const summaryError = computed(() => milkDeliveriesStore.dailySummaryError);

onMounted(() => {
    milkDeliveriesStore.fetchDailySummary({ days: SUMMARY_DAYS });
});

const quickLinks = [
    {
        path: '/admin/countries',
        label: 'Countries',
        description: 'Manage national profiles, regions, and supporting infrastructure.',
        pill: 'Geography',
        icon: 'mdi:earth',
    },
    {
        path: '/admin/milk-collection-centers',
        label: 'Milk Centers',
        description: 'Oversee facilities, capacity, and delivery performance.',
        pill: 'Operations',
        icon: 'mdi:storefront-outline',
    },
    {
        path: '/admin/farmers',
        label: 'Farmers',
        description: 'Review farmer profiles, herd data, and registration status.',
        pill: 'Community',
        icon: 'mdi:account-outline',
    },
    {
        path: '/admin/cows',
        label: 'Cows',
        description: 'Track herd health, milk production, and treatment history.',
        pill: 'Livestock',
        icon: 'mdi:cow',
    },
    {
        path: '/admin/milk-deliveries',
        label: 'Deliveries',
        description: 'Analyse milk delivery trends, quality, and payouts.',
        pill: 'Analytics',
        icon: 'mdi:bucket-outline',
    },
    {
        path: '/admin/vets',
        label: 'Vets',
        description: 'Onboard veterinary professionals and monitor treatments.',
        pill: 'Support',
        icon: 'mdi:medical-bag',
    },
    {
        path: '/admin/partners',
        label: 'Partners',
        description: 'Onboard partner organisations and manage their access.',
        pill: 'Allies',
        icon: 'mdi:handshake',
    },
        {
            path: '/admin/partner-claims',
            label: 'MCC Claims',
            description: 'Review and action partner requests to manage collection centers.',
            pill: 'Approvals',
            icon: 'mdi:handshake-outline',
        },
];
</script>

