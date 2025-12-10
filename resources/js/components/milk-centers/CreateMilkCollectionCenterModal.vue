<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-[9999] overflow-y-auto"
                role="dialog"
                aria-modal="true"
                @click.self="handleClose"
            >
                <div class="fixed inset-0 bg-gray-800/60" @click="handleClose"></div>

                <div class="relative z-10 flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="transition ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <div
                            v-if="isOpen"
                            class="inline-block w-full max-w-3xl transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle"
                        >
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="flex items-start justify-between border-b border-gray-100 px-6 py-4">
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900">Add Milk Collection Center</h2>
                                        <p class="text-sm text-gray-500">
                                            Register a new collection center with infrastructure details.
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        class="rounded-full p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none"
                                        @click="handleClose"
                                    >
                                        <Icon icon="mdi:close" :size="20" />
                                    </button>
                                </div>

                                <div class="space-y-6 px-6">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Center Name <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model.trim="form.name"
                                                type="text"
                                                required
                                                placeholder="Enter center name"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Registration #
                                            </label>
                                            <input
                                                v-model.trim="form.registration_number"
                                                type="text"
                                                placeholder="Registration identifier"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Established Date
                                            </label>
                                            <input
                                                v-model="form.established_date"
                                                type="date"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Manager Name
                                            </label>
                                            <input
                                                v-model.trim="form.manager_name"
                                                type="text"
                                                placeholder="Manager full name"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Manager Phone
                                            </label>
                                            <input
                                                v-model.trim="form.manager_phone"
                                                type="tel"
                                                placeholder="Contact number"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Staff Count
                                            </label>
                                            <input
                                                v-model.number="form.staff_count"
                                                type="number"
                                                min="0"
                                                placeholder="Number of staff"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Cooler Capacity (Liters)
                                            </label>
                                            <input
                                                v-model.number="form.cooler_capacity_liters"
                                                type="number"
                                                min="0"
                                                step="1"
                                                placeholder="Daily cooling capacity"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Power Source
                                            </label>
                                            <select
                                                v-model="form.power_source"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select power source</option>
                                                <option value="grid">Grid</option>
                                                <option value="solar">Solar</option>
                                                <option value="generator">Generator</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Physical Address <span class="text-red-500">*</span>
                                            </label>
                                            <textarea
                                                v-model.trim="form.physical_address"
                                                rows="2"
                                                required
                                                placeholder="Street, district, additional directions"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Latitude
                                            </label>
                                            <input
                                                v-model.number="form.latitude"
                                                type="number"
                                                step="0.000001"
                                                placeholder="Latitude"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Longitude
                                            </label>
                                            <input
                                                v-model.number="form.longitude"
                                                type="number"
                                                step="0.000001"
                                                placeholder="Longitude"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Location Hierarchy
                                        </label>
                                        <LocationCascadeSelector
                                            v-model="locationSelection"
                                            :disabled="submitting"
                                        />
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700">
                                            <input
                                                v-model="form.has_testing_equipment"
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            Testing Equipment Available
                                        </label>
                                        <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700">
                                            <input
                                                v-model="form.has_washing_bay"
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            Washing Bay Available
                                        </label>
                                    </div>
                                </div>

                                <div v-if="errorMessage" class="px-6">
                                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                        {{ errorMessage }}
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white disabled:cursor-not-allowed disabled:opacity-50"
                                        @click="handleClose"
                                        :disabled="submitting"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="submitting"
                                    >
                                        <Icon icon="mdi:warehouse" :size="18" />
                                        <span>{{ submitting ? 'Saving...' : 'Save Center' }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { computed, reactive, watch, ref } from 'vue';
import axios from 'axios';
import Icon from '../../components/shared/Icon.vue';
import LocationCascadeSelector, { type LocationSelection } from '../shared/LocationCascadeSelector.vue';

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const submitting = ref(false);
const errorMessage = ref<string | null>(null);

const form = reactive({
    name: '',
    registration_number: '',
    physical_address: '',
    latitude: '' as number | '' | null,
    longitude: '' as number | '' | null,
    established_date: '',
    manager_name: '',
    manager_phone: '',
    staff_count: '' as number | '' | null,
    power_source: '',
    cooler_capacity_liters: '' as number | '' | null,
    has_testing_equipment: false,
    has_washing_bay: false,
});

let locationSelection = reactive<LocationSelection>({
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
});

const resetForm = () => {
    Object.assign(form, {
        name: '',
        registration_number: '',
        physical_address: '',
        latitude: '',
        longitude: '',
        established_date: '',
        manager_name: '',
        manager_phone: '',
        staff_count: '',
        power_source: '',
        cooler_capacity_liters: '',
        has_testing_equipment: false,
        has_washing_bay: false,
    });
    Object.assign(locationSelection, {
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
    });
    errorMessage.value = null;
};

const handleClose = () => {
    if (submitting.value) return;
    resetForm();
    emit('close');
};

const toNullableNumber = (value: number | '' | null) => {
    if (value === '' || value === null || value === undefined) return null;
    const parsed = Number(value);
    return Number.isNaN(parsed) ? null : parsed;
};

const buildLocationPayload = () => {
    const {
        country_id,
        region_id,
        district_id,
        county_id,
        subcounty_id,
        parish_id,
        village_id,
    } = locationSelection;

    if (
        country_id === null &&
        region_id === null &&
        district_id === null &&
        county_id === null &&
        subcounty_id === null &&
        parish_id === null &&
        village_id === null
    ) {
        return null;
    }

    return {
        country_id,
        region_id,
        district_id,
        county_id,
        subcounty_id,
        parish_id,
        village_id,
    };
};

const handleSubmit = async () => {
    submitting.value = true;
    errorMessage.value = null;

    try {
        await axios.post('/milk-collection-centers', {
            name: form.name,
            registration_number: form.registration_number || null,
            physical_address: form.physical_address,
            latitude: toNullableNumber(form.latitude),
            longitude: toNullableNumber(form.longitude),
            established_date: form.established_date || null,
            manager_name: form.manager_name || null,
            manager_phone: form.manager_phone || null,
            staff_count: toNullableNumber(form.staff_count),
            power_source: form.power_source || null,
            cooler_capacity_liters: toNullableNumber(form.cooler_capacity_liters),
            has_testing_equipment: form.has_testing_equipment,
            has_washing_bay: form.has_washing_bay,
            location: buildLocationPayload(),
        });

        emit('created');
        handleClose();
    } catch (err: any) {
        errorMessage.value = err.response?.data?.message || 'Failed to create milk collection center.';
    } finally {
        submitting.value = false;
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            resetForm();
        }
    }
);
</script>
