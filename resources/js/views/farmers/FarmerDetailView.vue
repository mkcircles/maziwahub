<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2">
                <router-link
                    to="/admin/farmers"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to Farmers
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ fullName }}
                    </h1>
                    <p class="text-xs text-gray-500">
                        Farmer ID:
                        <span class="font-semibold tracking-wide text-gray-700">
                            {{ farmer?.farmer_id ?? 'N/A' }}
                        </span>
                    </p>
                    
                    
                    <p class="text-xs text-gray-500">
                        Farmer Location:
                        <span class="font-semibold tracking-wide text-gray-700">
                            {{ formatFarmerLocation(farmer) }}
                        </span>
                    </p>
                                    </div>
            </div>
<div class="flex flex-wrap items-center gap-3">
    <span
                    class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                    :class="statusChipClass(farmer?.status)"
                >
                    <Icon :icon="statusChipIcon(farmer?.status)" :size="14" />
                    {{ farmer?.status ?? 'Unknown' }}
                </span>
                <span
                    v-if="farmer?.reg_type"
                    class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 uppercase"
                >
                    {{ farmer?.reg_type }}
                </span>
   
</div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white/60 p-1">
            <div class="flex flex-wrap gap-1">
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
        </div>

        <div v-if="detailLoading" class="rounded-lg bg-white p-6 shadow">
            <p class="text-gray-600">Loading farmer details...</p>
        </div>
        <div v-else-if="detailError" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            {{ detailError }}
        </div>

        <template v-else-if="farmer">
            
            <div v-if="activeTab === 'overview'" class="space-y-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <button
        type="button"
        class="inline-flex items-center btn-xs gap-2 rounded-lg text-xs text-blue-400 font-medium transition hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
        @click="openEditModal"
        :disabled="detailLoading || !farmer"
    >
        <Icon icon="mdi:pencil-outline" :size="16" />
        Edit Profile
    </button>   
            </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatisticalCard icon="mdi:account-outline" icon-class="text-blue-600">
                    <template #title>Household Members</template>
                    <template #default>{{ farmer.family_size ?? '—' }}</template>
                    <template #caption>Total family size</template>
                </StatisticalCard>

                <StatisticalCard icon="mdi:human-male-female-child" icon-class="text-emerald-600">
                    <template #title>Adults</template>
                    <template #default>{{ farmer.above_18 ?? 0 }}</template>
                    <template #caption>Household members above 18</template>
                </StatisticalCard>

                <StatisticalCard icon="mdi:baby-face-outline" icon-class="text-amber-500">
                    <template #title>Children</template>
                    <template #default>{{ farmer.below_5 ?? 0 }}</template>
                    <template #caption>Children under 5 years</template>
                </StatisticalCard>

                <StatisticalCard icon="mdi:nature" icon-class="text-lime-600">
                    <template #title>Farm Size</template>
                    <template #default>{{ farmer.farm_size ?? 0 }} acres</template>
                    <template #caption>Land registered by farmer</template>
                </StatisticalCard>
            </div>

                <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg bg-white p-6 shadow space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">First Name</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.first_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Last Name</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.last_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Gender</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.gender ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Date of Birth</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ formatDate(farmer.dob) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Phone Number</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.phone_number ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">National ID</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.id_number ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Marital Status</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.marital_status ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Education Level</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.educational_level ?? '—' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Household &amp; Next of Kin</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Household Head</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ farmer.household_head ? 'Yes' : 'No' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Registered By</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.registered_by ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Next of Kin</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.next_of_kin ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Next of Kin Contact</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.next_of_kin_contact ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Relationship</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.next_of_kin_relationship ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Financial Instrument</p>
                            <p class="text-sm font-medium text-gray-900">{{ farmer.financial_instrument ?? '—' }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <section class="rounded-lg bg-white p-6 shadow space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Milk Collection &amp; Location</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Milk Collection Center</h3>
                        <p class="mt-2 text-sm font-medium text-gray-900">
                            {{ farmer.milkCollectionCenter?.name ?? 'Not assigned' }}
                        </p>
                        <p v-if="farmer.milkCollectionCenter?.physical_address" class="text-xs text-gray-500">
                            {{ farmer.milkCollectionCenter?.physical_address }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Location Trail</h3>
                        <p class="mt-2 text-sm text-gray-900">{{ formatFarmerLocation(farmer) }}</p>
                    </div>
                    <div v-if="farmer.coordinates" class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Coordinates</h3>
                        <p class="mt-2 text-sm text-gray-900">
                            Lat: {{ farmer.coordinates?.latitude ?? '—' }}<br />
                            Lng: {{ farmer.coordinates?.longitude ?? '—' }}
                        </p>
                    </div>
                </div>
            </section>

            <section v-if="farmer.support_needed" class="rounded-lg bg-white p-6 shadow space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Support Needed</h2>
                <p class="text-sm text-gray-700">
                    {{ farmer.support_needed }}
                </p>
            </section>
            </div>

            <div v-else-if="activeTab === 'cows'" class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Registered Cows</h2>
                        <p class="text-sm text-gray-500">
                            Manage the herd associated with this farmer.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="openAddCowModal"
                        :disabled="detailLoading"
                    >
                        <Icon icon="mdi:plus" :size="18" />
                        Add Cow
                    </button>
                </div>
                <FarmerCowsTable
                    :cows="cows"
                    :format-date="formatDate"
                    :format-liters="formatLiters"
                />
            </div>

            <div v-else-if="activeTab === 'milk'" class="space-y-4">
                <FarmerMilkProductionTable
                    :records="milkProductionsSorted"
                    :format-date="formatDate"
                    :format-liters="formatLiters"
                    :get-cow-tag="getCowTag"
                />
            </div>
        </template>

        <EditFarmerModal
            :is-open="showEditModal"
            :farmer="farmer"
            :milk-centers="milkCenters"
            @close="handleModalClose"
            @updated="handleFarmerUpdated"
        />
        <AddCowModal
            :is-open="showAddCowModal"
            :farmer-id="farmer?.id ?? null"
            @close="handleCowModalClose"
            @created="handleCowCreated"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import EditFarmerModal from '../../components/farmers/EditFarmerModal.vue';
import FarmerCowsTable from '../../components/farmers/FarmerCowsTable.vue';
import FarmerMilkProductionTable from '../../components/farmers/FarmerMilkProductionTable.vue';
import AddCowModal from '../../components/farmers/AddCowModal.vue';
import { useFarmerStore } from '../../stores/farmerStore';
import { useGeographyStore } from '../../stores/geographyStore';
import type { Farmer, Cow, CowMilkProduction } from '../../stores/geographyStore';

const route = useRoute();
const farmerStore = useFarmerStore();
const geographyStore = useGeographyStore();
const { milkCenters } = storeToRefs(geographyStore);

const farmerId = computed(() => Number(route.params.id));

type TabKey = 'overview' | 'cows' | 'milk';

const farmer = computed(() => farmerStore.selectedFarmer as Farmer | null);
const detailLoading = computed(() => farmerStore.detailLoading);
const detailError = computed(() => farmerStore.detailError);
const showEditModal = ref(false);
const showAddCowModal = ref(false);
const activeTab = ref<TabKey>('overview');

const fullName = computed(() => {
    if (!farmer.value) return 'Farmer';
    return `${farmer.value.first_name} ${farmer.value.last_name}`.trim();
});

const cows = computed<Cow[]>(() => {
    const value = farmer.value as Farmer | null;
    const list =
        (value?.cows as Cow[] | undefined) ??
        ((value as any)?.cows ?? []);
    return Array.isArray(list) ? list : [];
});

const milkProductions = computed<CowMilkProduction[]>(() => {
    const value = farmer.value as any;
    const list =
        value?.cow_milk_productions ??
        value?.cowMilkProductions ??
        [];
    return Array.isArray(list) ? list : [];
});

const milkProductionsSorted = computed(() =>
    [...milkProductions.value].sort((a, b) => {
        const dateA = new Date(a.recorded_date ?? '').getTime();
        const dateB = new Date(b.recorded_date ?? '').getTime();
        return dateB - dateA;
    })
);

const cowLookup = computed(() => {
    const map = new Map<number, Cow>();
    cows.value.forEach((cow) => {
        map.set(cow.id, cow);
    });
    return map;
});

const tabs = computed<Array<{ id: TabKey; label: string }>>(() => [
    { id: 'overview', label: 'Overview' },
    { id: 'cows', label: `Cows (${cows.value.length})` },
    { id: 'milk', label: `Milk Production (${milkProductions.value.length})` },
]);

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    try {
        return new Date(value).toLocaleDateString();
    } catch {
        return value;
    }
};

const formatLiters = (value?: number | string | null) => {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const numberValue = Number(value);

    if (Number.isNaN(numberValue)) {
        return '—';
    }

    return numberValue.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const getCowTag = (cowId?: number | null) => {
    if (!cowId) return `Cow #${cowId ?? '—'}`;
    return cowLookup.value.get(cowId)?.tag_number ?? `Cow #${cowId}`;
};

const formatFarmerLocation = (farmer: Farmer) => {
    const location = (farmer as any)?.location ?? {};
    const segments = [
        location.country ?? farmer.district,
        location.region,
        location.district ?? farmer.district,
        location.county ?? farmer.county,
        location.sub_county ?? farmer.sub_county,
        location.parish ?? farmer.parish,
        location.village ?? farmer.village,
    ].filter((segment): segment is string => Boolean(segment));
    return segments.length ? segments.join(' > ') : 'Location details unavailable';
};

const statusChipClass = (status?: string | null) => {
    if (status === 'active') return 'bg-green-100 text-green-700';
    if (status === 'pending') return 'bg-yellow-100 text-yellow-700';
    if (status === 'inactive') return 'bg-gray-100 text-gray-600';
    return 'bg-gray-100 text-gray-600';
};

const statusChipIcon = (status?: string | null) => {
    if (status === 'active') return 'mdi:check-circle-outline';
    if (status === 'pending') return 'mdi:clock-outline';
    if (status === 'inactive') return 'mdi:alert-circle-outline';
    return 'mdi:help-circle-outline';
};

const ensureMilkCenters = async () => {
    if (!milkCenters.value.length) {
        try {
            await geographyStore.getMilkCollectionCenters();
        } catch {
            // error handled in store
        }
    }
};

const loadFarmer = async (id: number) => {
    if (Number.isNaN(id)) {
        farmerStore.detailError = 'Invalid farmer identifier.';
        return;
    }

    try {
        await farmerStore.fetchFarmer(id);
    } catch {
        // error already handled in store
    }
};

const openEditModal = async () => {
    await ensureMilkCenters();
    showEditModal.value = true;
};

const openAddCowModal = () => {
    showAddCowModal.value = true;
};

const handleCowModalClose = () => {
    showAddCowModal.value = false;
};

const selectTab = (tab: TabKey) => {
    activeTab.value = tab;
};

const handleModalClose = () => {
    showEditModal.value = false;
};

const handleFarmerUpdated = async () => {
    showEditModal.value = false;
    await loadFarmer(farmerId.value);
};

const handleCowCreated = async () => {
    await loadFarmer(farmerId.value);
};

onMounted(async () => {
    await Promise.all([loadFarmer(farmerId.value), ensureMilkCenters()]);
});

watch(
    farmerId,
    async (newId) => {
        await loadFarmer(newId);
    }
);
</script>

