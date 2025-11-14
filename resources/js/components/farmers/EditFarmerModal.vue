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
                v-if="isOpen && farmer"
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
                            v-if="isOpen && farmer"
                            class="inline-block w-full max-w-4xl transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle"
                        >
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="flex items-start justify-between border-b border-gray-100 px-6 py-4">
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900">Edit Farmer Profile</h2>
                                        <p class="text-sm text-gray-500">
                                            Update the farmer’s complete profile, household data, and operational details.
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
                                    <section class="space-y-4">
                                    <h3 class="text-xs font-semibold uppercase tracking-wide text-sky-500">Basic Details</h3>
                                    <hr class="border-b border-gray-200" />
                                        <div class="grid gap-4 sm:grid-cols-3">
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
                                                    Farmer ID
                                                </label>
                                                <input
                                                    v-model="form.farmer_id"
                                                    type="text"
                                                    placeholder="e.g. AB1234"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Date of Birth
                                                </label>
                                                <input
                                                    v-model="form.dob"
                                                    type="date"
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
                                                    Educational Level
                                                </label>
                                                <input
                                                    v-model="form.educational_level"
                                                    type="text"
                                                    placeholder="e.g. Secondary, Diploma"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
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
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Registered By
                                                </label>
                                                <input
                                                    v-model="form.registered_by"
                                                    type="text"
                                                    placeholder="Registrar name"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Registered By Agent ID
                                                </label>
                                                <input
                                                    v-model="form.registered_by_agent_id"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Agent identifier"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                        </div>
                                    </section>

                                    <section class="space-y-4">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-sky-500">Contact & Identification</h3>
                                        <hr class="border-b border-gray-200" />
                                        <div class="grid gap-4 sm:grid-cols-3">
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
                                                    National ID Number
                                                </label>
                                                <input
                                                    v-model="form.id_number"
                                                    type="text"
                                                    placeholder="Enter ID number"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Marital Status
                                                </label>
                                                <input
                                                    v-model="form.marital_status"
                                                    type="text"
                                                    placeholder="e.g. Married, Single"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Next of Kin
                                                </label>
                                                <input
                                                    v-model="form.next_of_kin"
                                                    type="text"
                                                    placeholder="Full name"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Next of Kin Contact
                                                </label>
                                                <input
                                                    v-model="form.next_of_kin_contact"
                                                    type="text"
                                                    placeholder="Contact details"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Relationship
                                                </label>
                                                <input
                                                    v-model="form.next_of_kin_relationship"
                                                    type="text"
                                                    placeholder="Relationship"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                        </div>
                                    </section>

                                   
                                    <section class="space-y-4">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-sky-500">Household & Demographics</h3>
                                        <hr class="border-b border-gray-200" />
                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Household Members
                                                </label>
                                                <input
                                                    v-model="form.no_of_household_members"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Total household members"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Family Size
                                                </label>
                                                <input
                                                    v-model="form.family_size"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Family size"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Male Members
                                                </label>
                                                <input
                                                    v-model="form.male_members"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Number of males"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Female Members
                                                </label>
                                                <input
                                                    v-model="form.female_members"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Number of females"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Adults (18+)
                                                </label>
                                                <input
                                                    v-model="form.above_18"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Members above 18"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Children (&lt;5 years)
                                                </label>
                                                <input
                                                    v-model="form.below_5"
                                                    type="number"
                                                    min="0"
                                                    placeholder="Members below 5"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                                    <input
                                                        v-model="form.household_head"
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    />
                                                    Household Head
                                                </label>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="space-y-4">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-sky-500">Milk Production & Resources</h3>
                                        <hr class="border-b border-gray-200" />
                                        <div class="grid gap-4 sm:grid-cols-4">
                                            <div class="space-y-1 sm:col-span-2">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Milk Collection Center
                                                </label>
                                                <select
                                                    v-model="form.milk_collection_center_id"
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
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Financial Instrument
                                                </label>
                                                <input
                                                    v-model="form.financial_instrument"
                                                    type="text"
                                                    placeholder="e.g. Bank account, Mobile money"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Available Energy Source
                                                </label>
                                                <input
                                                    v-model="form.available_energy_source"
                                                    type="text"
                                                    placeholder="e.g. Grid, Solar"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Farm Size (acres)
                                                </label>
                                                <input
                                                    v-model="form.farm_size"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="Total farm size"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Land Under Use (acres)
                                                </label>
                                                <input
                                                    v-model="form.land_under_use"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="Land under production"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
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
                                            <div class="sm:col-span-2">
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                                    <input
                                                        v-model="form.is_farmer_insured"
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    />
                                                    Farmer is insured
                                                </label>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="space-y-4">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-sky-500">Additional Information</h3>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            
                                            <div class="space-y-1 sm:col-span-2">
                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    Support Needed
                                                </label>
                                                <textarea
                                                    v-model="form.support_needed"
                                                    rows="3"
                                                    placeholder="Describe support needs"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <div v-if="updateError" class="px-6">
                                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                        {{ updateError }}
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white disabled:cursor-not-allowed disabled:opacity-50"
                                        @click="handleClose"
                                        :disabled="updating"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="updating"
                                    >
                                        <Icon icon="mdi:content-save" :size="18" />
                                        <span>{{ updating ? 'Saving...' : 'Update Farmer' }}</span>
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
import { reactive, watch } from 'vue';
import { storeToRefs } from 'pinia';
import Icon from '../shared/Icon.vue';
import { useFarmerStore } from '../../stores/farmerStore';
import type { Farmer, MilkCollectionCenter } from '../../stores/geographyStore';

const props = defineProps<{
    isOpen: boolean;
    farmer: Farmer | null;
    milkCenters: MilkCollectionCenter[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'updated', farmerId?: number | string): void;
}>();

const farmerStore = useFarmerStore();
const { updating, updateError } = storeToRefs(farmerStore);

const defaultForm = () => ({
    farmer_id: '',
    first_name: '',
    last_name: '',
    dob: '',
    gender: '',
    educational_level: '',
    phone_number: '',
    id_number: '',
    marital_status: '',
    district: '',
    county: '',
    sub_county: '',
    parish: '',
    village: '',
    next_of_kin: '',
    next_of_kin_contact: '',
    next_of_kin_relationship: '',
    no_of_household_members: '',
    registered_by: '',
    registered_by_agent_id: '',
    milk_collection_center_id: '' as number | '' | null,
    photo: '',
    coordinates_latitude: '',
    coordinates_longitude: '',
    status: 'pending',
    validation_reason: '',
    reg_type: '',
    household_head: false,
    family_size: '',
    male_members: '',
    female_members: '',
    above_18: '',
    below_5: '',
    financial_instrument: '',
    available_energy_source: '',
    farm_size: '',
    land_under_use: '',
    land_ownership: '',
    farming_type: '',
    crop_production: '',
    animal_production: '',
    is_farmer_insured: false,
    support_needed: '',
});

const form = reactive(defaultForm());

const resetForm = () => {
    Object.assign(form, defaultForm());
};

const populateFromFarmer = (farmer: Farmer) => {
    const anyFarmer = farmer as Record<string, any>;
    const coordinates = (anyFarmer.coordinates ?? {}) as Record<string, any>;

    form.farmer_id = anyFarmer.farmer_id ?? '';
    form.first_name = anyFarmer.first_name ?? '';
    form.last_name = anyFarmer.last_name ?? '';
    form.dob = anyFarmer.dob ?? '';
    form.gender = anyFarmer.gender ?? '';
    form.educational_level = anyFarmer.educational_level ?? '';
    form.phone_number = anyFarmer.phone_number ?? '';
    form.id_number = anyFarmer.id_number ?? '';
    form.marital_status = anyFarmer.marital_status ?? '';
    form.district = anyFarmer.district ?? '';
    form.county = anyFarmer.county ?? '';
    form.sub_county = anyFarmer.sub_county ?? '';
    form.parish = anyFarmer.parish ?? '';
    form.village = anyFarmer.village ?? '';
    form.next_of_kin = anyFarmer.next_of_kin ?? '';
    form.next_of_kin_contact = anyFarmer.next_of_kin_contact ?? '';
    form.next_of_kin_relationship = anyFarmer.next_of_kin_relationship ?? '';
    form.no_of_household_members =
        anyFarmer.no_of_household_members != null ? String(anyFarmer.no_of_household_members) : '';
    form.registered_by = anyFarmer.registered_by ?? '';
    form.registered_by_agent_id =
        anyFarmer.registered_by_agent_id != null ? String(anyFarmer.registered_by_agent_id) : '';
    form.milk_collection_center_id = anyFarmer.milk_collection_center_id ?? '';
    form.photo = anyFarmer.photo ?? '';
    form.coordinates_latitude = coordinates.latitude != null ? String(coordinates.latitude) : '';
    form.coordinates_longitude = coordinates.longitude != null ? String(coordinates.longitude) : '';
    form.status = anyFarmer.status ?? 'pending';
    form.validation_reason = anyFarmer.validation_reason ?? '';
    form.reg_type = anyFarmer.reg_type ?? '';
    form.household_head = Boolean(anyFarmer.household_head);
    form.family_size = anyFarmer.family_size != null ? String(anyFarmer.family_size) : '';
    form.male_members = anyFarmer.male_members != null ? String(anyFarmer.male_members) : '';
    form.female_members = anyFarmer.female_members != null ? String(anyFarmer.female_members) : '';
    form.above_18 = anyFarmer.above_18 != null ? String(anyFarmer.above_18) : '';
    form.below_5 = anyFarmer.below_5 != null ? String(anyFarmer.below_5) : '';
    form.financial_instrument = anyFarmer.financial_instrument ?? '';
    form.available_energy_source = anyFarmer.available_energy_source ?? '';
    form.farm_size = anyFarmer.farm_size != null ? String(anyFarmer.farm_size) : '';
    form.land_under_use = anyFarmer.land_under_use != null ? String(anyFarmer.land_under_use) : '';
    form.land_ownership = anyFarmer.land_ownership ?? '';
    form.farming_type = anyFarmer.farming_type ?? '';
    form.crop_production = anyFarmer.crop_production ?? '';
    form.animal_production = anyFarmer.animal_production ?? '';
    form.is_farmer_insured = Boolean(anyFarmer.is_farmer_insured);
    form.support_needed = anyFarmer.support_needed ?? '';
};

const handleClose = () => {
    if (updating.value) return;
    updateError.value = null;
    resetForm();
    emit('close');
};

const toNullableString = (value: string) => (value === '' ? null : value);
const toNullableInt = (value: string) => {
    if (value === '' || value === null || value === undefined) return null;
    const parsed = parseInt(value, 10);
    return Number.isNaN(parsed) ? null : parsed;
};
const toNullableFloat = (value: string) => {
    if (value === '' || value === null || value === undefined) return null;
    const parsed = parseFloat(value);
    return Number.isNaN(parsed) ? null : parsed;
};
const toNullableNumber = (value: string | number | null) => {
    if (value === '' || value === null || value === undefined) return null;
    const parsed = Number(value);
    return Number.isNaN(parsed) ? null : parsed;
};

const handleSubmit = async () => {
    if (!props.farmer) return;

    try {
        const latitude = toNullableFloat(form.coordinates_latitude);
        const longitude = toNullableFloat(form.coordinates_longitude);
        const coordinates =
            latitude === null && longitude === null ? null : { latitude, longitude };

        const payload: Record<string, any> = {
            farmer_id: toNullableString(form.farmer_id),
            first_name: form.first_name,
            last_name: form.last_name,
            dob: toNullableString(form.dob),
            gender: toNullableString(form.gender),
            educational_level: toNullableString(form.educational_level),
            phone_number: toNullableString(form.phone_number),
            id_number: toNullableString(form.id_number),
            marital_status: toNullableString(form.marital_status),
            district: toNullableString(form.district),
            county: toNullableString(form.county),
            sub_county: toNullableString(form.sub_county),
            parish: toNullableString(form.parish),
            village: toNullableString(form.village),
            next_of_kin: toNullableString(form.next_of_kin),
            next_of_kin_contact: toNullableString(form.next_of_kin_contact),
            next_of_kin_relationship: toNullableString(form.next_of_kin_relationship),
            no_of_household_members: toNullableInt(form.no_of_household_members),
            registered_by: toNullableString(form.registered_by),
            registered_by_agent_id: toNullableNumber(form.registered_by_agent_id),
            milk_collection_center_id: toNullableNumber(form.milk_collection_center_id),
            photo: toNullableString(form.photo),
            coordinates,
            status: form.status,
            validation_reason: toNullableString(form.validation_reason),
            reg_type: toNullableString(form.reg_type),
            household_head: form.household_head,
            family_size: toNullableInt(form.family_size),
            male_members: toNullableInt(form.male_members),
            female_members: toNullableInt(form.female_members),
            above_18: toNullableInt(form.above_18),
            below_5: toNullableInt(form.below_5),
            financial_instrument: toNullableString(form.financial_instrument),
            available_energy_source: toNullableString(form.available_energy_source),
            farm_size: toNullableFloat(form.farm_size),
            land_under_use: toNullableFloat(form.land_under_use),
            land_ownership: toNullableString(form.land_ownership),
            farming_type: toNullableString(form.farming_type),
            crop_production: toNullableString(form.crop_production),
            animal_production: toNullableString(form.animal_production),
            is_farmer_insured: form.is_farmer_insured,
            support_needed: toNullableString(form.support_needed),
        };

        const farmer = await farmerStore.updateFarmer(props.farmer.id, payload);
        emit('updated', farmer?.id);
        handleClose();
    } catch {
        // errors handled via store
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open && props.farmer) {
            updateError.value = null;
            populateFromFarmer(props.farmer);
        } else if (!open) {
            resetForm();
        }
    }
);

watch(
    () => props.farmer,
    (value) => {
        if (props.isOpen && value) {
            populateFromFarmer(value);
        }
    }
);
</script>


