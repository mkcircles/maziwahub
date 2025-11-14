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
                <div class="fixed inset-0 bg-gray-500/65 transition-opacity" @click="handleClose"></div>

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
                                        <h2 class="text-lg font-semibold text-gray-900">Register New Farmer</h2>
                                        <p class="text-sm text-gray-500">
                                            Capture the farmer’s personal details and link them to a milk collection center.
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

                                <div class="grid gap-6 px-6">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                First Name <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model="form.first_name"
                                                type="text"
                                                required
                                                placeholder="Enter first name"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Last Name <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model="form.last_name"
                                                type="text"
                                                required
                                                placeholder="Enter last name"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Phone Number
                                            </label>
                                            <input
                                                v-model="form.phone_number"
                                                type="tel"
                                                placeholder="Enter phone number"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Gender
                                            </label>
                                            <select
                                                v-model="form.gender"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Status
                                            </label>
                                            <select
                                                v-model="form.status"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="pending">Pending</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Registration Type
                                            </label>
                                            <select
                                                v-model="form.reg_type"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select type</option>
                                                <option value="individual">Individual</option>
                                                <option value="cooperative">Cooperative</option>
                                                <option value="association">Association</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Milk Collection Center
                                            </label>
                                            <select
                                                v-model.number="form.milk_collection_center_id"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select center</option>
                                                <option
                                                    v-for="center in milkCenters"
                                                    :key="center.id"
                                                    :value="center.id"
                                                >
                                                    {{ center.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <LocationCascadeSelector
                                        v-model="locationSelection"
                                        :disabled="creating"
                                    />

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Land Ownership
                                            </label>
                                            <input
                                                v-model="form.land_ownership"
                                                type="text"
                                                placeholder="e.g. Owned, Leased"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Farming Type
                                            </label>
                                            <input
                                                v-model="form.farming_type"
                                                type="text"
                                                placeholder="e.g. Mixed, Crop"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Crop Production
                                            </label>
                                            <input
                                                v-model="form.crop_production"
                                                type="text"
                                                placeholder="Main crops"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Animal Production
                                            </label>
                                            <input
                                                v-model="form.animal_production"
                                                type="text"
                                                placeholder="Livestock details"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div v-if="createError" class="px-6">
                                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                        {{ createError }}
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white"
                                        @click="handleClose"
                                        :disabled="creating"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="creating"
                                    >
                                        <Icon icon="mdi:content-save" :size="18" />
                                        <span>{{ creating ? 'Saving...' : 'Register Farmer' }}</span>
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
import { computed, reactive, watch } from 'vue';
import Icon from '../shared/Icon.vue';
import LocationCascadeSelector, { type LocationSelection } from '../shared/LocationCascadeSelector.vue';
import { useFarmerStore } from '../../stores/farmerStore';
import type { MilkCollectionCenter } from '../../stores/geographyStore';

const props = defineProps<{
    isOpen: boolean;
    milkCenters: MilkCollectionCenter[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created', farmerId?: number | string): void;
}>();

const farmerStore = useFarmerStore();
const creating = computed(() => farmerStore.creating);
const createError = computed(() => farmerStore.createError);

const defaultLocation = (): LocationSelection => ({
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

const locationSelection = reactive<LocationSelection>(defaultLocation());

const form = reactive({
    first_name: '',
    last_name: '',
    phone_number: '',
    gender: '',
    status: 'pending',
    reg_type: '',
    milk_collection_center_id: '' as number | '' | null,
    district: '',
    county: '',
    sub_county: '',
    parish: '',
    village: '',
    land_ownership: '',
    farming_type: '',
    crop_production: '',
    animal_production: '',
});

const resetForm = () => {
    Object.assign(form, {
        first_name: '',
        last_name: '',
        phone_number: '',
        gender: '',
        status: 'pending',
        reg_type: '',
        milk_collection_center_id: '',
        district: '',
        county: '',
        sub_county: '',
        parish: '',
        village: '',
        land_ownership: '',
        farming_type: '',
        crop_production: '',
        animal_production: '',
    });
    Object.assign(locationSelection, defaultLocation());
};

const handleClose = () => {
    if (creating.value) return;
    resetForm();
    emit('close');
};

const handleSubmit = async () => {
    try {
        const payload = {
            ...form,
            milk_collection_center_id:
                form.milk_collection_center_id === '' ? null : Number(form.milk_collection_center_id),
        };

        const farmer = await farmerStore.createFarmer(payload);
        resetForm();
        emit('created', farmer?.id);
    } catch {
        // error handled via store createError
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            farmerStore.createError = null;
        } else {
            resetForm();
        }
    }
);

watch(
    locationSelection,
    (value) => {
        form.district = value.district_name ?? '';
        form.county = value.county_name ?? '';
        form.sub_county = value.subcounty_name ?? '';
        form.parish = value.parish_name ?? '';
        form.village = value.village_name ?? '';
    },
    { deep: true }
);
</script>

