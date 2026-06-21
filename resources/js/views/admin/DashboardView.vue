<template>
    <div class="space-y-6 pb-16 min-h-full">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-surface-900">Dashboard</h1>
                <p class="text-xs text-surface-500 font-medium">Overview of platform status, activities, and operational performance.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="ynex-btn-secondary py-1.5 px-3 text-xs flex items-center gap-1.5">
                    <Icon icon="mdi:calendar" :size="16" />
                    Today: {{ new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                </button>
                <button
                    class="ynex-btn-primary py-1.5 px-3 text-xs flex items-center gap-1.5">
                    <Icon icon="mdi:file-download-outline" :size="16" />
                    Export Report
                </button>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Active Users</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            <span v-if="dashboardStore.loading" class="animate-pulse">...</span>
                            <span v-else>{{ dashboardStore.adminSummary?.active_users ?? '—' }}</span>
                        </h3>
                        <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:arrow-up-bold-circle-outline" :size="14" />
                            <span>System Admin active</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-primary-100 text-primary-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:account-multiple" :size="24" />
                    </div>
                </div>
            </div>

            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Farmers</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            <span v-if="dashboardStore.loading" class="animate-pulse">...</span>
                            <span v-else>{{ dashboardStore.adminSummary?.registered_farmers ?? '—' }}</span>
                        </h3>
                        <p class="text-xs text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:account-group" :size="14" />
                            <span>Total registered</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:account-group" :size="24" />
                    </div>
                </div>
            </div>

            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Milk Centers</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            <span v-if="dashboardStore.loading" class="animate-pulse">...</span>
                            <span v-else>{{ dashboardStore.adminSummary?.milk_centers ?? '—' }}</span>
                        </h3>
                        <p class="text-xs text-amber-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:storefront" :size="14" />
                            <span>Collection hubs</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-amber-100 text-amber-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:storefront" :size="24" />
                    </div>
                </div>
            </div>

            <div class="ynex-card group">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider">Livestock Records</p>
                        <h3 class="text-2xl font-extrabold text-surface-900 mt-1">
                            <span v-if="dashboardStore.loading" class="animate-pulse">...</span>
                            <span v-else>{{ dashboardStore.adminSummary?.cows_monitored ?? '—' }}</span>
                        </h3>
                        <p class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                            <Icon icon="mdi:cow" :size="14" />
                            <span>Cows monitored</span>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded bg-rose-100 text-rose-600 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Icon icon="mdi:cow" :size="24" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Deliveries Chart -->
            <div class="ynex-card lg:col-span-2 flex flex-col">
                <div class="ynex-card-header">
                    <h2 class="text-sm font-bold text-surface-800">Deliveries Trend (Liters)</h2>
                    <span class="text-xs font-semibold text-surface-400 bg-surface-100 px-2 py-0.5 rounded">{{ SUMMARY_DAYS }}D Trend</span>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-center min-h-[320px] relative">
                    <DailyDeliveriesLineChart v-if="!summaryLoading && !summaryError && dailySummary.length"
                        :summary="dailySummary" class="flex-1 w-full" />
                    <div v-else-if="summaryLoading"
                        class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm text-sm text-surface-500 flex-col gap-3">
                        <Icon icon="line-md:loading-twotone-loop" :size="32" class="text-primary-500" />
                        <span class="font-medium animate-pulse">Loading trend tracking…</span>
                    </div>
                    <div v-else-if="summaryError"
                        class="p-4 border border-red-100 bg-red-50 text-sm text-red-700 rounded flex items-start gap-2">
                        <Icon icon="mdi:alert-circle" :size="20" class="shrink-0 text-red-500" />
                        <span class="font-medium">{{ summaryError }}</span>
                    </div>
                    <div v-else
                        class="flex h-full min-h-[250px] items-center justify-center rounded border-2 border-dashed border-surface-200 bg-surface-50 text-sm text-surface-500 flex-col gap-3">
                        <Icon icon="mdi:chart-bar-off" :size="32" class="text-surface-300" />
                        <span class="font-medium">No data available for this period.</span>
                    </div>
                </div>
            </div>

            <!-- Operations Timeline / Reminders -->
            <div class="ynex-card flex flex-col">
                <div class="ynex-card-header">
                    <h2 class="text-sm font-bold text-surface-800">Operational Timeline</h2>
                    <button class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-0.5">
                        <Icon icon="mdi:plus" :size="14" /> Add
                    </button>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-center">
                    <div class="text-center py-8">
                        <div class="h-12 w-12 rounded bg-surface-100 flex items-center justify-center mx-auto mb-3">
                            <Icon icon="mdi:timeline-clock-outline" :size="24" class="text-surface-400" />
                        </div>
                        <p class="text-sm font-bold text-surface-700">No events scheduled</p>
                        <p class="text-xs font-medium text-surface-400 mt-1 max-w-xs mx-auto leading-relaxed">
                            Sync your scheduled tasks, meetings, or add custom reminders to track operations.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Hub -->
        <div class="ynex-card">
            <div class="ynex-card-header">
                <h2 class="text-sm font-bold text-surface-800">System Quick Navigation</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <router-link v-for="link in quickLinks" :key="link.path" :to="link.path"
                        class="p-4 rounded border border-surface-100 hover:border-primary-100 hover:bg-primary-50/50 flex flex-col items-center text-center transition-all duration-200 group">
                        <div class="h-10 w-10 rounded bg-surface-50 text-surface-600 flex items-center justify-center mb-2 group-hover:bg-primary-100 group-hover:text-primary-600 transition-colors shadow-sm">
                            <Icon :icon="link.icon" :size="20" />
                        </div>
                        <span class="text-xs font-bold text-surface-800 group-hover:text-primary-700 transition-colors">{{ link.label }}</span>
                        <span class="text-[10px] text-surface-400 font-semibold uppercase tracking-wider mt-1">{{ link.pill }}</span>
                    </router-link>
                </div>
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
