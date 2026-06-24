<template>
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-2xl border border-surface-200/60 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 px-8 py-10 text-white shadow-xl shadow-slate-950/20">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(94,234,212,0.25),transparent_65%)] opacity-80"></div>
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-emerald-400">
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
            <StatisticalCard icon="mdi:account-group-outline" icon-class="text-primary-500" class="rounded-xl border border-surface-200/50 shadow-sm">
                <template #title>Total Farmers</template>
                <template #default>{{ metrics.total }}</template>
                <template #caption>Overall farmers linked to your centers</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:check-circle-outline" icon-class="text-emerald-500" class="rounded-xl border border-surface-200/50 shadow-sm">
                <template #title>Active</template>
                <template #default>{{ metrics.active }}</template>
                <template #caption>In good standing</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:clock-outline" icon-class="text-amber-500" class="rounded-xl border border-surface-200/50 shadow-sm">
                <template #title>Pending</template>
                <template #default>{{ metrics.pending }}</template>
                <template #caption>Awaiting verification</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:shield-check-outline" icon-class="text-primary-600" class="rounded-xl border border-surface-200/50 shadow-sm">
                <template #title>Insured</template>
                <template #default>{{ metrics.insured }}</template>
                <template #caption>Farmers with insurance cover</template>
            </StatisticalCard>
        </section>

        <section class="rounded-2xl border border-surface-200 bg-white/95 p-8 shadow-sm backdrop-blur-sm">
            <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="applyFilters">
                <div class="xl:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">
                        Search
                    </label>
                    <div class="mt-1 flex items-center gap-2 rounded-lg border border-surface-200 bg-surface-50/50 px-3 py-2 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-200 transition-all">
                        <Icon icon="mdi:magnify" :size="18" class="text-surface-400" />
                        <input
                            v-model.trim="filters.search"
                            type="text"
                            placeholder="Search by name, farmer ID, or phone"
                            class="w-full bg-transparent text-sm text-surface-800 placeholder:text-surface-400 focus:outline-none"
                        />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">
                        Milk collection center
                    </label>
                    <select
                        v-model.number="filters.centerId"
                        class="mt-1 w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
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
                    <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">
                        Status
                    </label>
                    <select
                        v-model="filters.status"
                        class="mt-1 w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">
                        Rows per page
                    </label>
                    <select
                        v-model.number="perPage"
                        class="mt-1 w-full rounded-lg border border-surface-200 px-3 py-2 text-sm text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option v-for="option in perPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700"
                    >
                        Apply filters
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-surface-200 px-4 py-2.5 text-sm font-medium text-surface-600 transition hover:bg-surface-100 hover:text-surface-800"
                        @click="resetFilters"
                    >
                        Reset
                    </button>
                </div>
            </form>

            <div
                v-if="errorMessage"
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50/70 px-4 py-3 text-sm text-rose-700"
            >
                {{ errorMessage }}
            </div>

            <div v-if="farmers.length" class="mt-6 overflow-hidden rounded-xl border border-surface-200/70 shadow-sm">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-surface-50/70 border-b border-surface-200 text-xs uppercase tracking-wide text-surface-500">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-semibold">Farmer</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Contact</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Milk Collection Center</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Location</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Status</th>
                            <th class="px-6 py-3.5 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs text-surface-700">
                        <tr v-for="farmer in farmers" :key="farmer.id" class="transition hover:bg-surface-50/60 duration-150">
                            <td class="px-6 py-4">
                                <div class="text-xs font-semibold text-surface-900">
                                    <router-link :to="`/partner/farmers/${farmer.id}`"
                                        class="hover:text-primary-600 hover:underline">
                                        {{ farmer.first_name }} {{ farmer.last_name }}
                                    </router-link>
                                </div>
                                <div class="text-xs text-surface-400 uppercase tracking-wide">
                                    ID: {{ farmer.farmer_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-surface-600">
                                <div>
                                    <Icon icon="mdi:phone" :size="10" class="inline-block text-emerald-500" /> 
                                    <span class="text-[12px] text-surface-400 ml-1"> {{ farmer.phone_number ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-surface-600">
                                <div class="font-medium text-surface-800">{{ farmer.milkCollectionCenter?.name ?? 'Not assigned' }}</div>
                                <div v-if="farmer.milkCollectionCenter?.physical_address" class="text-xs text-surface-400 mt-0.5">
                                    {{ farmer.milkCollectionCenter.physical_address }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[12px] text-surface-500">
                                <div>{{ formatFarmerLocation(farmer) }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                    :class="statusChipClass(farmer.status)">
                                    <Icon :icon="statusChipIcon(farmer.status)" :size="14" />
                                    {{ farmer.status ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1">
                                    <button v-if="farmer.status === 'pending' || farmer.status === 'inactive'"
                                        class="inline-flex items-center justify-center rounded-full p-2 text-surface-400 hover:bg-surface-100 hover:text-emerald-600 transition-colors"
                                        title="Activate" @click="toggleFarmerStatus(farmer, 'active')">
                                        <Icon icon="mdi:check-circle-outline" :size="18" />
                                    </button>
                                    <button v-if="farmer.status === 'active'"
                                        class="inline-flex items-center justify-center rounded-full p-2 text-surface-400 hover:bg-surface-100 hover:text-red-600 transition-colors"
                                        title="Deactivate" @click="toggleFarmerStatus(farmer, 'inactive')">
                                        <Icon icon="mdi:pause-circle-outline" :size="18" />
                                    </button>
                                    <button
                                        class="inline-flex items-center justify-center rounded-full p-2 text-surface-400 hover:bg-surface-100 hover:text-primary-600 transition-colors"
                                        title="Edit" @click="openEditModal(farmer)">
                                        <Icon icon="mdi:pencil-outline" :size="18" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else-if="loadingFarmers"
                class="mt-6 rounded-lg border border-dashed border-surface-200 bg-surface-50/80 p-12 text-center text-sm text-surface-500"
            >
                Loading farmers…
            </div>
            <div
                v-else
                class="mt-6 rounded-lg border border-dashed border-surface-200 bg-surface-50/80 p-12 text-center text-sm text-surface-500"
            >
                No farmers found for the selected filters.
            </div>

            <div class="mt-6 flex flex-col gap-4 border-t border-surface-200 pt-6 md:flex-row md:items-center md:justify-between">
                <p class="text-sm text-surface-500">
                    Showing
                    <span class="font-semibold text-surface-700">
                        {{ farmers.length }}
                    </span>
                    of
                    <span class="font-semibold text-surface-700">
                        {{ pagination.total }}
                    </span>
                    farmers
                </p>
                <div class="flex items-center gap-2">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-medium text-surface-600 transition hover:bg-surface-100 hover:text-surface-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="pagination.current_page <= 1"
                        @click="goToPage(pagination.current_page - 1)"
                    >
                        <Icon icon="mdi:chevron-left" :size="18" />
                        Previous
                    </button>
                    <span class="text-xs text-surface-500">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </span>
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-medium text-surface-600 transition hover:bg-surface-100 hover:text-surface-800 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="pagination.current_page >= pagination.last_page"
                        @click="goToPage(pagination.current_page + 1)"
                    >
                        Next
                        <Icon icon="mdi:chevron-right" :size="18" />
                    </button>
                </div>
            </div>
        </section>
        <EditFarmerModal :is-open="showEditModal" :farmer="selectedFarmerForEdit" :milk-centers="centers"
            @close="closeEditModal" @updated="handleFarmerUpdated" />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import EditFarmerModal from '../../components/farmers/EditFarmerModal.vue';
import { useAuthStore } from '../../stores/authStore';
import { usePartnerStore } from '../../stores/partnerStore';
import { useFarmerStore } from '../../stores/farmerStore';
import type { Farmer } from '../../stores/geographyStore';

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

const showEditModal = ref(false);
const selectedFarmerForEdit = ref<Farmer | null>(null);

const openEditModal = (farmer: Farmer) => {
    selectedFarmerForEdit.value = farmer;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    selectedFarmerForEdit.value = null;
};

const handleFarmerUpdated = async () => {
    showEditModal.value = false;
    selectedFarmerForEdit.value = null;
    await loadFarmers(pagination.value.current_page);
    await fetchMetrics();
};

const toggleFarmerStatus = async (farmer: Farmer, newStatus: 'active' | 'inactive') => {
    const label = newStatus === 'active' ? 'activate' : 'deactivate';
    const confirmed = window.confirm(
        `Are you sure you want to ${label} ${farmer.first_name} ${farmer.last_name}?`
    );
    if (!confirmed) return;

    try {
        await farmerStore.updateFarmer(farmer.id, { ...farmer, status: newStatus });
    } catch (err: any) {
        window.alert(`Failed to update farmer status: ${err.message || err}`);
    }
};

const formatFarmerLocation = (farmer: Farmer) => {
    const location = (farmer as any)?.location ?? {};
    const segments = [
        location.country,
        location.region,
        location.district ?? farmer.district,
        // location.county ?? farmer.county,
        // location.sub_county ?? farmer.sub_county,
        // location.parish ?? farmer.parish,
        // location.village ?? farmer.village,
    ].filter((segment): segment is string => Boolean(segment));
    return segments.length ? segments.join(' > ') : 'Location details unavailable';
};

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
            return 'bg-surface-100 text-surface-600 border border-surface-200';
        default:
            return 'bg-surface-100 text-surface-600 border border-surface-200';
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

const statusChipClass = (status?: string | null) => {
    if (status === 'active') {
        return 'bg-green-100 text-green-700';
    }
    if (status === 'pending') {
        return 'bg-yellow-100 text-yellow-700';
    }
    if (status === 'inactive') {
        return 'bg-surface-100 text-surface-600';
    }
    return 'bg-surface-100 text-surface-600';
};

const statusChipIcon = (status?: string | null) => {
    if (status === 'active') {
        return 'mdi:check-circle-outline';
    }
    if (status === 'pending') {
        return 'mdi:clock-outline';
    }
    if (status === 'inactive') {
        return 'mdi:alert-circle-outline';
    }
    return 'mdi:help-circle-outline';
};
</script>



