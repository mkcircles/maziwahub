<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2">
                <router-link
                    to="/admin/districts"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to Districts
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ districtTitle }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <span v-if="regionName">
                            Region:
                            <router-link
                                v-if="regionLink"
                                :to="regionLink"
                                class="font-semibold text-blue-600 hover:text-blue-800"
                            >
                                {{ regionName }}
                            </router-link>
                            <span v-else class="font-semibold text-gray-700">{{ regionName }}</span>
                        </span>
                        <span v-if="countryName">
                            Country:
                            <router-link
                                v-if="countryLink"
                                :to="countryLink"
                                class="font-semibold text-blue-600 hover:text-blue-800"
                            >
                                {{ countryName }}
                            </router-link>
                            <span v-else class="font-semibold text-gray-700">{{ countryName }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                            <Icon icon="mdi:tag-outline" :size="14" />
                            {{ districtSlug }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isDistrictLoading" class="rounded-lg bg-white p-6 shadow">
            <p class="text-gray-600">Loading district details...</p>
        </div>

        <template v-else>
            <div v-if="errorMessage" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                {{ errorMessage }}
            </div>

            <div v-if="district" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium uppercase tracking-wide text-gray-500">Counties</h3>
                            <Icon icon="mdi:home-group" :size="20" className="text-indigo-500" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ countyCount }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Within this district</p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium uppercase tracking-wide text-gray-500">Subcounties</h3>
                            <Icon icon="mdi:map-marker-radius-outline" :size="20" className="text-emerald-500" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ subcountyCount }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Across all counties</p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium uppercase tracking-wide text-gray-500">Parishes</h3>
                            <Icon icon="mdi:church-outline" :size="20" className="text-blue-500" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ parishCount }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Nested within subcounties</p>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium uppercase tracking-wide text-gray-500">Villages</h3>
                            <Icon icon="mdi:home-city-outline" :size="20" className="text-purple-600" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ villageCount }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Grassroots administrative units</p>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Milk Collection Centers</h2>
                            <p class="text-sm text-gray-500">
                                Centers serving communities within this district
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
                                No milk collection centers registered for this district.
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
                        <h2 class="text-lg font-semibold text-gray-900">Counties &amp; Subcounties</h2>
                        <span class="text-sm text-gray-500">
                            Detailed administrative breakdown within the district
                        </span>
                    </div>

                    <div v-if="countyCount === 0" class="mt-6 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                        No counties have been registered for this district yet.
                    </div>

                    <div v-else class="mt-6 grid gap-4 lg:grid-cols-1 xl:grid-cols-2">
                        <div
                            v-for="county in districtCounties"
                            :key="county.id"
                            class="rounded-lg border border-gray-100 bg-gray-50 p-4"
                        >
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">
                                        <router-link
                                            :to="`/admin/counties/${county.id}`"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            {{ getCountyDisplayName(county) }}
                                        </router-link>
                                    </h3>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">
                                        Slug: {{ getCountySlug(county) }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                        <Icon icon="mdi:map-marker-multiple-outline" :size="14" />
                                        {{ county.subcounties?.length ?? 0 }} Subcounties
                                    </span>
                                </div>
                            </div>

                            <div v-if="county.subcounties?.length" class="mt-4 space-y-3">
                                <div
                                    v-for="subcounty in county.subcounties"
                                    :key="subcounty.id"
                                    class="rounded-lg border border-white bg-white p-4 shadow-sm"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                <router-link
                                                    :to="`/admin/subcounties/${subcounty.id}`"
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
                                                    {{ getSubcountyDisplayName(subcounty) }}
                                                </router-link>
                                            </h4>
                                            <p class="text-xs uppercase tracking-wide text-gray-400">
                                                Slug: {{ getSubcountySlug(subcounty) }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-0.5 text-[11px] font-medium text-indigo-700">
                                            <Icon icon="mdi:church-outline" :size="13" />
                                            {{ subcounty.parishes?.length ?? 0 }} Parishes
                                        </span>
                                    </div>

                                    <div v-if="subcounty.parishes?.length" class="mt-3 grid gap-2 sm:grid-cols-2">
                                        <div
                                            v-for="parish in subcounty.parishes"
                                            :key="parish.id"
                                            class="rounded-md border border-dashed border-gray-200 p-3"
                                        >
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-700">
                                                        <router-link
                                                            :to="`/admin/parishes/${parish.id}`"
                                                            class="text-blue-600 hover:text-blue-900"
                                                        >
                                                            {{ getParishDisplayName(parish) }}
                                                        </router-link>
                                                    </h5>
                                                    <p class="text-[11px] uppercase tracking-wide text-gray-400">
                                                        Slug: {{ getParishSlug(parish) }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-medium text-purple-700">
                                                    <Icon icon="mdi:home-city-outline" :size="12" />
                                                    {{ parish.villages?.length ?? 0 }} Villages
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium uppercase tracking-wide text-gray-500">Farmers</h3>
                            <Icon icon="mdi:account-group-outline" :size="20" className="text-purple-500" />
                        </div>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ farmersCount }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Registered within this district</p>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Farmers</h2>
                            <p class="text-sm text-gray-500">
                                Farmers registered whose district matches this district
                            </p>
                        </div>
                        <div class="inline-flex items-center gap-2 text-sm text-gray-500">
                            <Icon icon="mdi:database-outline" :size="18" />
                            Showing {{ districtFarmers.length }} farmer{{ districtFarmers.length === 1 ? '' : 's' }}
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
                            <div v-if="districtFarmers.length === 0" class="mt-6 rounded-md border border-dashed border-gray-200 p-6 text-center text-gray-500">
                                No farmers found for this district.
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
                                        <tr v-for="farmer in districtFarmers" :key="farmer.id" class="hover:bg-gray-50">
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
                                                <div>{{ districtTitle }}</div>
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
import {
    useGeographyStore,
    type Country,
    type Region as RegionType,
    type District as DistrictType,
    type County as CountyType,
    type Subcounty as SubcountyType,
    type Parish as ParishType,
    type Village as VillageType,
    type Farmer,
    type MilkCollectionCenter,
} from '../../stores/geographyStore';

type VillageDetail = VillageType & {
    village_name?: string;
    village_slug?: string;
};

type ParishDetail = Omit<ParishType, 'villages'> & {
    parish_name?: string;
    parish_slug?: string;
    villages?: VillageDetail[];
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

type RegionDetail = RegionType & {
    region_name?: string;
    region_slug?: string;
    country?: Country;
};

type DistrictDetail = Omit<DistrictType, 'counties'> & {
    district_name?: string;
    district_slug?: string;
    region?: RegionDetail;
    counties?: CountyDetail[];
};

const route = useRoute();
const geographyStore = useGeographyStore();

const districtId = computed(() => Number(route.params.id));

const district = ref<DistrictDetail | null>(null);
const isDistrictLoading = ref(false);
const isFarmersLoading = ref(false);
const errorMessage = ref<string | null>(null);
const farmersError = ref<string | null>(null);
const districtFarmers = ref<Farmer[]>([]);
const isMilkCentersLoading = ref(false);
const milkCentersError = ref<string | null>(null);
const milkCenters = ref<MilkCollectionCenter[]>([]);

const districtTitle = computed(() => district.value?.district_name ?? district.value?.name ?? 'District');
const districtSlug = computed(() => district.value?.district_slug ?? district.value?.slug ?? '—');

const regionId = computed(() => district.value?.region?.id ?? (district.value?.region as any)?.region_id);
const regionName = computed(() => district.value?.region?.region_name ?? (district.value?.region as any)?.name ?? '');
const regionLink = computed(() => (regionId.value ? `/admin/regions/${regionId.value}` : null));

const countryId = computed(() => district.value?.region?.country?.id ?? (district.value?.region?.country as any)?.country_id);
const countryName = computed(
    () => district.value?.region?.country?.name ?? (district.value?.region?.country as any)?.country_name ?? ''
);
const countryLink = computed(() => (countryId.value ? `/admin/countries/${countryId.value}` : null));

const districtCounties = computed<CountyDetail[]>(() => (district.value?.counties as CountyDetail[] ?? []));

const countyCount = computed(() => districtCounties.value.length);
const subcountyCount = computed(() =>
    districtCounties.value.reduce((total, county) => total + (county.subcounties?.length ?? 0), 0)
);
const parishCount = computed(() =>
    districtCounties.value.reduce((total, county) => {
        const parishTotal =
            county.subcounties?.reduce((subTotal, subcounty) => subTotal + (subcounty.parishes?.length ?? 0), 0) ?? 0;
        return total + parishTotal;
    }, 0)
);
const villageCount = computed(() =>
    districtCounties.value.reduce((total, county) => {
        const villageTotal =
            county.subcounties?.reduce((subTotal, subcounty) => {
                const villagesInSubcounty =
                    subcounty.parishes?.reduce((parishTotal, parish) => parishTotal + (parish.villages?.length ?? 0), 0) ??
                    0;
                return subTotal + villagesInSubcounty;
            }, 0) ?? 0;
        return total + villageTotal;
    }, 0)
);
const farmersCount = computed(() => districtFarmers.value.length);
const milkCentersCount = computed(() => milkCenters.value.length);

const districtIdentifiers = computed(() => {
    const identifiers = new Set<string>();
    if (!district.value) {
        return identifiers;
    }

    if (district.value.id != null) identifiers.add(String(district.value.id));
    if (district.value.district_name) identifiers.add(district.value.district_name);
    if (district.value.name) identifiers.add(district.value.name);
    return identifiers;
});

const lookupMaps = computed(() => {
    const county = new Map<string, string>();
    const subcounty = new Map<string, string>();
    const parish = new Map<string, string>();
    const village = new Map<string, string>();

    districtCounties.value.forEach((countyItem) => {
        const countyName = countyItem.county_name ?? countyItem.name ?? `County ${countyItem.id}`;
        if (countyItem.id != null) county.set(String(countyItem.id), countyName);
        if (countyItem.county_name) county.set(countyItem.county_name, countyName);
        if (countyItem.name) county.set(countyItem.name, countyName);

        countyItem.subcounties?.forEach((subcountyItem) => {
            const subcountyName = subcountyItem.subcounty_name ?? subcountyItem.name ?? `Subcounty ${subcountyItem.id}`;
            if (subcountyItem.id != null) subcounty.set(String(subcountyItem.id), subcountyName);
            if (subcountyItem.subcounty_name) subcounty.set(subcountyItem.subcounty_name, subcountyName);
            if (subcountyItem.name) subcounty.set(subcountyItem.name, subcountyName);

            subcountyItem.parishes?.forEach((parishItem) => {
                const parishName = parishItem.parish_name ?? parishItem.name ?? `Parish ${parishItem.id}`;
                if (parishItem.id != null) parish.set(String(parishItem.id), parishName);
                if (parishItem.parish_name) parish.set(parishItem.parish_name, parishName);
                if (parishItem.name) parish.set(parishItem.name, parishName);

                parishItem.villages?.forEach((villageItem) => {
                    if (!villageItem) return;
                    const villageName = villageItem.village_name ?? villageItem.name ?? `Village ${villageItem.id}`;
                    if (villageItem.id != null) village.set(String(villageItem.id), villageName);
                    if (villageItem.village_name) village.set(villageItem.village_name, villageName);
                    if (villageItem.name) village.set(villageItem.name, villageName);
                });
            });
        });
    });

    return { county, subcounty, parish, village };
});

const getCountyDisplayName = (county: CountyDetail) =>
    county.county_name ?? county.name ?? `County ${county.id}`;
const getCountySlug = (county: CountyDetail) =>
    county.county_slug ?? county.slug ?? '—';
const getSubcountyDisplayName = (subcounty: SubcountyDetail) =>
    subcounty.subcounty_name ?? subcounty.name ?? `Subcounty ${subcounty.id}`;
const getSubcountySlug = (subcounty: SubcountyDetail) =>
    subcounty.subcounty_slug ?? subcounty.slug ?? '—';
const getParishDisplayName = (parish: ParishDetail) =>
    parish.parish_name ?? parish.name ?? `Parish ${parish.id}`;
const getParishSlug = (parish: ParishDetail) =>
    parish.parish_slug ?? parish.slug ?? '—';

const resolveName = (value: string | undefined, map: Map<string, string>) => {
    if (value == null) return undefined;
    return map.get(String(value)) ?? value;
};

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

    const countyName = resolveName(loc.county_id != null ? String(loc.county_id) : undefined, lookupMaps.value.county);
    const subcountyName = resolveName(
        loc.subcounty_id != null ? String(loc.subcounty_id) : undefined,
        lookupMaps.value.subcounty
    );
    const parishName = resolveName(loc.parish_id != null ? String(loc.parish_id) : undefined, lookupMaps.value.parish);
    const villageName = resolveName(
        loc.village_id != null ? String(loc.village_id) : undefined,
        lookupMaps.value.village
    );

    if (countyName) segments.push(countyName);
    if (subcountyName) segments.push(subcountyName);
    if (parishName) segments.push(parishName);
    if (villageName) segments.push(villageName);

    return segments.length ? segments.join(' • ') : districtTitle.value;
};

const loadDistrict = async (id: number) => {
    isDistrictLoading.value = true;
    errorMessage.value = null;
    try {
        const data = await geographyStore.getDistrict(id);
        district.value = data as DistrictDetail;
    } catch (error) {
        errorMessage.value = geographyStore.error ?? 'Failed to load district details.';
    } finally {
        isDistrictLoading.value = false;
    }
};

const loadDistrictFarmers = async () => {
    if (!district.value) {
        districtFarmers.value = [];
        return;
    }

    const identifiers = districtIdentifiers.value;
    if (!identifiers.size) {
        districtFarmers.value = [];
        return;
    }

    const countryIdValue = countryId.value;
    if (!countryIdValue) {
        districtFarmers.value = [];
        return;
    }

    isFarmersLoading.value = true;
    farmersError.value = null;
    districtFarmers.value = [];

    try {
        const perPage = 100;
        let page = 1;
        let lastPage = 1;
        const seenFarmerIds = new Set<number>();

        do {
            const response = await geographyStore.getCountryFarmers(countryIdValue, page, perPage);
            const pageFarmers = response.data ?? [];
            const filtered = pageFarmers.filter((farmer) => {
                if (!farmer?.district) return false;
                const value = String(farmer.district);
                return identifiers.has(value);
            });

            filtered.forEach((farmer) => {
                if (farmer.id != null && seenFarmerIds.has(farmer.id)) return;
                districtFarmers.value.push(farmer);
                if (farmer.id != null) seenFarmerIds.add(farmer.id);
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

const loadDistrictMilkCenters = async () => {
    if (Number.isNaN(districtId.value)) {
        milkCenters.value = [];
        return;
    }

    isMilkCentersLoading.value = true;
    milkCentersError.value = null;

    try {
        const data = await geographyStore.getMilkCollectionCenters({ district_id: districtId.value });
        milkCenters.value = data;
    } catch (error) {
        milkCentersError.value = geographyStore.error ?? 'Failed to load milk collection centers.';
        milkCenters.value = [];
    } finally {
        isMilkCentersLoading.value = false;
    }
};

watch(
    districtId,
    async (newId) => {
        if (Number.isNaN(newId)) {
            errorMessage.value = 'Invalid district identifier.';
            return;
        }
        await loadDistrict(newId);
        await Promise.all([loadDistrictFarmers(), loadDistrictMilkCenters()]);
    },
    { immediate: true }
);
</script>

