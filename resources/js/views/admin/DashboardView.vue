<template>
    <div class="space-y-8 pb-16 min-h-full">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-tr from-primary-950 via-primary-900 to-primary-800 px-8 py-12 text-white shadow-2xl shadow-primary-900/20 sm:px-12 mb-8">
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-60 mix-blend-overlay">
            </div>
            <div
                class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-primary-500/20 blur-[100px] pointer-events-none">
            </div>
            <div
                class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-blue-500/20 blur-[80px] pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-2xl space-y-4">
                    <p
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-primary-100 backdrop-blur-md border border-white/10 shadow-inner">
                        <Icon icon="mdi:shield-crown-outline" :size="14" />
                        Administration
                    </p>
                    <h1
                        class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl text-transparent bg-clip-text bg-gradient-to-r from-white via-primary-50 to-primary-200">
                        Command Center
                    </h1>
                    <p class="text-base text-primary-100/90 leading-relaxed max-w-lg font-light">
                        Monitor platform activity, jump into key workflows, and keep the dairy network aligned from a
                        single integrated dashboard.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <button
                        class="group relative inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-primary-900 shadow-lg shadow-white/10 transition-all hover:bg-primary-50 hover:shadow-xl hover:-translate-y-0.5 active:scale-95 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary-100/0 via-primary-100/30 to-primary-100/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                        </div>
                        <Icon icon="mdi:lightning-bolt" :size="20" class="text-primary-600" />
                        Quick Actions
                    </button>
                    <button
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 backdrop-blur-md px-5 py-3 text-sm font-bold text-white shadow-lg transition-all hover:bg-white/20 hover:shadow-xl hover:-translate-y-0.5 active:scale-95">
                        <Icon icon="mdi:file-download-outline" :size="20" />
                        Export Summary
                    </button>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <StatisticalCard icon="mdi:account-multiple" icon-class="text-primary-600 bg-primary-100/50"
                class="group relative overflow-hidden rounded-md border border-surface-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-primary-200 z-10 after:absolute after:inset-x-0 after:bottom-0 after:h-1 after:bg-gradient-to-r after:from-primary-400 after:to-primary-600 after:origin-left after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-500">
                <template #title><span class="text-surface-600 font-semibold tracking-wide">Active
                        Users</span></template>
                <template #default>
                    <span v-if="dashboardStore.loading"
                        class="text-3xl font-extrabold text-surface-400 tracking-tight animate-pulse">...</span>
                    <span v-else class="text-3xl font-extrabold text-surface-900 tracking-tight">{{
                        dashboardStore.adminSummary?.active_users ?? '—' }}</span>
                </template>
                <template #caption><span class="text-surface-400 text-sm font-medium">Administrators
                        online</span></template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:map-marker" icon-class="text-emerald-600 bg-emerald-100/50"
                class="group relative overflow-hidden rounded-md border border-surface-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-emerald-200 z-10 after:absolute after:inset-x-0 after:bottom-0 after:h-1 after:bg-gradient-to-r after:from-emerald-400 after:to-emerald-600 after:origin-left after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-500">
                <template #title><span class="text-surface-600 font-semibold tracking-wide">Geography
                        Layers</span></template>
                <template #default>
                    <span v-if="dashboardStore.loading"
                        class="text-3xl font-extrabold text-surface-400 tracking-tight animate-pulse">...</span>
                    <span v-else class="text-3xl font-extrabold text-surface-900 tracking-tight">{{
                        dashboardStore.adminSummary?.geography_layers ?? '—' }}</span>
                </template>
                <template #caption><span class="text-surface-400 text-sm font-medium">Countries &
                        Regions</span></template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:storefront" icon-class="text-amber-600 bg-amber-100/50"
                class="group relative overflow-hidden rounded-md border border-surface-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-amber-200 z-10 after:absolute after:inset-x-0 after:bottom-0 after:h-1 after:bg-gradient-to-r after:from-amber-400 after:to-amber-600 after:origin-left after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-500">
                <template #title><span class="text-surface-600 font-semibold tracking-wide">Milk
                        Centers</span></template>
                <template #default>
                    <span v-if="dashboardStore.loading"
                        class="text-3xl font-extrabold text-surface-400 tracking-tight animate-pulse">...</span>
                    <span v-else class="text-3xl font-extrabold text-surface-900 tracking-tight">{{
                        dashboardStore.adminSummary?.milk_centers ?? '—' }}</span>
                </template>
                <template #caption><span class="text-surface-400 text-sm font-medium">Collection hubs</span></template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:cow" icon-class="text-rose-600 bg-rose-100/50"
                class="group relative overflow-hidden rounded-md border border-surface-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:border-rose-200 z-10 after:absolute after:inset-x-0 after:bottom-0 after:h-1 after:bg-gradient-to-r after:from-rose-400 after:to-rose-600 after:origin-left after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-500">
                <template #title><span class="text-surface-600 font-semibold tracking-wide">Livestock
                        Records</span></template>
                <template #default>
                    <span v-if="dashboardStore.loading"
                        class="text-3xl font-extrabold text-surface-400 tracking-tight animate-pulse">...</span>
                    <span v-else class="text-3xl font-extrabold text-surface-900 tracking-tight">{{
                        dashboardStore.adminSummary?.cows_monitored ?? '—' }}</span>
                </template>
                <template #caption><span class="text-surface-400 text-sm font-medium">Cows monitored</span></template>
            </StatisticalCard>
        </div>

        <div class="space-y-8">
            <!-- Navigation Hub -->
            <div
                class="space-y-6 rounded-md border border-surface-200/80 bg-white/80 backdrop-blur-xl p-5 sm:p-6 shadow-sm transition-all hover:shadow-md">
                <div
                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-surface-100/80 pb-5">
                    <div>
                        <h2 class="text-xl font-bold text-surface-900 tracking-tight">Navigation Hub</h2>
                        <p class="text-sm font-medium text-surface-500 mt-1">
                            Jump straight into key areas
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <router-link v-for="link in quickLinks" :key="link.path" :to="link.path"
                        class="group flex flex-col justify-between rounded-md border border-surface-200/60 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-primary-300 hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] hover:shadow-primary-100/40">
                        <div class="flex items-start justify-between mb-5">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-surface-50 shadow-sm border border-surface-100 text-surface-600 group-hover:bg-primary-50 group-hover:text-primary-600 group-hover:border-primary-200 transition-all duration-300 transform group-hover:scale-110 group-hover:rotate-3">
                                <Icon :icon="link.icon" :size="24" />
                            </div>
                            <span
                                class="inline-flex items-center rounded-full bg-surface-100 px-2.5 py-1 text-[11px] font-bold text-surface-600 uppercase tracking-widest group-hover:bg-primary-100 group-hover:text-primary-700 transition-colors shadow-sm">
                                {{ link.pill }}
                            </span>
                        </div>
                        <div>
                            <h3
                                class="text-lg font-bold text-surface-900 group-hover:text-primary-700 transition-colors">
                                {{ link.label }}</h3>
                            <p class="mt-2 text-sm text-surface-500 line-clamp-2 leading-relaxed font-medium">{{
                                link.description }}
                            </p>
                        </div>
                    </router-link>
                </div>
            </div>

            <!-- Graphs & Alerts -->
            <div class="flex flex-col">
                <!-- Deliveries Chart -->
                <div
                    class="flex flex-col flex-grow rounded-md border border-surface-200/80 bg-white/80 backdrop-blur-xl p-5 sm:p-6 shadow-sm transition-all hover:shadow-md h-full">
                    <div class="flex items-center justify-between mb-6 border-b border-surface-100/80 pb-5">
                        <h2 class="text-xl font-bold text-surface-900 tracking-tight">Deliveries (Liters)</h2>
                        <div
                            class="inline-flex items-center gap-1.5 rounded-lg bg-surface-100 px-3 py-1.5 text-xs font-bold text-surface-700 shadow-sm border border-surface-200/50 hover:bg-surface-200 transition-colors cursor-default">
                            <Icon icon="mdi:calendar" :size="16" />
                            {{ SUMMARY_DAYS }}D Trend
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center min-h-[300px] relative">
                        <DailyDeliveriesLineChart v-if="!summaryLoading && !summaryError && dailySummary.length"
                            :summary="dailySummary" class="flex-1 w-full" />
                        <div v-else-if="summaryLoading"
                            class="absolute inset-0 flex items-center justify-center rounded-2xl bg-surface-50/50 backdrop-blur-sm text-sm text-surface-500 flex-col gap-3 z-10">
                            <Icon icon="line-md:loading-twotone-loop" :size="32" class="text-primary-500" />
                            <span class="font-medium animate-pulse">Loading trend tracking…</span>
                        </div>
                        <div v-else-if="summaryError"
                            class="rounded-xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700 flex items-start gap-3 shadow-sm">
                            <Icon icon="mdi:alert-circle" :size="20" class="shrink-0 mt-0.5 text-rose-500" />
                            <span class="font-medium">{{ summaryError }}</span>
                        </div>
                        <div v-else
                            class="flex h-full min-h-[250px] items-center justify-center rounded-2xl border-2 border-dashed border-surface-200 bg-surface-50/50 text-sm text-surface-500 flex-col gap-3 group transition-colors hover:border-primary-200 hover:bg-primary-50/30">
                            <Icon icon="mdi:chart-bar-off" :size="32"
                                class="text-surface-300 group-hover:text-primary-300 transition-colors" />
                            <span class="font-medium group-hover:text-primary-600 transition-colors">No data available
                                for the
                                period.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Timeline -->
        <div
            class="rounded-md border border-surface-200/80 bg-white/80 backdrop-blur-xl p-5 sm:p-6 shadow-sm transition-all hover:shadow-md">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-surface-100/80 pb-5">
                <div>
                    <h2 class="text-xl font-bold text-surface-900 tracking-tight">Operational Timeline</h2>
                    <p class="text-sm font-medium text-surface-500 mt-1">
                        Track upcoming tasks and recent platform updates.
                    </p>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-xl bg-surface-100 px-5 py-2.5 text-sm font-bold text-surface-700 shadow-sm border border-surface-200/50 transition-all hover:bg-primary-50 hover:text-primary-700 hover:border-primary-200 hover:-translate-y-0.5 active:scale-95">
                    <Icon icon="mdi:plus" :size="20" />
                    Add Reminder
                </button>
            </div>

            <div
                class="mt-8 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-surface-200 bg-surface-50/50 py-16 text-center transition-colors hover:bg-surface-100/50 hover:border-surface-300">
                <div class="h-16 w-16 rounded-full bg-surface-100 flex items-center justify-center mb-4 shadow-sm">
                    <Icon icon="mdi:timeline-clock-outline" :size="32" class="text-surface-400" />
                </div>
                <p class="text-base font-bold text-surface-700">No events scheduled</p>
                <p class="text-sm font-medium text-surface-500 mt-2 max-w-sm mx-auto leading-relaxed">
                    Sync your scheduled tasks, meetings, or add custom reminders here to keep track of your operations.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import DailyDeliveriesLineChart from '../../components/dashboard/DailyDeliveriesLineChart.vue';
import { useMilkDeliveriesStore } from '../../stores/milkDeliveriesStore';
import { useDashboardStore } from '../../stores/dashboardStore';

const SUMMARY_DAYS = 14;

const milkDeliveriesStore = useMilkDeliveriesStore();
const dashboardStore = useDashboardStore();

const dailySummary = computed(() => milkDeliveriesStore.dailySummary);
const summaryLoading = computed(() => milkDeliveriesStore.dailySummaryLoading);
const summaryError = computed(() => milkDeliveriesStore.dailySummaryError);

onMounted(() => {
    milkDeliveriesStore.fetchDailySummary({ days: SUMMARY_DAYS });
    dashboardStore.fetchAdminSummary();
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
