<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-xl bg-[#0F172A] px-3 py-5 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Herd Overview
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Cows Registry</h1>
                    <p class="text-sm text-white/70">
                        Monitor herd performance, track health status, and identify productivity insights across all
                        registered cows.
                    </p>
                </div>
                <button
                    class="inline-flex items-center gap-2 self-start rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25 disabled:cursor-not-allowed disabled:opacity-60"
                    @click="refresh"
                    :disabled="loading"
                >
                    <Icon icon="mdi:refresh" :size="18" />
                    Refresh data
                </button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard
                icon="mdi:cow"
                icon-class="text-emerald-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Total Cows</template>
                <template #default>{{ loading ? '•••' : filteredCows.length }}</template>
                <template #caption>Registered cows across all farmers</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:bucket-outline"
                icon-class="text-sky-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Average Capacity</template>
                <template #default>{{ loading ? '•••' : formatLiters(averageCapacity) }}</template>
                <template #caption>Average daily milk capacity</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:account-outline"
                icon-class="text-purple-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Unique Farmers</template>
                <template #default>{{ loading ? '•••' : uniqueFarmers }}</template>
                <template #caption>Farmers owning registered cows</template>
            </StatisticalCard>
            <StatisticalCard
                icon="mdi:leaf"
                icon-class="text-emerald-500"
                class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
            >
                <template #title>Lactating Cows</template>
                <template #default>{{ loading ? '•••' : lactatingCows }}</template>
                <template #caption>Producing milk currently</template>
            </StatisticalCard>
        </div>

        <div class="space-y-6 rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-lg shadow-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Filters</h2>
                    <p class="text-sm text-slate-500">Refine the herd list by breed, farmer, or tag number.</p>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                    @click="clearFilters"
                    :disabled="loading"
                >
                    <Icon icon="mdi:filter-remove-outline" :size="18" />
                    Reset filters
                </button>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700" for="cow-search">Search</label>
                    <div class="relative mt-1">
                        <input
                            id="cow-search"
                            v-model="searchTerm"
                            type="search"
                            placeholder="Search by tag number or breed"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-2 text-sm text-slate-700 shadow-inner focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                        />
                        <Icon
                            icon="mdi:magnify"
                            :size="18"
                            class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"
                        />
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700" for="cow-breed">Breed</label>
                    <select
                        id="cow-breed"
                        v-model="breedFilter"
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                        <option value="">All breeds</option>
                        <option v-for="breed in breedOptions" :key="breed" :value="breed">
                            {{ breed }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700" for="cow-farmer-id">Farmer ID</label>
                    <input
                        id="cow-farmer-id"
                        v-model="farmerIdInput"
                        type="text"
                        placeholder="Filter by farmer ID"
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200"
                    />
                </div>
            </div>
            <p v-if="error" class="text-sm font-medium text-red-600">
                {{ error }}
            </p>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-lg shadow-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-3">Tag Number</th>
                            <th class="px-6 py-3">Breed</th>
                            <th class="px-6 py-3">Farmer</th>
                            <th class="px-6 py-3">Milk Capacity (L)</th>
                            <th class="px-6 py-3">Age</th>
                            <th class="px-6 py-3">Health</th>
                            <th class="px-6 py-3">Last Recorded</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <tr v-if="loading" class="bg-white">
                            <td colspan="8" class="px-6 py-6 text-center text-sm text-slate-500">
                                Loading cows...
                            </td>
                        </tr>
                        <tr v-else-if="filteredCows.length === 0" class="bg-white">
                            <td colspan="8" class="px-6 py-6 text-center text-sm text-slate-500">
                                No cows match the selected filters.
                            </td>
                        </tr>
                        <tr v-for="cow in filteredCows" :key="cow.id" class="transition hover:bg-slate-50/60">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <router-link :to="`/admin/cows/${cow.id}`" class="hover:text-blue-600 hover:underline">
                                    {{ cow.tag_number }}
                                </router-link>
                            </td>
                            <td class="px-6 py-4">
                                {{ cow.breed ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">
                                    <router-link
                                        v-if="cow.farmer?.id"
                                        :to="`/admin/farmers/${cow.farmer.id}`"
                                        class="hover:text-blue-600 hover:underline"
                                    >
                                        {{ formatFarmerName(cow) }}
                                    </router-link>
                                    <span v-else>Unassigned</span>
                                </div>
                                <div v-if="cow.farmer?.farmer_id" class="text-xs uppercase tracking-wide text-slate-400">
                                    ID: {{ cow.farmer.farmer_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ formatLiters(cow.milk_capacity_liters) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formatAge(cow.date_of_birth) }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                    :class="healthChipClass(cow.health_status)"
                                >
                                    <Icon :icon="healthChipIcon(cow.health_status)" :size="14" />
                                    {{ cow.health_status ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ formatDate(lastProductionDate(cow)) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <router-link
                                    :to="`/admin/cows/${cow.id}`"
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-600 transition hover:bg-blue-100 hover:text-blue-700"
                                >
                                    View
                                    <Icon icon="mdi:chevron-right" :size="16" />
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import { useCowStore } from '../../stores/cowStore';
import type { Cow } from '../../stores/geographyStore';

const cowStore = useCowStore();
const { cows, loading, error, filters } = storeToRefs(cowStore);

const searchTerm = ref(filters.value.search);
const breedFilter = ref('');
const farmerIdInput = ref(filters.value.farmer_id ? String(filters.value.farmer_id) : '');

const filteredCows = computed<Cow[]>(() => {
    const breed = breedFilter.value.trim().toLocaleLowerCase();
    return cows.value.filter((cow) => {
        if (breed && (cow.breed ?? '').toLocaleLowerCase() !== breed) {
            return false;
        }
        return true;
    });
});

const breedOptions = computed(() => {
    const breeds = new Set<string>();
    cows.value.forEach((cow) => {
        if (cow.breed) {
            breeds.add(cow.breed);
        }
    });
    return Array.from(breeds).sort((a, b) => a.localeCompare(b));
});

const lactatingCows = computed(() =>
    filteredCows.value.filter((cow) => Number(cow.milk_capacity_liters ?? 0) > 0).length
);

const uniqueFarmers = computed(() => {
    const ids = new Set<number>();
    filteredCows.value.forEach((cow) => {
        if (cow.farmer?.id) {
            ids.add(cow.farmer.id);
        }
    });
    return ids.size;
});

const averageCapacity = computed(() => {
    if (!filteredCows.value.length) return 0;
    const total = filteredCows.value.reduce(
        (sum, cow) => sum + Number(cow.milk_capacity_liters ?? 0),
        0
    );
    return total / filteredCows.value.length;
});

const formatLiters = (value?: number | string | null) => {
    if (value === null || value === undefined || value === '') return '—';
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '—';
    return numeric.toLocaleString(undefined, {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    });
};

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    try {
        return new Date(value).toLocaleDateString();
    } catch {
        return value;
    }
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

const lastProductionDate = (cow: Cow) => {
    const records = (cow.milkProductions ?? cow.milk_productions ?? []) as Array<any>;
    if (!records.length) return null;
    const sorted = [...records].sort((a, b) => {
        const dateA = new Date(a.recorded_date ?? '').getTime();
        const dateB = new Date(b.recorded_date ?? '').getTime();
        return dateB - dateA;
    });
    return sorted[0]?.recorded_date ?? null;
};

const healthChipClass = (status?: string | null) => {
    if (!status) return 'bg-gray-100 text-gray-600';
    const normalized = status.toLowerCase();
    if (normalized.includes('healthy')) return 'bg-emerald-100 text-emerald-700';
    if (normalized.includes('treatment')) return 'bg-amber-100 text-amber-700';
    if (normalized.includes('sick')) return 'bg-red-100 text-red-700';
    return 'bg-gray-100 text-gray-600';
};

const healthChipIcon = (status?: string | null) => {
    if (!status) return 'mdi:emoticon-neutral-outline';
    const normalized = status.toLowerCase();
    if (normalized.includes('healthy')) return 'mdi:check-circle-outline';
    if (normalized.includes('treatment')) return 'mdi:medical-bag';
    if (normalized.includes('sick')) return 'mdi:alert-circle-outline';
    return 'mdi:emoticon-neutral-outline';
};

let searchDebounce: ReturnType<typeof setTimeout> | null = null;
watch(searchTerm, (value) => {
    if (searchDebounce) clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
        cowStore.fetchCows({ search: value.trim() });
    }, 250);
});

watch(farmerIdInput, (value) => {
    const normalized = value.trim();
    const parsed = normalized ? Number(normalized) : null;
    cowStore.fetchCows({ farmer_id: parsed && !Number.isNaN(parsed) ? parsed : null });
});

const clearFilters = () => {
    searchTerm.value = '';
    breedFilter.value = '';
    farmerIdInput.value = '';
    cowStore.resetFilters();
};

const refresh = async () => {
    await cowStore.fetchCows();
};

onMounted(async () => {
    await cowStore.fetchCows();
});
</script>

