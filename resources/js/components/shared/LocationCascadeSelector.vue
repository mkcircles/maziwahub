<template>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="space-y-1 sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Country
            </label>
            <select
                v-model.number="local.country_id"
                :disabled="isDisabled"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <option value="">Select country</option>
                <option
                    v-for="country in countries"
                    :key="country.id"
                    :value="country.id"
                >
                    {{ country.name }}
                </option>
            </select>
        </div>

        <div class="space-y-1 sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                District
            </label>
            <select
                v-model.number="local.district_id"
                :disabled="isDisabled || districts.length === 0"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <option value="">Select district</option>
                <option
                    v-for="district in districts"
                    :key="district.id"
                    :value="district.id"
                >
                    {{ district.district_name ?? (district as any).name }}
                </option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                County
            </label>
            <select
                v-model.number="local.county_id"
                :disabled="isDisabled || counties.length === 0"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <option value="">Select county</option>
                <option
                    v-for="county in counties"
                    :key="county.id"
                    :value="county.id"
                >
                    {{ county.county_name ?? (county as any).name }}
                </option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Subcounty
            </label>
            <select
                v-model.number="local.subcounty_id"
                :disabled="isDisabled || subcounties.length === 0"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <option value="">Select subcounty</option>
                <option
                    v-for="subcounty in subcounties"
                    :key="subcounty.id"
                    :value="subcounty.id"
                >
                    {{ subcounty.subcounty_name ?? (subcounty as any).name }}
                </option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Parish
            </label>
            <select
                v-model.number="local.parish_id"
                :disabled="isDisabled || parishes.length === 0"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <option value="">Select parish</option>
                <option
                    v-for="parish in parishes"
                    :key="parish.id"
                    :value="parish.id"
                >
                    {{ parish.parish_name ?? (parish as any).name }}
                </option>
            </select>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                Village
            </label>
            <select
                v-model.number="local.village_id"
                :disabled="isDisabled || villages.length === 0"
                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <option value="">Select village</option>
                <option
                    v-for="village in villages"
                    :key="village.id"
                    :value="village.id"
                >
                    {{ village.village_name ?? (village as any).name }}
                </option>
            </select>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import {
    useGeographyStore,
    type Country,
    type Region,
    type District,
    type County,
    type Subcounty,
    type Parish,
    type Village,
} from '../../stores/geographyStore';

export interface LocationSelection {
    country_id: number | null;
    country_name?: string;
    region_id: number | null;
    region_name?: string;
    district_id: number | null;
    district_name?: string;
    county_id: number | null;
    county_name?: string;
    subcounty_id: number | null;
    subcounty_name?: string;
    parish_id: number | null;
    parish_name?: string;
    village_id: number | null;
    village_name?: string;
}

function defaultSelection(): LocationSelection {
    return {
        country_id: null,
        country_name: '',
        region_id: null,
        region_name: '',
        district_id: null,
        district_name: '',
        county_id: null,
        county_name: '',
        subcounty_id: null,
        subcounty_name: '',
        parish_id: null,
        parish_name: '',
        village_id: null,
        village_name: '',
    };
}

const props = defineProps<{
    modelValue?: LocationSelection;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: LocationSelection): void;
}>();

const geographyStore = useGeographyStore();

const local = reactive<LocationSelection>({
    ...defaultSelection(),
    ...(props.modelValue ?? {}),
});

const isDisabled = computed(() => props.disabled ?? false);

const countries = ref<Country[]>([]);
const districts = ref<District[]>([]);
const counties = ref<County[]>([]);
const subcounties = ref<Subcounty[]>([]);
const parishes = ref<Parish[]>([]);
const villages = ref<Village[]>([]);
const regionLookup = ref(new Map<number, Region>());

const ensureCountriesLoaded = async () => {
    if (!geographyStore.countries.length) {
        await geographyStore.getCountries();
    }
    countries.value = geographyStore.countries;
};

const resetBelow = (level: 'country' | 'district' | 'county' | 'subcounty' | 'parish') => {
    if (['country'].includes(level)) {
        local.region_id = null;
        local.region_name = '';
        local.district_id = null;
        local.district_name = '';
        districts.value = [];
        regionLookup.value = new Map();
    }

    if (['country', 'district'].includes(level)) {
        local.county_id = null;
        local.county_name = '';
        counties.value = [];
    }

    if (['country', 'district', 'county'].includes(level)) {
        local.subcounty_id = null;
        local.subcounty_name = '';
        subcounties.value = [];
    }

    if (['country', 'district', 'county', 'subcounty'].includes(level)) {
        local.parish_id = null;
        local.parish_name = '';
        parishes.value = [];
    }

    if (['country', 'district', 'county', 'subcounty', 'parish'].includes(level)) {
        local.village_id = null;
        local.village_name = '';
        villages.value = [];
    }
};

const parseId = (value: number | null | undefined | '' | string) => {
    if (value === '' || value === null || value === undefined) return null;
    const parsed = Number(value);
    return Number.isNaN(parsed) ? null : parsed;
};

const loadDistrictsForCountry = async (countryId: number) => {
    regionLookup.value = new Map();
    const aggregated: District[] = [];

    const regions = await geographyStore.getRegions(countryId);
    if (regions && regions.length) {
        for (const region of regions) {
            const regionDistricts = await geographyStore.getDistricts(region.id);
            regionDistricts.forEach((district) => {
                regionLookup.value.set(district.id, region);
            });
            aggregated.push(...regionDistricts);
        }
    }

    districts.value = aggregated;
};

const loadCountiesForDistrict = async (districtId: number) => {
    const list = await geographyStore.getCounties(districtId);
    counties.value = list ?? [];
};

const loadSubcountiesForCounty = async (countyId: number) => {
    const list = await geographyStore.getSubcounties(countyId);
    subcounties.value = list ?? [];
};

const loadParishesForSubcounty = async (subcountyId: number) => {
    const list = await geographyStore.getParishes(subcountyId);
    parishes.value = list ?? [];
};

const loadVillagesForParish = async (parishId: number) => {
    const list = await geographyStore.getVillages(parishId);
    villages.value = list ?? [];
};

watch(
    () => props.modelValue,
    (value) => {
        Object.assign(local, defaultSelection(), value ?? {});
    },
    { deep: true }
);

watch(
    local,
    (value) => {
        emit('update:modelValue', { ...value });
    },
    { deep: true }
);

watch(
    () => local.country_id,
    async (countryId) => {
        resetBelow('country');
        const id = parseId(countryId);
        if (!id) return;

        const selected = countries.value.find((country) => country.id === id);
        local.country_name = selected?.name ?? '';

        await loadDistrictsForCountry(id);
    }
);

watch(
    () => local.district_id,
    async (districtId) => {
        resetBelow('district');
        const id = parseId(districtId);
        if (!id) {
            local.district_name = '';
            local.region_id = null;
            local.region_name = '';
            return;
        }

        const selected = districts.value.find((district) => district.id === id);
        local.district_name = (selected as any)?.district_name ?? selected?.name ?? '';

        const region = regionLookup.value.get(id);
        if (region) {
            local.region_id = region.id;
            local.region_name = region.name ?? (region as any)?.region_name ?? '';
        } else {
            local.region_id = null;
            local.region_name = '';
        }

        await loadCountiesForDistrict(id);
    }
);

watch(
    () => local.county_id,
    async (countyId) => {
        resetBelow('county');
        const id = parseId(countyId);
        if (!id) {
            local.county_name = '';
            return;
        }

        const selected = counties.value.find((county) => county.id === id);
        local.county_name = (selected as any)?.county_name ?? selected?.name ?? '';

        await loadSubcountiesForCounty(id);
    }
);

watch(
    () => local.subcounty_id,
    async (subcountyId) => {
        resetBelow('subcounty');
        const id = parseId(subcountyId);
        if (!id) {
            local.subcounty_name = '';
            return;
        }

        const selected = subcounties.value.find((subcounty) => subcounty.id === id);
        local.subcounty_name = (selected as any)?.subcounty_name ?? selected?.name ?? '';

        await loadParishesForSubcounty(id);
    }
);

watch(
    () => local.parish_id,
    async (parishId) => {
        resetBelow('parish');
        const id = parseId(parishId);
        if (!id) {
            local.parish_name = '';
            return;
        }

        const selected = parishes.value.find((parish) => parish.id === id);
        local.parish_name = (selected as any)?.parish_name ?? selected?.name ?? '';

        await loadVillagesForParish(id);
    }
);

watch(
    () => local.village_id,
    (villageId) => {
        const id = parseId(villageId);
        if (!id) {
            local.village_name = '';
            return;
        }
        const selected = villages.value.find((village) => village.id === id);
        local.village_name = (selected as any)?.village_name ?? selected?.name ?? '';
    }
);

ensureCountriesLoaded();
</script>


