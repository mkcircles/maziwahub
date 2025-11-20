<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-3xl bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Geography Management
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Countries</h1>
                    <p class="text-sm text-white/70">
                        Manage registered countries, monitor their regional hierarchy, and track milk infrastructure and farmer coverage.
                    </p>
                </div>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 self-start rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                >
                    <Icon icon="mdi:plus" :size="20" />
                    Add Country
                </button>
            </div>
        </div>

        <AddCountryModal
            :is-open="showAddModal"
            @close="showAddModal = false"
            @created="handleCountryCreated"
        />

        <div v-if="countriesStore.loading" class="rounded-3xl border border-slate-200 bg-white p-8 text-slate-600 shadow-lg shadow-slate-100">
            Loading countries…
        </div>

        <div v-else-if="countriesStore.error" class="rounded-3xl border border-red-200 bg-red-50/80 p-6 text-red-700 shadow-lg shadow-red-100">
            {{ countriesStore.error }}
        </div>

        <div v-else class="space-y-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Countries</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ countries.length }}</p>
                    <p class="text-xs text-slate-500">Total registered</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Regions</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ totalRegionsCount }}</p>
                    <p class="text-xs text-slate-500">Across all countries</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Milk Centers</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ totalMilkCentersCount }}</p>
                    <p class="text-xs text-slate-500">Supporting dairy infrastructure</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Farmers</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ totalFarmersCount }}</p>
                    <p class="text-xs text-slate-500">Registered across these countries</p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Average Farmers / Country</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ averageFarmersPerCountry }}</p>
                    <p class="text-xs text-slate-500">Using the full dataset</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Average Milk Centers / Country</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ averageCentersPerCountry }}</p>
                    <p class="text-xs text-slate-500">Infrastructure coverage</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur space-y-2">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <Icon icon="mdi:trophy-outline" :size="16" />
                        Top Performers
                    </div>
                    <p class="text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ topCountryByFarmers?.name ?? '—' }}</span>
                        has the most farmers.
                    </p>
                        <p class="text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ topCountryByMilkCenters?.name ?? '—' }}</span>
                        leads on milk centers.
                    </p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-100 bg-white/95 p-8 shadow-lg shadow-slate-100 backdrop-blur space-y-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Filters</h2>
                        <p class="text-sm text-slate-500">
                            Narrow down the list by ISO code, search, or focus on active countries.
                        </p>
                    </div>
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:opacity-50"
                        @click="resetFilters"
                        :disabled="!searchTerm && !isoFilter && sortOption === 'name-asc' && !onlyWithActivity"
                    >
                        <Icon icon="mdi:filter-remove-outline" :size="18" />
                        Reset filters
                    </button>
                </div>

                <div class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="country-search">
                            Search
                        </label>
                        <div class="relative mt-2">
                            <input
                                id="country-search"
                                v-model="searchTerm"
                                type="search"
                                placeholder="Search by country name or ISO code"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                            />
                            <Icon
                                icon="mdi:magnify"
                                :size="18"
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="country-iso-filter">
                            ISO Code
                        </label>
                        <select
                            id="country-iso-filter"
                            v-model="isoFilter"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        >
                            <option value="">All codes</option>
                            <option v-for="code in isoCodeOptions" :key="code" :value="code">
                                {{ code }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500" for="country-sort">
                            Sort by
                        </label>
                        <select
                            id="country-sort"
                            v-model="sortOption"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        >
                            <option value="name-asc">Name (A → Z)</option>
                            <option value="name-desc">Name (Z → A)</option>
                            <option value="farmers-desc">Farmers (High → Low)</option>
                            <option value="centers-desc">Milk centers (High → Low)</option>
                            <option value="regions-desc">Regions (High → Low)</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600">
                    <label class="inline-flex items-center gap-2">
                        <input
                            v-model="onlyWithActivity"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        />
                        Show only countries with activity
                    </label>
                    <span class="inline-flex items-center gap-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                        <Icon icon="mdi:information-outline" :size="14" />
                        {{ filteredTotals.totalCountries }} match{{ filteredTotals.totalCountries === 1 ? '' : 'es' }}
                    </span>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur space-y-2">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <Icon icon="mdi:chart-bar" :size="16" />
                        Filtered Overview
                    </div>
                    <p class="text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ filteredTotals.totalCountries }}</span>
                        countr{{ filteredTotals.totalCountries === 1 ? 'y' : 'ies' }} currently in view.
                    </p>
                    <p class="text-xs text-slate-400">
                        Total farmers: {{ filteredTotals.totalFarmers.toLocaleString() }} • Milk centers: {{ filteredTotals.totalCenters.toLocaleString() }}
                    </p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Average Farmers (Filtered)</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ filteredTotals.averageFarmers }}</p>
                    <p class="text-xs text-slate-500">Per country in the current results</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Focus Country</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">
                        {{ viewTopCountry?.name ?? 'No data available' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        Leads filtered list with {{ numeric(viewTopCountry?.farmers_count ?? 0) }} farmers.
                    </p>
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-lg shadow-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Country
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    ISO Code
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Regions
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Milk Centers
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Farmers
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                            <tr v-if="displayedCountries.length === 0">
                                <td colspan="6" class="px-6 py-6 text-center text-sm text-slate-500">
                                    <span v-if="countries.length === 0">No countries found.</span>
                                    <span v-else>No countries match the selected filters.</span>
                                </td>
                            </tr>
                            <tr
                                v-for="country in displayedCountries"
                                :key="country.id"
                                class="transition hover:bg-slate-50/60"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-slate-900">{{ country.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        {{ country.iso_code }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ country.regions_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ country.milk_centers_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ country.farmers_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <router-link
                                        :to="`/admin/countries/${country.id}`"
                                        class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-1 text-sm font-medium text-sky-600 transition hover:bg-sky-100 hover:text-sky-700"
                                    >
                                        <Icon icon="mdi:eye" :size="16" />
                                        View
                                    </router-link>
                                    <button
                                        @click="handleDeleteCountry(country.id)"
                                        class="ml-3 inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-600 transition hover:bg-red-100 hover:text-red-700"
                                    >
                                        <Icon icon="mdi:delete" :size="16" />
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useCountriesStore, type Country as StoreCountry } from '../../stores/geographyStore';
import AddCountryModal from '../../components/geography/AddCountryModal.vue';
import Icon from '../../components/shared/Icon.vue';

type CountryWithMetrics = StoreCountry & {
    regions_count?: number | null;
    milk_centers_count?: number | null;
    farmers_count?: number | null;
};

const countriesStore = useCountriesStore();
const showAddModal = ref(false);

const searchTerm = ref('');
const isoFilter = ref('');
const sortOption = ref<'name-asc' | 'name-desc' | 'farmers-desc' | 'centers-desc' | 'regions-desc'>('name-asc');
const onlyWithActivity = ref(false);

onMounted(() => {
    countriesStore.getCountries();
});

const countries = computed<CountryWithMetrics[]>(() =>
    countriesStore.countries.map((country) => country as CountryWithMetrics)
);

const numeric = (value: unknown): number => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : 0;
};

const totalRegionsCount = computed(() =>
    countries.value.reduce((sum, country) => sum + numeric(country.regions_count), 0)
);
const totalMilkCentersCount = computed(() =>
    countries.value.reduce((sum, country) => sum + numeric(country.milk_centers_count), 0)
);
const totalFarmersCount = computed(() =>
    countries.value.reduce((sum, country) => sum + numeric(country.farmers_count), 0)
);

const averageFarmersPerCountry = computed(() => {
    if (!countries.value.length) return 0;
    return Math.round((totalFarmersCount.value / countries.value.length) * 10) / 10;
});

const averageCentersPerCountry = computed(() => {
    if (!countries.value.length) return 0;
    return Math.round((totalMilkCentersCount.value / countries.value.length) * 10) / 10;
});

const isoCodeOptions = computed(() =>
    Array.from(
        new Set(
            countries.value
                .map((country) => country.iso_code)
                .filter((code): code is string => Boolean(code))
        )
    ).sort((a, b) => a.localeCompare(b))
);

const filteredCountries = computed(() => {
    let list = [...countries.value];

    const search = searchTerm.value.trim().toLowerCase();
    if (search) {
        list = list.filter((country) => {
            const name = country.name?.toLowerCase() ?? '';
            const iso = country.iso_code?.toLowerCase() ?? '';
            return name.includes(search) || iso.includes(search);
        });
    }

    if (isoFilter.value) {
        list = list.filter((country) => country.iso_code === isoFilter.value);
    }

    if (onlyWithActivity.value) {
        list = list.filter(
            (country) => numeric(country.milk_centers_count) > 0 || numeric(country.farmers_count) > 0
        );
    }

    return list;
});

const displayedCountries = computed(() => {
    const list = [...filteredCountries.value];

    switch (sortOption.value) {
        case 'name-desc':
            list.sort((a, b) => (b.name ?? '').localeCompare(a.name ?? ''));
            break;
        case 'farmers-desc':
            list.sort((a, b) => numeric(b.farmers_count) - numeric(a.farmers_count));
            break;
        case 'centers-desc':
            list.sort((a, b) => numeric(b.milk_centers_count) - numeric(a.milk_centers_count));
            break;
        case 'regions-desc':
            list.sort((a, b) => numeric(b.regions_count) - numeric(a.regions_count));
            break;
        default:
            list.sort((a, b) => (a.name ?? '').localeCompare(b.name ?? ''));
            break;
    }

    return list;
});

const filteredTotals = computed(() => {
    const list = displayedCountries.value;
    const totalCountries = list.length;
    const totalFarmers = list.reduce((sum, country) => sum + numeric(country.farmers_count), 0);
    const totalCenters = list.reduce((sum, country) => sum + numeric(country.milk_centers_count), 0);
    const totalRegions = list.reduce((sum, country) => sum + numeric(country.regions_count), 0);

    return {
        totalCountries,
        totalFarmers,
        totalCenters,
        totalRegions,
        averageFarmers: totalCountries ? Math.round((totalFarmers / totalCountries) * 10) / 10 : 0,
        averageCenters: totalCountries ? Math.round((totalCenters / totalCountries) * 10) / 10 : 0,
    };
});

const topCountryByFarmers = computed(() => {
    if (!countries.value.length) return null;
    return [...countries.value].sort((a, b) => numeric(b.farmers_count) - numeric(a.farmers_count))[0] ?? null;
});

const topCountryByMilkCenters = computed(() => {
    if (!countries.value.length) return null;
    return [...countries.value].sort((a, b) => numeric(b.milk_centers_count) - numeric(a.milk_centers_count))[0] ?? null;
});

const viewTopCountry = computed(() => {
    if (!displayedCountries.value.length) return null;
    return [...displayedCountries.value].sort((a, b) => numeric(b.farmers_count) - numeric(a.farmers_count))[0] ?? null;
});

const handleCountryCreated = () => {
    showAddModal.value = false;
};

const handleDeleteCountry = async (countryId: number) => {
    const target = countries.value.find((country) => country.id === countryId);
    const countryName = target?.name ?? 'this country';

    const confirmed = window.confirm(`Delete ${countryName}? This action cannot be undone.`);
    if (!confirmed) return;

    await countriesStore.deleteCountry(countryId);
};

const resetFilters = () => {
    searchTerm.value = '';
    isoFilter.value = '';
    sortOption.value = 'name-asc';
    onlyWithActivity.value = false;
};
</script>

