<template>
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 px-8 py-10 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(94,234,212,0.25),transparent_65%)] opacity-80"></div>
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Farmer Registry
                    </p>
                    <h2 class="text-3xl font-semibold tracking-tight lg:text-4xl">
                        Farmers Connected to Your Centers
                    </h2>
                    <p class="text-sm text-white/70">
                        View farmer profiles, monitor herd and production trends, and coordinate support across your
                        partner network.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <router-link
                        to="/partner/milk-centers"
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20"
                    >
                        <Icon icon="mdi:storefront-outline" :size="18" />
                        Back to centers
                    </router-link>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard icon="mdi:account-group-outline" icon-class="text-indigo-500">
                <template #title>Total Farmers</template>
                <template #default>{{ metrics.total }}</template>
                <template #caption>Overall farmers linked to your centers</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:check-circle-outline" icon-class="text-emerald-500">
                <template #title>Active</template>
                <template #default>{{ metrics.active }}</template>
                <template #caption>In good standing</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:clock-outline" icon-class="text-amber-500">
                <template #title>Pending</template>
                <template #default>{{ metrics.pending }}</template>
                <template #caption>Awaiting verification</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:shield-check-outline" icon-class="text-sky-500">
                <template #title>Insured</template>
                <template #default>{{ metrics.insured }}</template>
                <template #caption>Farmers with insurance cover</template>
            </StatisticalCard>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="applyFilters">
                <div class="xl:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Search
                    </label>
                    <div class="mt-1 flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2">
                        <Icon icon="mdi:magnify" :size="18" class="text-slate-400" />
                        <input
                            v-model.trim="filters.search"
                            type="text"
                            placeholder="Search by name, farmer ID, or phone"
                            class="w-full bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none"
                        />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Milk collection center
                    </label>
                    <select
                        v-model.number="filters.centerId"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                    >
                        <option :value="0">All centers</option>
                        <option
                            v-for="center in centers"
                            :key="center.id"
                            :value="center.id"
                        >
                            {{ center.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Status
                    </label>
                    <select
                        v-model="filters.status"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                    >
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Rows per page
                    </label>
                    <select
                        v-model.number="perPage"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                    >
                        <option v-for="option in perPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
                    >
                        Apply filters
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                        @click="resetFilters"
                    >
                        Reset
                    </button>
                </div>
            </form>

            <div
                v-if="errorMessage"
                class="mt-4 rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-3 text-sm text-rose-700"
            >
                {{ errorMessage }}
            </div>

            <div v-if="farmers.length" class="mt-6 overflow-hidden rounded-2xl border border-slate-200/70">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr class="text-left">
                            <th class="px-6 py-3 font-semibold">Farmer</th>
                            <th class="px-6 py-3 font-semibold">Contact</th>
                            <th class="px-6 py-3 font-semibold">Center</th>
                            <th class="px-6 py-3 font-semibold">Herd Size</th>
                            <th class="px-6 py-3 font-semibold">Grazing</th>
                            <th class="px-6 py-3 font-semibold text-right">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="farmer in farmers" :key="farmer.id" class="hover:bg-slate-50/60">
                            <td class="px-6 py-4 align-top">
                                <div class="font-semibold text-slate-900">
                                    {{ farmer.first_name }} {{ farmer.last_name }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ farmer.farmer_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-slate-600">
                                <div class="flex items-center gap-2">
                                    <Icon icon="mdi:phone-outline" :size="16" class="text-slate-400" />
                                    <span>{{ farmer.phone_number || '—' }}</span>
                                </div>
                                <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                    <Icon icon="mdi:map-marker-outline" :size="16" />
                                    <span>
                                        {{ farmer.village || farmer.parish || farmer.sub_county || 'Location unknown' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-slate-600">
                                <div class="font-medium text-slate-800">
                                    {{ farmer.milkCollectionCenter?.name || 'Not assigned' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-400">
                                    {{ farmer.milkCollectionCenter?.physical_address || '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-slate-600">
                                {{ farmer.herd_size || '—' }}
                            </td>
                            <td class="px-6 py-4 align-top text-slate-600">
                                {{ formatGrazing(farmer.grazing_type) }}
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                    :class="statusClass(farmer.status)"
                                >
                                    <span class="inline-block h-2 w-2 rounded-full" :class="statusDotClass(farmer.status)"></span>
                                    {{ farmer.status || 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <router-link
                                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                                    :to="`/admin/farmers/${farmer.id}`"
                                >
                                    View profile
                                    <Icon icon="mdi:arrow-right" :size="16" />
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else-if="loadingFarmers"
                class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-12 text-center text-sm text-slate-500"
            >
                Loading farmers…
            </div>
            <div
                v-else
                class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-12 text-center text-sm text-slate-500"
            >
                No farmers found for the selected filters.
            </div>

            <div class="mt-6 flex flex-col gap-4 border-t border-slate-200 pt-6 md:flex-row md:items-center md:justify-between">
                <p class="text-sm text-slate-500">
                    Showing
                    <span class="font-semibold text-slate-700">
                        {{ farmers.length }}
                    </span>
                    of
                    <span class="font-semibold text-slate-700">
                        {{ pagination.total }}
                    </span>
                    farmers
                </p>
                <div class="flex items-center gap-2">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="pagination.current_page <= 1"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        <Icon icon="mdi:chevron-left" :size="18" />
                        Previous
                    </button>
                    <span class="text-xs text-slate-500">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </span>
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="pagination.current_page >= pagination.last_page"
                        @click="goToPage(pagination.current_page + 1)"
                    >
                        Next
                        <Icon icon="mdi:chevron-right" :size="18" />
                    </button>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import { useAuthStore } from '../../stores/authStore';
import { usePartnerStore } from '../../stores/partnerStore';
import { useFarmerStore } from '../../stores/farmerStore';

const authStore = useAuthStore();
const partnerStore = usePartnerStore();
const farmerStore = useFarmerStore();
const route = useRoute();

const filters = reactive({
    search: '',
    status: '',
    centerId: 0,
});

const perPageOptions = [10, 25, 50, 100];
const perPage = ref(25);
const errorMessage = ref<string | null>(null);
const initializing = ref(false);

const partnerId = computed(() => partnerStore.activePartner?.id ?? authStore.user?.partner_id ?? null);
const activePartner = computed(() => partnerStore.activePartner);
const centers = computed(() => activePartner.value?.milk_collection_centers ?? []);

const farmers = computed(() => farmerStore.farmers);
const pagination = computed(() => farmerStore.pagination);
const loadingFarmers = computed(() => farmerStore.loading);

const metrics = reactive({
    total: 0,
    active: 0,
    pending: 0,
    insured: 0,
});

const fetchMetrics = async () => {
    if (!partnerId.value) return;
    try {
        const [total, active, pending, insured] = await Promise.all([
            farmerStore.getCount({ partner_id: partnerId.value }),
            farmerStore.getCount({ status: 'active', partner_id: partnerId.value }),
            farmerStore.getCount({ status: 'pending', partner_id: partnerId.value }),
            farmerStore.getCount({ is_farmer_insured: true, partner_id: partnerId.value }),
        ]);
        Object.assign(metrics, { total, active, pending, insured });
    } catch (error: any) {
        console.error(error);
    }
};

const loadFarmers = async (page = 1) => {
    if (!partnerId.value) return;
    errorMessage.value = null;
    try {
        await farmerStore.fetchFarmers({
            search: filters.search || undefined,
            status: filters.status || undefined,
            milk_collection_center_id: filters.centerId ? filters.centerId : undefined,
            partner_id: partnerId.value,
            per_page: perPage.value,
            page,
        });
    } catch (error: any) {
        errorMessage.value = error?.response?.data?.message || 'Failed to load farmers.';
    }
};

const goToPage = (page: number) => {
    if (page < 1 || page > pagination.value.last_page) return;
    loadFarmers(page);
};

const applyFilters = () => {
    loadFarmers(1);
};

const resetFilters = () => {
    filters.search = '';
    filters.status = '';
    filters.centerId = 0;
    perPage.value = 25;
    loadFarmers(1);
};

const ensurePartnerLoaded = async (id: number) => {
    if (!activePartner.value || activePartner.value.id !== id) {
        await partnerStore.fetchPartner(id);
    }
};

const initialize = async (id: number) => {
    initializing.value = true;
    try {
        await ensurePartnerLoaded(id);
        const centerFromQuery = Number(route.query.center);
        if (Number.isFinite(centerFromQuery) && centerFromQuery > 0) {
            filters.centerId = centerFromQuery;
        }
        await Promise.all([loadFarmers(1), fetchMetrics()]);
    } finally {
        initializing.value = false;
    }
};

watch(
    partnerId,
    id => {
        if (!id) return;
        initialize(id);
    },
    { immediate: true },
);

watch(
    () => route.query.center,
    value => {
        const centerId = Number(value);
        if (!Number.isFinite(centerId) || centerId <= 0) return;
        filters.centerId = centerId;
        loadFarmers(1);
    },
);

watch(
    () => perPage.value,
    () => {
        loadFarmers(1);
    },
);

const formatGrazing = (value?: string | null) => {
    if (!value) return '—';
    return value
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const statusClass = (status?: string | null) => {
    switch ((status || '').toLowerCase()) {
        case 'active':
            return 'bg-emerald-50 text-emerald-600 border border-emerald-200';
        case 'pending':
            return 'bg-amber-50 text-amber-600 border border-amber-200';
        case 'inactive':
            return 'bg-slate-100 text-slate-600 border border-slate-200';
        default:
            return 'bg-slate-100 text-slate-600 border border-slate-200';
    }
};

const statusDotClass = (status?: string | null) => {
    switch ((status || '').toLowerCase()) {
        case 'active':
            return 'bg-emerald-500';
        case 'pending':
            return 'bg-amber-500';
        case 'inactive':
            return 'bg-slate-400';
        default:
            return 'bg-slate-400';
    }
};
</script>



