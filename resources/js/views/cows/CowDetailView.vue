<template>
    <div class="space-y-8 pb-16">
        <div
            v-if="loading"
            class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-100"
        >
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <router-link
                    to="/admin/cows"
                    class="inline-flex items-center gap-2 self-start rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to cows
                </router-link>
                <div class="flex items-center gap-3 text-slate-500">
                    <Icon icon="mdi:loading" :size="22" class="animate-spin" />
                    <span class="text-sm font-medium">Loading cow details…</span>
                </div>
            </div>
        </div>
        <div
            v-else-if="error"
            class="rounded-3xl border border-red-200/60 bg-red-50/80 p-8 text-red-700 shadow-sm shadow-red-100"
        >
            <div class="flex flex-col gap-4">
                <router-link
                    to="/admin/cows"
                    class="inline-flex items-center gap-2 self-start rounded-full bg-red-100/80 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-200/80"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to cows
                </router-link>
                <p class="text-sm font-medium">{{ error }}</p>
            </div>
        </div>
        <div
            v-else-if="!cow"
            class="rounded-3xl border border-slate-200 bg-white p-10 text-center text-slate-600 shadow-sm shadow-slate-100"
        >
            <div class="mx-auto flex max-w-xl flex-col items-center gap-4">
                <router-link
                    to="/admin/cows"
                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to cows
                </router-link>
                <p class="text-base font-medium">Cow record not found.</p>
            </div>
        </div>

        <template v-else>
            <div
                class="relative overflow-hidden rounded-xl bg-[#0F172A] p-5 text-white shadow-xl shadow-blue-900/40"
            >
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
                <div class="relative flex flex-col gap-8">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <router-link
                            to="/admin/cows"
                            class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                        >
                            <Icon icon="mdi:arrow-left" :size="18" />
                            Back to cows
                        </router-link>
                        <button
                            class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1 text-sm font-medium text-white backdrop-blur transition hover:bg-white/30 disabled:cursor-not-allowed disabled:opacity-60"
                            @click="refresh"
                            :disabled="loading"
                        >
                            <Icon icon="mdi:refresh" :size="18" />
                            Refresh data
                        </button>
                    </div>

                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-white/60">
                                Cow Profile
                            </p>
                            <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">
                                {{ cow?.tag_number ?? 'Cow' }}
                            </h1>
                            <p class="mt-2 text-xs text-white/80 md:text-sm">
                                Registered {{ formatDate(cow?.created_at) }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2 text-[11px] md:text-xs">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white/90"
                                >
                                    <Icon icon="mdi:cow" :size="16" class="text-white/80" />
                                    {{ cow.breed ?? 'Unknown breed' }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white/90"
                                >
                                    <Icon icon="mdi:leaf" :size="16" class="text-white/80" />
                                    {{ cow.health_status ?? 'Health check pending' }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white/90"
                                >
                                    <Icon icon="mdi:calendar-clock" :size="16" class="text-white/80" />
                                    {{ formatAge(cow.date_of_birth) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 text-sm text-white/85">
                            <div class="inline-flex items-center gap-2">
                                <Icon icon="mdi:account-badge" :size="18" class="text-white/80" />
                                <span>
                                    Farmer:
                                    <router-link
                                        v-if="cow.farmer?.id"
                                        :to="`/admin/farmers/${cow.farmer.id}`"
                                        class="font-semibold text-white underline decoration-white/50 underline-offset-4 transition hover:text-white"
                                    >
                                        {{ formatFarmerName(cow) }}
                                    </router-link>
                                    <span v-else class="font-semibold text-white/80">Unassigned</span>
                                </span>
                            </div>
                            <div class="inline-flex items-center gap-2">
                                <Icon icon="mdi:calendar-month" :size="18" class="text-white/80" />
                                <span>Acquired {{ formatDate(cow.acquired_date) }}</span>
                            </div>
                            <div class="inline-flex items-center gap-2">
                                <Icon icon="mdi:map-marker-radius" :size="18" class="text-white/80" />
                                <span>{{ cow.notes ? cow.notes : 'No additional notes for this cow.' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatisticalCard
                    icon="mdi:cow"
                    icon-class="text-emerald-500"
                    class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                >
                    <template #title>Breed</template>
                    <template #default>{{ cow.breed ?? 'Unknown' }}</template>
                    <template #caption>Recorded breed information</template>
                </StatisticalCard>
                <StatisticalCard
                    icon="mdi:bucket-outline"
                    icon-class="text-blue-500"
                    class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                >
                    <template #title>Milk Capacity</template>
                    <template #default>{{ formatLiters(cow.milk_capacity_liters) }}</template>
                    <template #caption>Average daily milk potential</template>
                </StatisticalCard>
                <StatisticalCard
                    icon="mdi:calendar"
                    icon-class="text-amber-500"
                    class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                >
                    <template #title>Age</template>
                    <template #default>{{ formatAge(cow.date_of_birth) }}</template>
                    <template #caption>Based on date of birth</template>
                </StatisticalCard>
                <StatisticalCard
                    icon="mdi:leaf"
                    icon-class="text-lime-500"
                    class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                >
                    <template #title>Health Status</template>
                    <template #default>{{ cow.health_status ?? 'Not recorded' }}</template>
                    <template #caption>Latest health assessment</template>
                </StatisticalCard>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm shadow-slate-100">
                <nav class="flex flex-wrap gap-2" aria-label="Cow detail tabs">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 focus-visible:ring-offset-2"
                        :class="tabButtonClass(tab.id)"
                        @click="setActiveTab(tab.id)"
                    >
                        <Icon :icon="tab.icon" :size="16" />
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <div v-if="activeTab === 'overview'" class="grid gap-6 lg:grid-cols-2">
                <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Ownership &amp; Identification</h2>
                        <router-link
                            v-if="cow.farmer?.id"
                            :to="`/admin/farmers/${cow.farmer.id}`"
                            class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700"
                        >
                            View farmer
                            <Icon icon="mdi:chevron-right" :size="16" />
                        </router-link>
                    </div>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Tag Number</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ cow.tag_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Farmer</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ formatFarmerName(cow) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Date of Birth</dt>
                            <dd class="text-sm text-gray-700">{{ formatDate(cow.date_of_birth) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Acquired Date</dt>
                            <dd class="text-sm text-gray-700">{{ formatDate(cow.acquired_date) }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Notes</dt>
                            <dd class="text-sm text-gray-700">{{ cow.notes ?? 'No notes recorded.' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Facts</h2>
                    <ul class="divide-y divide-slate-100 text-sm text-gray-700">
                        <li class="flex items-center justify-between py-2 first:pt-0">
                            <span>Milk Capacity</span>
                            <span>{{ formatLiters(cow.milk_capacity_liters) }}</span>
                        </li>
                        <li class="flex items-center justify-between py-2">
                            <span>Health Status</span>
                            <span>{{ cow.health_status ?? 'Not recorded' }}</span>
                        </li>
                        <li class="flex items-center justify-between py-2">
                            <span>Registered</span>
                            <span>{{ formatDate(cow.created_at) }}</span>
                        </li>
                        <li class="flex items-center justify-between py-2 last:pb-0">
                            <span>Last Updated</span>
                            <span>{{ formatDate(cow.updated_at) }}</span>
                        </li>
                    </ul>
                </section>
            </div>

            <div v-else-if="activeTab === 'milk'" class="space-y-6">
                <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Milk Production Metrics</h2>
                        <button
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                            @click="refreshMilkRecords"
                            :disabled="milkLoading"
                        >
                            <Icon icon="mdi:refresh" :size="16" />
                            Refresh
                        </button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Last Recording</p>
                            <p class="text-sm font-medium text-gray-900">{{ formatDate(lastRecordDate) }}</p>
                            <p class="text-xs text-gray-500">Most recent entry</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Average Daily Total</p>
                            <p class="text-sm font-medium text-gray-900">{{ formatLiters(averageDailyVolume) }}</p>
                            <p class="text-xs text-gray-500">Across all recorded days</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Peak Daily Total</p>
                            <p class="text-sm font-medium text-gray-900">{{ formatLiters(peakDailyVolume) }}</p>
                            <p class="text-xs text-gray-500">Highest recorded yield</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Records Count</p>
                            <p class="text-sm font-medium text-gray-900">{{ milkRecords.length }}</p>
                            <p class="text-xs text-gray-500">Total production entries</p>
                        </div>
                    </div>
                </section>

                <section class="space-y-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Milk Production Trends</h2>
                            <p class="text-sm text-gray-500">Morning, midday, and evening volumes over time.</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <CowMilkProductionChart :records="milkRecords" />
                    </div>
                </section>
            </div>

            <div v-else-if="activeTab === 'treatments'" class="space-y-4">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Veterinary Treatments</h2>
                            <p class="text-sm text-gray-500">Track interventions, follow-ups, and health outcomes.</p>
                        </div>
                        <button
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="openCreateTreatmentModal"
                            :disabled="treatmentsLoading || !cow"
                        >
                            <Icon icon="mdi:medical-bag" :size="18" />
                            Record treatment
                        </button>
                    </div>

                    <div v-if="treatmentsLoading" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                        Loading treatments...
                    </div>

                    <template v-else>
                        <div class="mt-6 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5 shadow-sm">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Total Treatments</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ treatmentsSummary.totalTreatments }}</p>
                                <p class="text-xs text-gray-500">All recorded interventions</p>
                            </div>
                            <div class="rounded-2xl border border-purple-100 bg-purple-50/70 p-5 shadow-sm">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Treatment Cost</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(treatmentsSummary.totalCost) }}</p>
                                <p class="text-xs text-gray-500">Cumulative spend</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-5 shadow-sm">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Upcoming Follow-ups</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ treatmentsSummary.upcomingFollowUps }}</p>
                                <p class="text-xs text-gray-500">From today onward</p>
                            </div>
                        </div>
                    </template>
                </div>

                <CowTreatmentsTimeline
                    v-if="!treatmentsLoading"
                    :treatments="treatments"
                    :format-date="formatDate"
                    :format-currency="formatCurrency"
                />

                <div
                    v-if="!treatmentsLoading"
                    class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100"
                >
                    <CowTreatmentsTable
                        :treatments="treatments"
                        :format-date="formatDate"
                        :format-currency="formatCurrency"
                    />
                </div>
            </div>
        </template>
    </div>

    <CreateCowTreatmentModal
        :is-open="showCreateTreatmentModal"
        :cow-id="cow?.id ?? null"
        @close="closeCreateTreatmentModal"
        @created="handleTreatmentCreated"
    />
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import { useCowStore } from '../../stores/cowStore';
import type { Cow, CowMilkProduction, CowTreatment } from '../../stores/geographyStore';
import CowMilkProductionChart from '../../components/cows/CowMilkProductionChart.vue';
import CowTreatmentsTimeline from '../../components/cows/CowTreatmentsTimeline.vue';
import CowTreatmentsTable from '../../components/cows/CowTreatmentsTable.vue';
import CreateCowTreatmentModal from '../../components/cows/CreateCowTreatmentModal.vue';

const route = useRoute();
const cowIdParam = route.params.id;
const cowId = Number(cowIdParam);

const cowStore = useCowStore();
const cow = ref<Cow | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);
const milkRecords = ref<CowMilkProduction[]>([]);
const milkLoading = ref(false);
const treatments = ref<CowTreatment[]>([]);
const treatmentsLoading = ref(false);
const showCreateTreatmentModal = ref(false);
const activeTab = ref<'overview' | 'milk' | 'treatments'>('overview');

const fetchCow = async () => {
    if (Number.isNaN(cowId)) {
        error.value = 'Invalid cow identifier.';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const result = await cowStore.fetchCow(cowId);
        cow.value = result;
        treatments.value = ((result as any)?.treatments ?? []) as CowTreatment[];
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load cow.';
        cow.value = null;
        treatments.value = [];
    } finally {
        loading.value = false;
    }
};

const fetchMilkRecords = async () => {
    if (!cow.value) return;
    milkLoading.value = true;
    try {
        const records = await cowStore.fetchCowMilkRecords(cow.value.id);
        milkRecords.value = (records ?? []).sort((a: any, b: any) => {
            const dateA = new Date(a.recorded_date ?? '').getTime();
            const dateB = new Date(b.recorded_date ?? '').getTime();
            return dateB - dateA;
        });
    } catch (err: any) {
        // swallow, table will show empty state
        milkRecords.value = [];
    } finally {
        milkLoading.value = false;
    }
};

const refresh = async () => {
    await fetchCow();
    await Promise.all([fetchMilkRecords(), fetchTreatments()]);
};

const refreshMilkRecords = async () => {
    await fetchMilkRecords();
};

const normalizeTreatment = (input: any): CowTreatment => {
    if (!input) {
        return input as CowTreatment;
    }

    const normalized: any = { ...input };

    if (normalized.recorded_by && !normalized.recordedBy) {
        normalized.recordedBy = normalized.recorded_by;
    }

    if (normalized.cow === undefined && normalized.cow_id && cow.value?.id === normalized.cow_id) {
        normalized.cow = cow.value;
    }

    return normalized as CowTreatment;
};

const fetchTreatments = async () => {
    if (!cow.value) return;
    treatmentsLoading.value = true;
    try {
        const records = await cowStore.fetchCowTreatments(cow.value.id);
        const normalized = (Array.isArray(records) ? records : []).map(normalizeTreatment);
        treatments.value = normalized;
    } catch {
        treatments.value = [];
    } finally {
        treatmentsLoading.value = false;
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

const formatLiters = (value?: number | string | null) => {
    if (value === null || value === undefined || value === '') return '—';
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '—';
    return numeric.toLocaleString(undefined, {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    });
};

const formatCurrency = (value?: number | string | null) => {
    if (value === null || value === undefined) return '—';
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '—';
    return numeric.toLocaleString(undefined, { style: 'currency', currency: 'UGX' });
};

const formatAge = (value?: string | null) => {
    if (!value) return '—';
    const birth = new Date(value);
    if (Number.isNaN(birth.getTime())) return '—';
    const diff = Date.now() - birth.getTime();
    const ageYears = diff / (1000 * 60 * 60 * 24 * 365.25);
    if (ageYears < 1) {
        const months = Math.max(1, Math.floor(ageYears * 12));
        return `${months} mo`;
    }
    return `${ageYears.toFixed(1)} yr`;
};

const formatFarmerName = (cow: Cow) => {
    const farmer = cow.farmer as any;
    if (!farmer) return 'Unassigned';
    return `${farmer.first_name ?? ''} ${farmer.last_name ?? ''}`.trim() || 'Unnamed farmer';
};

const lastRecordDate = computed(() => {
    if (!milkRecords.value.length) return null;
    return milkRecords.value[0]?.recorded_date ?? null;
});

const averageDailyVolume = computed(() => {
    if (!milkRecords.value.length) return 0;
    const total = milkRecords.value.reduce(
        (sum, record) => sum + Number(record.total_volume_liters ?? 0),
        0
    );
    return total / milkRecords.value.length;
});

const peakDailyVolume = computed(() => {
    if (!milkRecords.value.length) return 0;
    return milkRecords.value.reduce(
        (max, record) => Math.max(max, Number(record.total_volume_liters ?? 0)),
        0
    );
});

const treatmentsSummary = computed(() => {
    if (!treatments.value.length) {
        return {
            totalTreatments: 0,
            totalCost: 0,
            upcomingFollowUps: 0,
        };
    }

    const totalTreatments = treatments.value.length;
    const totalCost = treatments.value.reduce(
        (sum, record) => sum + Number(record.cost ?? 0),
        0
    );
    const upcomingFollowUps = treatments.value.filter((record) => {
        if (!record.follow_up_date) return false;
        return new Date(record.follow_up_date) >= new Date();
    }).length;

    return {
        totalTreatments,
        totalCost,
        upcomingFollowUps,
    };
});

onMounted(async () => {
    await fetchCow();
    await Promise.all([fetchMilkRecords(), fetchTreatments()]);
});

const openCreateTreatmentModal = () => {
    showCreateTreatmentModal.value = true;
};

const closeCreateTreatmentModal = () => {
    showCreateTreatmentModal.value = false;
};

const handleTreatmentCreated = async (treatment: CowTreatment) => {
    showCreateTreatmentModal.value = false;

    const normalized = normalizeTreatment(treatment);

    const existing = treatments.value.filter(record => record.id !== normalized.id);
    treatments.value = [normalized, ...existing].sort((a, b) => {
        const dateA = new Date(a.treatment_date ?? '').getTime();
        const dateB = new Date(b.treatment_date ?? '').getTime();
        return dateB - dateA;
    });

    try {
        await fetchTreatments();
    } catch {
        // no-op; state already optimistic
    }
};

const tabs = [
    { id: 'overview', label: 'Overview', icon: 'mdi:information-outline' },
    { id: 'milk', label: 'Milk Production', icon: 'mdi:bucket-outline' },
    { id: 'treatments', label: 'Treatments', icon: 'mdi:medical-bag' },
] as const;

const setActiveTab = (tabId: typeof tabs[number]['id']) => {
    activeTab.value = tabId;
};

const tabButtonClass = (tabId: typeof tabs[number]['id']) => {
    const isActive = activeTab.value === tabId;
    return [
        isActive
            ? 'bg-slate-900 text-white shadow-lg shadow-slate-400/40'
            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100',
    ];
};
</script>

