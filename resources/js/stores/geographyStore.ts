import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export interface Country {
    id: number;
    name: string;
    iso_code: string;
    slug: string;
    regions?: Region[];
}

export interface Region {
    id: number;
    name: string;
    slug: string;
    country_id: number;
    districts?: District[];
}

export interface District {
    id: number;
    name: string;
    slug: string;
    district_code: string;
    region_id: number;
    counties?: County[];
}

export interface County {
    id: number;
    name: string;
    slug: string;
    county_code: string;
    district_id: number;
    subcounties?: Subcounty[];
}

export interface Subcounty {
    id: number;
    name: string;
    slug: string;
    subcounty_code: string;
    county_id: number;
    parishes?: Parish[];
}

export interface Parish {
    id: number;
    name: string;
    slug: string;
    parish_code: string;
    subcounty_id: number;
    villages?: Village[];
}

export interface Village {
    id: number;
    name: string;
    slug: string;
    village_code: string;
    parish_id: number;
}

export interface MilkDelivery {
    id: number;
    farmer_id: number;
    milk_collection_center_id: number;
    delivery_date: string;
    volume_liters: number;
    quality_grade?: string | null;
    price_per_liter?: number | null;
    total_amount?: number | null;
    recorded_by?: string | null;
    notes?: string | null;
    milk_collection_center?: MilkCollectionCenter | null;
}

export interface CowMilkProduction {
    id: number;
    cow_id: number;
    recorded_date: string;
    morning_volume_liters?: number | null;
    midday_volume_liters?: number | null;
    evening_volume_liters?: number | null;
    total_volume_liters: number;
    recorded_by?: string | null;
    notes?: string | null;
}

export interface Cow {
    id: number;
    farmer_id: number;
    tag_number: string;
    breed?: string | null;
    date_of_birth?: string | null;
    acquired_date?: string | null;
    health_status?: string | null;
    milk_capacity_liters?: number | null;
    notes?: string | null;
    milk_productions?: CowMilkProduction[];
    milkProductions?: CowMilkProduction[];
    farmer?: Farmer | null;
    treatments?: CowTreatment[];
    created_at?: string;
    updated_at?: string;
}

export interface FeedingMethod {
    id: number;
    name: string;
    code: string;
    category?: string | null;
    description?: string | null;
    requires_details: boolean;
    is_active: boolean;
    metadata?: Record<string, any> | null;
    created_at?: string;
    updated_at?: string;
}

export interface FarmerFeedingHistory {
    id: number;
    farmer_id: number;
    feeding_method_id?: number | null;
    feeding_type: 'primary' | 'supplemental' | 'other';
    started_at?: string | null;
    ended_at?: string | null;
    notes?: string | null;
    metadata?: Record<string, any> | null;
    recorded_by_id?: number | null;
    created_at?: string;
    updated_at?: string;
    feedingMethod?: FeedingMethod | null;
    recordedBy?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

export interface Farmer {
    id: number;
    farmer_id: string;
    first_name: string;
    last_name: string;
    phone_number?: string;
    district?: string;
    county?: string;
    sub_county?: string;
    parish?: string;
    village?: string;
    status?: string;
    reg_type?: string;
    milk_collection_center_id?: number | null;
    registered_by_agent_id?: number | null;
    household_head?: boolean | null;
    family_size?: number | null;
    above_18?: number | null;
    below_5?: number | null;
    farm_size?: number | null;
    land_under_use?: number | null;
    is_farmer_insured?: boolean | null;
    support_needed?: string | null;
    herd_size?: number | null;
    grazing_type?: string | null;
    water_source?: string | null;
    primary_feeding_method_id?: number | null;
    supplemental_feeding_method_id?: number | null;
    feeding_notes?: string | null;
    feeding_metadata?: Record<string, any> | null;
    feeding_last_changed_at?: string | null;
    primaryFeedingMethod?: FeedingMethod | null;
    supplementalFeedingMethod?: FeedingMethod | null;
    feedingHistories?: FarmerFeedingHistory[] | null;
    milkCollectionCenter?: MilkCollectionCenter | null;
    registeredByAgent?: any;
    cows?: Cow[];
    milkDeliveries?: MilkDelivery[];
    milk_deliveries?: MilkDelivery[];
    cowMilkProductions?: CowMilkProduction[];
    cow_milk_productions?: CowMilkProduction[];
    [key: string]: any;
}

export interface MilkCollectionCenter {
    id: number;
    name: string;
    registration_number?: string | null;
    physical_address: string;
    latitude?: number | null;
    longitude?: number | null;
    location?: {
        country_id?: number | null;
        region_id?: number | null;
        district_id?: number | null;
        county_id?: number | null;
        subcounty_id?: number | null;
        parish_id?: number | null;
        village_id?: number | null;
    } | null;
    established_date?: string | null;
    manager_name?: string | null;
    manager_phone?: string | null;
    staff_count?: number | null;
    power_source?: string | null;
    cooler_capacity_liters?: number | null;
    has_testing_equipment?: boolean;
    has_washing_bay?: boolean;
}

export interface Vet {
    id: number;
    first_name: string;
    last_name: string;
    license_number: string;
    license_expiry_date?: string | null;
    phone_number?: string | null;
    email?: string | null;
    specialization?: string | null;
    employer?: string | null;
    milk_collection_center_id?: number | null;
    bio?: string | null;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
    milkCollectionCenter?: MilkCollectionCenter | null;
    treatments?: CowTreatment[];
}

export interface CowTreatment {
    id: number;
    cow_id: number;
    vet_id?: number | null;
    recorded_by_id?: number | null;
    treatment_date: string;
    diagnosis?: string | null;
    reason?: string | null;
    medication?: string | null;
    dosage?: string | null;
    dosage_unit?: string | null;
    route?: string | null;
    follow_up_date?: string | null;
    status?: string | null;
    outcome?: string | null;
    next_steps?: string | null;
    cost?: number | null;
    notes?: string | null;
    attachment_path?: string | null;
    life_cycle_status?: string | null;
    cow?: Cow | null;
    vet?: Vet | null;
    recordedBy?: any;
}

export const useGeographyStore = defineStore('geography', () => {
    const countries = ref<Country[]>([]);
    const regions = ref<Region[]>([]);
    const districts = ref<District[]>([]);
    const counties = ref<County[]>([]);
    const subcounties = ref<Subcounty[]>([]);
    const parishes = ref<Parish[]>([]);
    const villages = ref<Village[]>([]);
    const countryFarmers = ref<{ data: Farmer[]; current_page: number; last_page: number; per_page: number; total: number } | null>(null);
    const milkCenters = ref<MilkCollectionCenter[]>([]);

    const loading = ref(false);
    const error = ref<string | null>(null);

    // Countries
    async function getCountries() {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get<Country[]>('/countries');
            countries.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch countries';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function getCountry(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get<Country>(`/countries/${id}`);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch country';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createCountry(data: { name: string; iso_code: string }) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.post<Country>('/countries', data);
            await getCountries(); // Refresh the list
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to create country';
            if (err.response?.data?.errors) {
                const errors = err.response.data.errors;
                error.value = Object.values(errors).flat().join(', ');
            }
            throw err;
        } finally {
            loading.value = false;
        }
    }


    async function getCountryFarmers(countryId: number, page: number = 1, perPage: number = 15) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get<{ data: Farmer[]; current_page: number; last_page: number; per_page: number; total: number }>(
                `/countries/${countryId}/farmers`,
                {
                    params: { page, per_page: perPage }
                }
            );
            countryFarmers.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch country farmers';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteCountry(id: number) {
        loading.value = true;
        error.value = null;
        try {
            await axios.delete(`/countries/${id}`);
            await getCountries();
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to delete country';
            throw err;
        }
    }

    async function getMilkCollectionCenters(params: Record<string, any> = {}) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get<MilkCollectionCenter[]>('/milk-collection-centers', {
                params,
            });
            milkCenters.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch milk collection centers';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateMilkCollectionCenter(id: number, data: Record<string, any>) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.put<MilkCollectionCenter>(`/milk-collection-centers/${id}`, data);

            // Update local state if the center exists in the list
            const index = milkCenters.value.findIndex(c => c.id === id);
            if (index !== -1) {
                milkCenters.value[index] = response.data;
            }

            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to update milk collection center';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    // Regions
    async function getRegions(countryId?: number) {
        loading.value = true;
        error.value = null;
        try {
            const url = countryId
                ? `/countries/${countryId}/regions`
                : '/regions';
            const response = await axios.get<Region[]>(url);
            regions.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch regions';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function getRegion(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get<Region>(`/regions/${id}`);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch region';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    // Districts
    async function getDistricts(regionId?: number) {
        loading.value = true;
        error.value = null;
        try {
            const url = regionId
                ? `/regions/${regionId}/districts`
                : '/districts';
            const response = await axios.get<District[]>(url);
            districts.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch districts';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function getDistrict(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get<District>(`/districts/${id}`);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch district';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    // Counties
    async function getCounties(districtId?: number) {
        loading.value = true;
        error.value = null;
        try {
            const url = districtId
                ? `/districts/${districtId}/counties`
                : '/counties';
            const response = await axios.get<County[]>(url);
            counties.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch counties';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    // Subcounties
    async function getSubcounties(countyId?: number) {
        loading.value = true;
        error.value = null;
        try {
            const url = countyId
                ? `/counties/${countyId}/subcounties`
                : '/subcounties';
            const response = await axios.get<Subcounty[]>(url);
            subcounties.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch subcounties';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    // Parishes
    async function getParishes(subcountyId?: number) {
        loading.value = true;
        error.value = null;
        try {
            const url = subcountyId
                ? `/subcounties/${subcountyId}/parishes`
                : '/parishes';
            const response = await axios.get<Parish[]>(url);
            parishes.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch parishes';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    // Villages
    async function getVillages(parishId?: number) {
        loading.value = true;
        error.value = null;
        try {
            const url = parishId
                ? `/parishes/${parishId}/villages`
                : '/villages';
            const response = await axios.get<Village[]>(url);
            villages.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch villages';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    return {
        countries,
        regions,
        districts,
        counties,
        subcounties,
        parishes,
        villages,
        countryFarmers,
        milkCenters,
        loading,
        error,
        getCountries,
        getCountry,
        createCountry,
        getCountryFarmers,
        deleteCountry,
        getRegions,
        getRegion,
        getDistricts,
        getDistrict,
        getCounties,
        getSubcounties,
        getParishes,
        getVillages,
        getMilkCollectionCenters,
        updateMilkCollectionCenter,
        clearError,
    };
});

// Export individual stores for convenience
export const useCountriesStore = () => useGeographyStore();
export const useRegionsStore = () => useGeographyStore();
export const useDistrictsStore = () => useGeographyStore();
export const useCountiesStore = () => useGeographyStore();
export const useSubcountiesStore = () => useGeographyStore();
export const useParishesStore = () => useGeographyStore();
export const useVillagesStore = () => useGeographyStore();

