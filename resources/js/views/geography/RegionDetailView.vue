<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2">
                <router-link
                    to="/admin/regions"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to Regions
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ regionTitle }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <span v-if="countryName">Country: <span class="font-semibold text-gray-700">{{ countryName }}</span></span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                            <Icon icon="mdi:tag-outline" :size="14" />
                            {{ regionSlug }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isRegionLoading" class="rounded-lg bg-white p-6 shadow">
            <p class="text-gray-600">Loading region details...</p>
        </div>

        <template v-else>
            <div v-if="errorMessage" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                {{ errorMessage }}
            </div>

            <div v-if="region" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <StatisticalCard title="Districts" :value="districtCount" icon="mdi:map-outline" iconClass="text-blue-600" caption="Districts within this region" />
                    <StatisticalCard title="Counties" :value="countyCount" icon="mdi:home-group" iconClass="text-indigo-500" caption="Across all districts" />
                    <StatisticalCard title="Subcounties" :value="subcountyCount" icon="mdi:map-marker-radius-outline" iconClass="text-emerald-500" caption="Nested within counties" />
                    <StatisticalCard title="Farmers" :value="farmersCount" icon="mdi:account-group-outline" iconClass="text-purple-600" caption="Farmers registered within this region" />
                    <StatisticalCard title="Milk Collection Centers" :value="milkCentersCount" icon="mdi:milk-outline" iconClass="text-orange-500" caption="Centers operating within this region" />
                </div>




                    

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Milk Collection Centers</h2>
                            <p class="text-sm text-gray-500">
                                Centers operating in districts within this region
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
                                No milk collection centers registered for this region.
                            </div>
                            <div v-else class="mt-6 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                                <div
                                    v-for="center in milkCenters"
                                    :key="center.id"
                                    class="flex flex-col gap-3 rounded-lg border border-gray-100 bg-gray-50 p-4"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-base font-semibold text-gray-900">
                                                {{ center.name }}
                                            </h3>
                                            <p class="text-xs text-gray-500">
                                                {{ center.registration_number ?? 'Unregistered ID' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-medium text-blue-700">
                                            <Icon icon="mdi:snowflake-variant" :size="13" />
                                            {{ center.cooler_capacity_liters ?? 0 }} L
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ center.physical_address }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ formatCenterLocation(center) }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-medium"
                                            :class="center.has_testing_equipment ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                        >
                                            <Icon icon="mdi:flask-outline" :size="13" />
                                            Testing
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-medium"
                                            :class="center.has_washing_bay ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                        >
                                            <Icon icon="mdi:water-outline" :size="13" />
                                            Washing
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Districts &amp; Counties</h2>
                        <span class="text-sm text-gray-500">
                            Detailed breakdown of administrative areas
                        </span>
                    </div>

                    <div v-if="districtCount === 0" class="mt-6 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                        No districts have been registered for this region yet.
                    </div>

                    <ul v-else class="mt-6 space-y-4">
                        <li
                            v-for="district in regionDistricts"
                            :key="district.id"
                            class="rounded-lg border border-gray-100 bg-gray-50 p-4"
                        >
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">
                                       <router-link :to="`/admin/districts/${district.id}`" class="text-blue-600 hover:text-blue-900">{{ getDistrictDisplayName(district) }}</router-link>
                                    </h3>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">
                                        Slug: {{ getDistrictSlug(district) }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                        <Icon icon="mdi:home-city-outline" :size="14" />
                                        {{ district.counties?.length ?? 0 }} Counties
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                        <Icon icon="mdi:map-marker-multiple-outline" :size="14" />
                                        {{ getSubcountyCountForDistrict(district) }} Subcounties
                                    </span>
                                </div>
                            </div>

                            <div v-if="district.counties?.length" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div
                                    v-for="county in district.counties"
                                    :key="county.id"
                                    class="rounded-lg border border-white bg-white p-4 shadow-sm"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                {{ getCountyDisplayName(county) }}
                                            </h4>
                                            <p class="text-xs uppercase tracking-wide text-gray-400">
                                                Slug: {{ getCountySlug(county) }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-0.5 text-[11px] font-medium text-indigo-700">
                                            <Icon icon="mdi:map-marker-outline" :size="13" />
                                            {{ county.subcounties?.length ?? 0 }} Subcounties
                                        </span>
                                    </div>
                                    <div class="mt-3 text-xs text-gray-500">
                                        <p>Parishes: {{ getParishCountForCounty(county) }}</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Farmers</h2>
                            <p class="text-sm text-gray-500">Filtered to farmers whose districts fall within this region</p>
                        </div>
                        <div class="inline-flex items-center gap-2 text-sm text-gray-500">
                            <Icon icon="mdi:database-outline" :size="18" />
                            Showing {{ regionFarmers.length }} farmer{{ regionFarmers.length === 1 ? '' : 's' }}
                        </div>
                    </div>

                    <div v-if="isFarmersLoading" class="mt-6 rounded-md border border-gray-200 p-6 text-gray-600">
                        Loading farmers...
                    </div>
                    <div v-else>
                        <div v-if="farmersError" class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-red-700">
                            {{ farmersError }}
                        </div>
                        <template v-else>
                            <div v-if="regionFarmers.length === 0" class="mt-6 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                                No farmers found for this region.
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
                                        <tr v-for="farmer in regionFarmers" :key="farmer.id" class="hover:bg-gray-50">
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
                                                <div>{{ getFarmerDistrictName(farmer) }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ getFarmerLocationTrail(farmer) }}
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
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import {
    useGeographyStore,
    type Country,
    type Region as RegionType,
    type District as DistrictType,
    type County as CountyType,
    type Subcounty as SubcountyType,
    type Parish as ParishType,
    type Farmer,
    type MilkCollectionCenter,
} from '../../stores/geographyStore';

type ParishDetail = Omit<ParishType, 'villages'> & {
    parish_name?: string;
    parish_slug?: string;
    villages?: Array<{ id: number; name?: string; village_name?: string; village_slug?: string }>;
};

type SubcountyDetail = Omit<SubcountyType, 'parishes'> & {
    subcounty_name?: string;
    subcounty_slug?: string;
    parishes?: ParishDetail[];
};

type CountyDetail = Omit<CountyType, 'subcounties'> & {
    county_name?: string;
    county_slug?: string;
    subcounties?: SubcountyDetail[];
};

type DistrictDetail = Omit<DistrictType, 'counties'> & {
    district_name?: string;
    district_slug?: string;
    counties?: CountyDetail[];
};

type RegionDetail = Omit<RegionType, 'districts'> & {
    region_name?: string;
    region_slug?: string;
    country?: Country;
    districts?: DistrictDetail[];
};

const route = useRoute();
const geographyStore = useGeographyStore();

const regionId = computed(() => Number(route.params.id));

const region = ref<RegionDetail | null>(null);
const isRegionLoading = ref(false);
const isFarmersLoading = ref(false);
const errorMessage = ref<string | null>(null);
const farmersError = ref<string | null>(null);
const regionFarmers = ref<Farmer[]>([]);
const isMilkCentersLoading = ref(false);
const milkCentersError = ref<string | null>(null);
const milkCenters = ref<MilkCollectionCenter[]>([]);

const regionTitle = computed(() => region.value?.region_name ?? region.value?.name ?? 'Region');
const regionSlug = computed(() => region.value?.region_slug ?? region.value?.slug ?? '—');
const countryName = computed(() => region.value?.country?.name ?? (region.value?.country as any)?.country_name ?? '');

const regionDistricts = computed<DistrictDetail[]>(() => (region.value?.districts as DistrictDetail[] ?? []));

const districtCount = computed(() => regionDistricts.value.length);
const countyCount = computed(() =>
    regionDistricts.value.reduce((total, district) => total + (district.counties?.length ?? 0), 0)
);
const subcountyCount = computed(() =>
    regionDistricts.value.reduce((total, district) => {
        const districtSubcounties =
            district.counties?.reduce((countyTotal, county) => countyTotal + (county.subcounties?.length ?? 0), 0) ?? 0;
        return total + districtSubcounties;
    }, 0)
);
const farmersCount = computed(() => regionFarmers.value.length);
const milkCentersCount = computed(() => milkCenters.value.length);

const districtIdentifiers = computed(() => {
    const identifiers = new Set<string>();
    regionDistricts.value.forEach((district) => {
        if (district.id != null) identifiers.add(String(district.id));
        if (district.district_name) identifiers.add(district.district_name);
        if ((district as any).name) identifiers.add((district as any).name);
    });
    return identifiers;
});

const lookupMaps = computed(() => {
    const district = new Map<string, string>();
    const county = new Map<string, string>();
    const subcounty = new Map<string, string>();
    const parish = new Map<string, string>();
    const village = new Map<string, string>();

    regionDistricts.value.forEach((districtItem) => {
        const districtName = districtItem.district_name ?? (districtItem as any).name ?? `District ${districtItem.id}`;
        if (districtItem.id != null) district.set(String(districtItem.id), districtName);
        if (districtItem.district_name) district.set(districtItem.district_name, districtName);
        if ((districtItem as any).name) district.set((districtItem as any).name, districtName);

        districtItem.counties?.forEach((countyItem) => {
            const countyName = countyItem.county_name ?? (countyItem as any).name ?? `County ${countyItem.id}`;
            if (countyItem.id != null) county.set(String(countyItem.id), countyName);
            if (countyItem.county_name) county.set(countyItem.county_name, countyName);
            if ((countyItem as any).name) county.set((countyItem as any).name, countyName);

            countyItem.subcounties?.forEach((subcountyItem) => {
                const subcountyName = subcountyItem.subcounty_name ?? (subcountyItem as any).name ?? `Subcounty ${subcountyItem.id}`;
                if (subcountyItem.id != null) subcounty.set(String(subcountyItem.id), subcountyName);
                if (subcountyItem.subcounty_name) subcounty.set(subcountyItem.subcounty_name, subcountyName);
                if ((subcountyItem as any).name) subcounty.set((subcountyItem as any).name, subcountyName);

                subcountyItem.parishes?.forEach((parishItem) => {
                    const parishName = parishItem.parish_name ?? (parishItem as any).name ?? `Parish ${parishItem.id}`;
                    if (parishItem.id != null) parish.set(String(parishItem.id), parishName);
                    if (parishItem.parish_name) parish.set(parishItem.parish_name, parishName);
                    if ((parishItem as any).name) parish.set((parishItem as any).name, parishName);

                    parishItem.villages?.forEach((villageItem) => {
                        const villageName = villageItem?.village_name ?? (villageItem as any)?.name ?? `Village ${villageItem?.id}`;
                        if (villageItem?.id != null) village.set(String(villageItem.id), villageName);
                        if (villageItem?.village_name) village.set(villageItem.village_name, villageName);
                        if ((villageItem as any)?.name) village.set((villageItem as any).name, villageName);
                    });
                });
            });
        });
    });

    return { district, county, subcounty, parish, village };
});

const getDistrictDisplayName = (district: DistrictDetail) =>
    district.district_name ?? (district as any).name ?? `District ${district.id}`;
const getCountyDisplayName = (county: CountyDetail) =>
    county.county_name ?? (county as any).name ?? `County ${county.id}`;
const getDistrictSlug = (district: DistrictDetail) =>
    district.district_slug ?? (district as any).slug ?? '—';
const getCountySlug = (county: CountyDetail) =>
    county.county_slug ?? (county as any).slug ?? '—';

const getSubcountyCountForDistrict = (district: DistrictDetail) =>
    district.counties?.reduce((total, county) => total + (county.subcounties?.length ?? 0), 0) ?? 0;

const getParishCountForCounty = (county: CountyDetail) =>
    county.subcounties?.reduce((total, subcounty) => total + (subcounty.parishes?.length ?? 0), 0) ?? 0;

const resolveName = (value: string | undefined, map: Map<string, string>) => {
    if (value == null) return undefined;
    return map.get(String(value)) ?? value;
};

const getFarmerDistrictName = (farmer: Farmer) =>
    resolveName(farmer.district, lookupMaps.value.district) ?? '—';

const getFarmerLocationTrail = (farmer: Farmer) => {
    const segments = [
        resolveName(farmer.county, lookupMaps.value.county),
        resolveName(farmer.sub_county, lookupMaps.value.subcounty),
        resolveName(farmer.parish, lookupMaps.value.parish),
        resolveName(farmer.village, lookupMaps.value.village),
    ].filter((segment): segment is string => Boolean(segment));
    return segments.length ? segments.join(' • ') : 'Location details unavailable';
};

const formatCenterLocation = (center: MilkCollectionCenter) => {
    const loc = center.location ?? {};
    const segments: string[] = [];

    const districtName = resolveName(loc.district_id != null ? String(loc.district_id) : undefined, lookupMaps.value.district);
    const countyName = resolveName(loc.county_id != null ? String(loc.county_id) : undefined, lookupMaps.value.county);
    const subcountyName = resolveName(loc.subcounty_id != null ? String(loc.subcounty_id) : undefined, lookupMaps.value.subcounty);
    const parishName = resolveName(loc.parish_id != null ? String(loc.parish_id) : undefined, lookupMaps.value.parish);
    const villageName = resolveName(loc.village_id != null ? String(loc.village_id) : undefined, lookupMaps.value.village);

    if (districtName) segments.push(districtName);
    if (countyName) segments.push(countyName);
    if (subcountyName) segments.push(subcountyName);
    if (parishName) segments.push(parishName);
    if (villageName) segments.push(villageName);

    return segments.length ? segments.join(' • ') : 'Location details unavailable';
};

const loadRegion = async (id: number) => {
    isRegionLoading.value = true;
    errorMessage.value = null;
    try {
        const data = await geographyStore.getRegion(id);
        region.value = data as RegionDetail;
    } catch (error) {
        errorMessage.value = geographyStore.error ?? 'Failed to load region details.';
    } finally {
        isRegionLoading.value = false;
    }
};

const loadRegionFarmers = async () => {
    if (!region.value) {
        regionFarmers.value = [];
        return;
    }

    const identifiers = districtIdentifiers.value;
    if (!identifiers.size) {
        regionFarmers.value = [];
        return;
    }

    isFarmersLoading.value = true;
    farmersError.value = null;
    regionFarmers.value = [];

    try {
        const perPage = 100;
        let page = 1;
        let lastPage = 1;
        const seenFarmerIds = new Set<number>();

        do {
            const response = await geographyStore.getCountryFarmers(region.value.country_id, page, perPage);
            const pageFarmers = response.data ?? [];
            const filtered = pageFarmers.filter((farmer) => {
                if (!farmer?.district) {
                    return false;
                }
                const value = String(farmer.district);
                return identifiers.has(value);
            });

            filtered.forEach((farmer) => {
                if (farmer.id != null && seenFarmerIds.has(farmer.id)) {
                    return;
                }
                regionFarmers.value.push(farmer);
                if (farmer.id != null) {
                    seenFarmerIds.add(farmer.id);
                }
            });

            lastPage = response.last_page ?? page;
            page++;
        } while (page <= lastPage);
    } catch (error) {
        farmersError.value = geographyStore.error ?? 'Failed to load farmers.';
    } finally {
        isFarmersLoading.value = false;
    }
};

const loadRegionMilkCenters = async () => {
    if (Number.isNaN(regionId.value)) {
        milkCenters.value = [];
        return;
    }

    isMilkCentersLoading.value = true;
    milkCentersError.value = null;

    try {
        const data = await geographyStore.getMilkCollectionCenters({ region_id: regionId.value });
        milkCenters.value = data;
    } catch (error) {
        milkCentersError.value = geographyStore.error ?? 'Failed to load milk collection centers.';
        milkCenters.value = [];
    } finally {
        isMilkCentersLoading.value = false;
    }
};

watch(
    regionId,
    async (newId) => {
        if (Number.isNaN(newId)) {
            return;
        }
        await loadRegion(newId);
        await Promise.all([loadRegionFarmers(), loadRegionMilkCenters()]);
    },
    { immediate: true }
);
</script>

