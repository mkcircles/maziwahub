<template>
    <div class="space-y-8 pb-16 min-h-full">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-lg bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 px-6 py-10 text-white shadow-lg sm:px-10">
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-50">
            </div>
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-primary-500/20 blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-primary-500/20 blur-3xl"></div>

            <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-xl space-y-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-primary-200/80">
                        Administration
                    </p>
                    <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white">Command Center</h1>
                    <p class="text-sm text-primary-100/90 leading-relaxed max-w-lg">
                        Monitor platform activity, jump into key workflows, and keep the dairy network aligned from a
                        single integrated dashboard.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        class="inline-flex items-center gap-2 rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-primary-900 shadow-sm transition-all hover:bg-primary-50 hover:shadow-md active:scale-95">
                        <Icon icon="mdi:lightning-bolt" :size="18" class="text-primary-600" />
                        Quick Actions
                    </button>
                    <button
                        class="inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 backdrop-blur-md px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-white/20 active:scale-95">
                        <Icon icon="mdi:file-download-outline" :size="18" />
                        Export Summary
                    </button>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <StatisticalCard icon="mdi:account-multiple" icon-class="text-primary-500 bg-primary-50"
                class="rounded-md border border-surface-200 bg-white p-6 shadow-sm transition-all hover:shadow-md hover:border-primary-100">
                <template #title><span class="text-surface-600 font-medium">Active Users</span></template>
                <template #default><span class="text-2xl font-bold text-surface-900">—</span></template>
                <template #caption><span class="text-surface-400 text-xs">Administrators online</span></template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:map-marker" icon-class="text-emerald-500 bg-emerald-50"
                class="rounded-md border border-surface-200 bg-white p-6 shadow-sm transition-all hover:shadow-md hover:border-emerald-100">
                <template #title><span class="text-surface-600 font-medium">Geography Layers</span></template>
                <template #default><span class="text-2xl font-bold text-surface-900">—</span></template>
                <template #caption><span class="text-surface-400 text-xs">Countries & Regions</span></template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:storefront" icon-class="text-amber-500 bg-amber-50"
                class="rounded-md border border-surface-200 bg-white p-6 shadow-sm transition-all hover:shadow-md hover:border-amber-100">
                <template #title><span class="text-surface-600 font-medium">Milk Centers</span></template>
                <template #default><span class="text-2xl font-bold text-surface-900">—</span></template>
                <template #caption><span class="text-surface-400 text-xs">Collection hubs</span></template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:cow" icon-class="text-rose-500 bg-rose-50"
                class="rounded-md border border-surface-200 bg-white p-6 shadow-sm transition-all hover:shadow-md hover:border-rose-100">
                <template #title><span class="text-surface-600 font-medium">Livestock Records</span></template>
                <template #default><span class="text-2xl font-bold text-surface-900">—</span></template>
                <template #caption><span class="text-surface-400 text-xs">Cows monitored</span></template>
            </StatisticalCard>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left Column: Navigation Hub -->
            <div class="lg:col-span-2 space-y-6 rounded-lg border border-surface-200 bg-white p-6 sm:p-8 shadow-sm">
                <div
                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-surface-100 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-surface-900">Navigation Hub</h2>
                        <p class="text-sm text-surface-500">
                            Jump straight into key areas
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2">
                    <router-link v-for="link in quickLinks" :key="link.path" :to="link.path"
                        class="group flex flex-col justify-between rounded-md border border-surface-200 bg-surface-50 p-5 transition-all duration-300 hover:border-primary-200 hover:bg-white hover:shadow-md">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-surface-200 text-surface-600 group-hover:text-primary-600 group-hover:ring-primary-100 transition-all">
                                <Icon :icon="link.icon" :size="20" />
                            </div>
                            <span
                                class="inline-flex items-center rounded-md bg-surface-200/50 px-2 py-1 text-[10px] font-semibold text-surface-600 uppercase tracking-wide group-hover:bg-primary-50 group-hover:text-primary-700 transition-colors">
                                {{ link.pill }}
                            </span>
                        </div>
                        <div>
                            <h3
                                class="text-base font-bold text-surface-900 group-hover:text-primary-700 transition-colors">
                                {{
                                    link.label }}</h3>
                            <p class="mt-1 text-sm text-surface-500 line-clamp-2 leading-relaxed">{{ link.description }}
                            </p>
                        </div>
                    </router-link>
                </div>
            </div>

            <!-- Right Column: Graphs & Alerts -->
            <div class="space-y-8">
                <!-- Deliveries Chart -->
                <div class="flex flex-col rounded-lg border border-surface-200 bg-white p-6 sm:p-8 shadow-sm h-full">
                    <div class="flex items-center justify-between mb-6 border-b border-surface-100 pb-4">
                        <h2 class="text-lg font-bold text-surface-900">Deliveries (Liters)</h2>
                        <div
                            class="inline-flex items-center gap-1.5 rounded-md bg-surface-100 px-2.5 py-1 text-xs font-semibold text-surface-600">
                            <Icon icon="mdi:calendar" :size="14" />
                            {{ SUMMARY_DAYS }}D
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center min-h-[250px]">
                        <DailyDeliveriesBarChart v-if="!summaryLoading && !summaryError && dailySummary.length"
                            :summary="dailySummary" class="flex-1" />
                        <div v-else-if="summaryLoading"
                            class="flex h-full items-center justify-center rounded-md bg-surface-50 text-sm text-surface-500 flex-col gap-3">
                            <Icon icon="line-md:loading-twotone-loop" :size="24" class="text-primary-400" />
                            <span>Loading trend…</span>
                        </div>
                        <div v-else-if="summaryError"
                            class="rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 flex items-start gap-2">
                            <Icon icon="mdi:alert-circle" :size="18" class="shrink-0 mt-0.5" />
                            <span>{{ summaryError }}</span>
                        </div>
                        <div v-else
                            class="flex h-full items-center justify-center rounded-md border border-dashed border-surface-200 bg-surface-50 text-sm text-surface-500 flex-col gap-2">
                            <Icon icon="mdi:chart-bar-off" :size="24" class="text-surface-400" />
                            <span>No data available.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Timeline -->
        <div class="rounded-lg border border-surface-200 bg-white p-6 sm:p-8 shadow-sm">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-surface-100 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-surface-900">Operational Timeline</h2>
                    <p class="text-sm text-surface-500">
                        Track upcoming tasks and recent updates.
                    </p>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-surface-100 px-4 py-2 text-sm font-semibold text-surface-700 transition hover:bg-surface-200">
                    <Icon icon="mdi:plus" :size="18" />
                    Add reminder
                </button>
            </div>

            <div
                class="mt-6 flex flex-col items-center justify-center rounded-md border border-dashed border-surface-200 bg-surface-50 py-12 text-center">
                <Icon icon="mdi:timeline-clock-outline" :size="32" class="text-surface-300 mb-3" />
                <p class="text-sm font-medium text-surface-600">No events found</p>
                <p class="text-xs text-surface-400 mt-1 max-w-xs mx-auto">Sync scheduled tasks or add reminders here.
                </p>
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
