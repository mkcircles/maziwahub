<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center flex-col gap-3">
                <router-link
                    to="/admin/countries"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to Countries
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ country?.name ?? 'Loading country...' }}
                    </h1>
                    <p class="text-sm text-gray-500" v-if="country">
                        ISO Code: <span class="font-semibold tracking-wider">{{ country.iso_code }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div v-if="isLoading" class="rounded-lg bg-white p-6 shadow">
            <p class="text-gray-600">Loading country details...</p>
        </div>

        <template v-else>
            <div v-if="errorMessage" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                {{ errorMessage }}
            </div>

            <div v-if="country" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Regions</h3>
                            <Icon icon="mdi:map-outline" :size="20" className="text-blue-600" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ country.regions?.length ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Total regions in this country</p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Registered Farmers</h3>
                            <Icon icon="mdi:account-group-outline" :size="20" className="text-green-600" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ farmerTotal }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Farmers linked to this country</p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Milk Collection Centers</h3>
                            <Icon icon="mdi:milk-outline" :size="20" className="text-orange-500" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ milkCentersCount }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Centers registered within this country</p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Data Status</h3>
                            <Icon icon="mdi:database-search-outline" :size="20" className="text-purple-600" />
                        </div>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">Up to date</p>
                        <p class="text-xs text-gray-500 mt-1">Last refreshed when page loaded</p>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Milk Collection Centers</h2>
                            <p class="text-sm text-gray-500">
                                Facilities registered within this country's administrative hierarchy
                            </p>
                        </div>
                        <div class="inline-flex items-center gap-2 text-sm text-gray-500">
                            <Icon icon="mdi:milk-outline" :size="18" />
                            Showing {{ milkCentersCount }} center{{ milkCentersCount === 1 ? '' : 's' }}
                        </div>
                    </div>

                    <div v-if="isMilkCentersLoading" class="mt-6 rounded-md border border-gray-200 p-6 text-gray-600">
                        Loading milk collection centers...
                    </div>
                    <div v-else>
                        <div v-if="milkCentersError" class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-red-700">
                            {{ milkCentersError }}
                        </div>
                        <template v-else>
                            <div v-if="milkCenters.length === 0" class="mt-6 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                                No milk collection centers registered for this country.
                            </div>
                            <div v-else class="mt-6 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                                <CountryMilkCenterCard
                                    v-for="center in milkCenters"
                                    :key="center.id"
                                    :center="center"
                                    :format-location="formatCenterLocation"
                                    :minified="true"
                                />
                            </div>
                        </template>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Regions &amp; Districts</h2>
                        <span class="text-sm text-gray-500">Regions loaded from hierarchy</span>
                    </div>
                    <div v-if="(country.regions?.length ?? 0) === 0" class="mt-4 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                        No regions have been registered for this country yet.
                    </div>
                    <ul v-else class="mt-4 space-y-4">
                        <li
                            v-for="region in country.regions"
                            :key="region.id"
                            class="rounded-lg border border-gray-100 bg-gray-50 p-4"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ region.region_name }}</h3>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Slug: {{ region.region_slug }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                    <Icon icon="mdi:city-variant-outline" :size="14" />
                                    {{ region.districts_count ?? 0 }} Districts
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                                    <Icon icon="mdi:location-multiple" :size="14" />
                                    {{ region.counties_count ?? 0 }} Counties
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700">
                                    <Icon icon="mdi:location-multiple-outline" :size="14" />
                                    {{ region.subcounties_count ?? 0 }} Sub counties
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                    <Icon icon="mdi:account-group-outline" :size="14" />
                                    {{ region.subcounties_count ?? 0 }} Farmers
                                </span>
                                <router-link
                                    :to="`/admin/regions/${region.id}`"
                                    class="inline-flex items-center text-sm gap-1 text-blue-600 hover:text-blue-900 mr-4"
                                >
                                    <Icon icon="mdi:eye" :size="16" />
                                    View
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </div>

                        <div class="rounded-lg bg-white p-6 shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Farmers</h2>
                                    <p class="text-sm text-gray-500">Paginated list of farmers linked by district</p>
                                </div>
                                <div class="inline-flex items-center gap-2 text-sm text-gray-500">
                                    <Icon icon="mdi:database" :size="18" />
                                    Total: {{ farmerTotal }}
                                </div>
                            </div>

                            <div v-if="isFarmersLoading" class="mt-6 rounded-md border border-gray-200 p-6 text-gray-600">
                                Loading farmers...
                            </div>

                            <div v-else>
                                <div v-if="farmers.length === 0" class="mt-6 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                                    No farmers found for this country.
                                </div>
                                <div v-else class="mt-6 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Farmer</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contact</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="farmer in farmers" :key="farmer.id" class="hover:bg-gray-50">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ farmer.first_name }} {{ farmer.last_name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 uppercase tracking-wide">
                                                        ID: {{ farmer.farmer_id }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    <div>{{ farmer.phone_number ?? '—' }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        MCC: {{ farmer.milkCollectionCenter?.name ?? 'Not assigned' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    <div>{{ farmer.district ?? '—' }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ [farmer.county, farmer.sub_county, farmer.parish, farmer.village].filter(Boolean).join(' • ') || 'Location details unavailable' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                                        :class="farmer.status === 'active'
                                                            ? 'bg-green-100 text-green-700'
                                                            : farmer.status === 'pending'
                                                                ? 'bg-yellow-100 text-yellow-700'
                                                                : 'bg-gray-100 text-gray-600'"
                                                    >
                                                        <Icon
                                                            :icon="farmer.status === 'active'
                                                                ? 'mdi:check-circle-outline'
                                                                : farmer.status === 'pending'
                                                                    ? 'mdi:clock-outline'
                                                                    : 'mdi:alert-circle-outline'"
                                                            :size="14"
                                                        />
                                                        {{ farmer.status ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div v-if="countryFarmers" class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                                    <p class="text-sm text-gray-500">
                                        Page {{ countryFarmers.current_page }} of {{ countryFarmers.last_page }}
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                                            :disabled="!canGoToPrevious"
                                            @click="changeFarmersPage(countryFarmers.current_page - 1)"
                                        >
                                            <Icon icon="mdi:chevron-left" :size="18" />
                                            Previous
                                        </button>
                                        <button
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                                            :disabled="!canGoToNext"
                                            @click="changeFarmersPage(countryFarmers.current_page + 1)"
                                        >
                                            Next
                                            <Icon icon="mdi:chevron-right" :size="18" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import CountryMilkCenterCard from '../../components/geography/CountryMilkCenterCard.vue';
import {
    useGeographyStore,
    type Country,
    type Farmer,
    type MilkCollectionCenter,
    type Region,
} from '../../stores/geographyStore';

const route = useRoute();
const countriesStore = useGeographyStore();

const countryId = computed(() => Number(route.params.id));

type RegionWithMetrics = Region & {
    region_name?: string | null;
    region_slug?: string | null;
    districts_count?: number | null;
    counties_count?: number | null;
    subcounties_count?: number | null;
    farmers_count?: number | null;
};

const country = ref<(Country & { regions?: RegionWithMetrics[] }) | null>(null);
const isCountryLoading = ref(false);
const errorMessage = ref<string | null>(null);

const isFarmersLoading = ref(false);
const farmersPage = ref(1);

const isMilkCentersLoading = ref(false);
const milkCentersError = ref<string | null>(null);
const milkCenters = ref<MilkCollectionCenter[]>([]);


const countryFarmers = computed(() => countriesStore.countryFarmers);
const farmers = computed<Farmer[]>(() => countryFarmers.value?.data ?? []);
const farmerTotal = computed(() => countryFarmers.value?.total ?? 0);
const milkCentersCount = computed(() => milkCenters.value.length);

const canGoToPrevious = computed(() => {
    if (!countryFarmers.value) return false;
    return countryFarmers.value.current_page > 1;
});

const canGoToNext = computed(() => {
    if (!countryFarmers.value) return false;
    return countryFarmers.value.current_page < countryFarmers.value.last_page;
});

const isLoading = computed(
    () => isCountryLoading.value || isFarmersLoading.value || isMilkCentersLoading.value
);

const hierarchyMaps = computed(() => {
    const region = new Map<string, string>();
    const district = new Map<string, string>();
    const county = new Map<string, string>();
    const subcounty = new Map<string, string>();
    const parish = new Map<string, string>();
    const village = new Map<string, string>();

    country.value?.regions?.forEach((regionItem: any) => {
        const regionName = regionItem.region_name ?? regionItem.name ?? `Region ${regionItem.id}`;
        if (regionItem.id != null) region.set(String(regionItem.id), regionName);
        if (regionItem.region_name) region.set(regionItem.region_name, regionName);
        if (regionItem.name) region.set(regionItem.name, regionName);

        regionItem.districts?.forEach((districtItem: any) => {
            const districtName = districtItem.district_name ?? districtItem.name ?? `District ${districtItem.id}`;
            if (districtItem.id != null) district.set(String(districtItem.id), districtName);
            if (districtItem.district_name) district.set(districtItem.district_name, districtName);
            if (districtItem.name) district.set(districtItem.name, districtName);

            districtItem.counties?.forEach((countyItem: any) => {
                const countyName = countyItem.county_name ?? countyItem.name ?? `County ${countyItem.id}`;
                if (countyItem.id != null) county.set(String(countyItem.id), countyName);
                if (countyItem.county_name) county.set(countyItem.county_name, countyName);
                if (countyItem.name) county.set(countyItem.name, countyName);

                countyItem.subcounties?.forEach((subcountyItem: any) => {
                    const subcountyName = subcountyItem.subcounty_name ?? subcountyItem.name ?? `Subcounty ${subcountyItem.id}`;
                    if (subcountyItem.id != null) subcounty.set(String(subcountyItem.id), subcountyName);
                    if (subcountyItem.subcounty_name) subcounty.set(subcountyItem.subcounty_name, subcountyName);
                    if (subcountyItem.name) subcounty.set(subcountyItem.name, subcountyName);

                    subcountyItem.parishes?.forEach((parishItem: any) => {
                        const parishName = parishItem.parish_name ?? parishItem.name ?? `Parish ${parishItem.id}`;
                        if (parishItem.id != null) parish.set(String(parishItem.id), parishName);
                        if (parishItem.parish_name) parish.set(parishItem.parish_name, parishName);
                        if (parishItem.name) parish.set(parishItem.name, parishName);

                        parishItem.villages?.forEach((villageItem: any) => {
                            if (!villageItem) return;
                            const villageName = villageItem.village_name ?? villageItem.name ?? `Village ${villageItem.id}`;
                            if (villageItem.id != null) village.set(String(villageItem.id), villageName);
                            if (villageItem.village_name) village.set(villageItem.village_name, villageName);
                            if (villageItem.name) village.set(villageItem.name, villageName);
                        });
                    });
                });
            });
        });
    });

    return { region, district, county, subcounty, parish, village };
});

const resolveHierarchyName = (map: Map<string, string>, value?: number | string | null) => {
    if (value == null) return undefined;
    return map.get(String(value)) ?? String(value);
};

const formatCenterLocation = (center: MilkCollectionCenter) => {
    const loc = center.location ?? {};
    const segments: string[] = [];

    const districtName = resolveHierarchyName(hierarchyMaps.value.district, loc.district_id);
    const countyName = resolveHierarchyName(hierarchyMaps.value.county, loc.county_id);
    const subcountyName = resolveHierarchyName(hierarchyMaps.value.subcounty, loc.subcounty_id);
    const parishName = resolveHierarchyName(hierarchyMaps.value.parish, loc.parish_id);
    const villageName = resolveHierarchyName(hierarchyMaps.value.village, loc.village_id);

    if (districtName) segments.push(districtName);
    if (countyName) segments.push(countyName);
    if (subcountyName) segments.push(subcountyName);
    if (parishName) segments.push(parishName);
    if (villageName) segments.push(villageName);

    if (segments.length === 0 && loc.country_id) {
        const countryName = country.value?.name ?? `Country ${loc.country_id}`;
        segments.push(countryName);
    }

    return segments.length ? segments.join(' • ') : 'Location details unavailable';
};

const loadCountry = async () => {
    if (Number.isNaN(countryId.value)) return;
    isCountryLoading.value = true;
    errorMessage.value = null;

    try {
        country.value = await countriesStore.getCountry(countryId.value);
    } catch (error) {
        errorMessage.value = countriesStore.error ?? 'Failed to load country details.';
    } finally {
        isCountryLoading.value = false;
    }
};

const loadCountryMilkCenters = async () => {
    if (Number.isNaN(countryId.value)) return;
    isMilkCentersLoading.value = true;
    milkCentersError.value = null;

    try {
        const data = await countriesStore.getMilkCollectionCenters({ country_id: countryId.value });
        milkCenters.value = data;
    } catch (error) {
        milkCentersError.value = countriesStore.error ?? 'Failed to load milk collection centers.';
        milkCenters.value = [];
    } finally {
        isMilkCentersLoading.value = false;
    }
};

const loadCountryFarmers = async (page = 1) => {
    if (Number.isNaN(countryId.value)) return;
    isFarmersLoading.value = true;
    errorMessage.value = null;

    try {
        await countriesStore.getCountryFarmers(countryId.value, page);
        farmersPage.value = page;
    } catch (error) {
        errorMessage.value = countriesStore.error ?? 'Failed to load farmers.';
    } finally {
        isFarmersLoading.value = false;
    }
};

const changeFarmersPage = (page: number) => {
    if (page === farmersPage.value) return;
    loadCountryFarmers(page);
};

onMounted(async () => {
    await loadCountry();
    await Promise.all([loadCountryMilkCenters(), loadCountryFarmers()]);
});

watch(
    () => route.params.id,
    async () => {
        farmersPage.value = 1;
        await loadCountry();
        await Promise.all([loadCountryMilkCenters(), loadCountryFarmers()]);
    }
);

</script>

