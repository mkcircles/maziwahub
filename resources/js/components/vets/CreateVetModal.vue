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
                <div class="fixed inset-0 bg-gray-900/60" @click="handleClose"></div>

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
                            class="inline-block w-full max-w-4xl transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle"
                        >
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="flex items-start justify-between border-b border-gray-100 px-6 py-4">
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900">Onboard Vet</h2>
                                        <p class="text-sm text-gray-500">
                                            Register a veterinary professional and link them to a milk collection center.
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
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">First Name <span class="text-red-500">*</span></label>
                                            <input
                                                v-model.trim="form.first_name"
                                                type="text"
                                                required
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Last Name <span class="text-red-500">*</span></label>
                                            <input
                                                v-model.trim="form.last_name"
                                                type="text"
                                                required
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">License Number <span class="text-red-500">*</span></label>
                                            <input
                                                v-model.trim="form.license_number"
                                                type="text"
                                                required
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">License Expiry</label>
                                            <input
                                                v-model="form.license_expiry_date"
                                                type="date"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Phone</label>
                                            <input
                                                v-model.trim="form.phone_number"
                                                type="tel"
                                                placeholder="e.g. +256700000000"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email</label>
                                            <input
                                                v-model.trim="form.email"
                                                type="email"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Specialization</label>
                                            <input
                                                v-model.trim="form.specialization"
                                                type="text"
                                                placeholder="e.g. Dairy Health"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Employer</label>
                                            <input
                                                v-model.trim="form.employer"
                                                type="text"
                                                placeholder="Organization or MCC"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Milk Collection Center</label>
                                            <select
                                                v-model.number="form.milk_collection_center_id"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select center (optional)</option>
                                                <option
                                                    v-for="center in milkCenters"
                                                    :key="center.id"
                                                    :value="center.id"
                                                >
                                                    {{ center.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Bio / Notes</label>
                                            <textarea
                                                v-model.trim="form.bio"
                                                rows="3"
                                                placeholder="Background, areas of expertise, coverage region, etc."
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>
                                        <div class="flex items-center gap-3 sm:col-span-2">
                                            <input id="is_active" type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                            <label for="is_active" class="text-sm text-gray-700">Active</label>
                                        </div>
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
                                        <Icon icon="mdi:content-save" :size="18" />
                                        <span>{{ submitting ? 'Saving...' : 'Save Vet' }}</span>
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
import { reactive, ref, watch } from 'vue';
import axios from 'axios';
import Icon from '../shared/Icon.vue';
import { useVetStore } from '../../stores/vetStore';
import type { MilkCollectionCenter } from '../../stores/geographyStore';

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const vetStore = useVetStore();

const submitting = ref(false);
const errorMessage = ref<string | null>(null);
const milkCenters = ref<MilkCollectionCenter[]>([]);
const milkCentersLoading = ref(false);

const form = reactive({
    first_name: '',
    last_name: '',
    license_number: '',
    license_expiry_date: '',
    phone_number: '',
    email: '',
    specialization: '',
    employer: '',
    milk_collection_center_id: '' as number | '' | null,
    bio: '',
    is_active: true,
});

const resetForm = () => {
    Object.assign(form, {
        first_name: '',
        last_name: '',
        license_number: '',
        license_expiry_date: '',
        phone_number: '',
        email: '',
        specialization: '',
        employer: '',
        milk_collection_center_id: '',
        bio: '',
        is_active: true,
    });
    errorMessage.value = null;
};

const fetchMilkCenters = async () => {
    milkCentersLoading.value = true;
    try {
        const response = await axios.get('/milk-collection-centers', {
            params: { per_page: 1000 },
        });
        const payload = response.data;
        if (Array.isArray(payload?.data)) {
            milkCenters.value = payload.data as MilkCollectionCenter[];
        } else if (Array.isArray(payload)) {
            milkCenters.value = payload as MilkCollectionCenter[];
        } else {
            milkCenters.value = [];
        }
    } catch {
        milkCenters.value = [];
    } finally {
        milkCentersLoading.value = false;
    }
};

const handleClose = () => {
    if (submitting.value) return;
    resetForm();
    emit('close');
};

const handleSubmit = async () => {
    submitting.value = true;
    errorMessage.value = null;

    try {
        await vetStore.createVet({
            first_name: form.first_name,
            last_name: form.last_name,
            license_number: form.license_number,
            license_expiry_date: form.license_expiry_date || null,
            phone_number: form.phone_number || null,
            email: form.email || null,
            specialization: form.specialization || null,
            employer: form.employer || null,
            milk_collection_center_id: form.milk_collection_center_id === '' ? null : Number(form.milk_collection_center_id),
            bio: form.bio || null,
            is_active: form.is_active,
        });
        emit('created');
        resetForm();
    } catch (err: any) {
        errorMessage.value = err.response?.data?.message || vetStore.createError || 'Failed to add vet.';
    } finally {
        submitting.value = false;
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            errorMessage.value = null;
            if (!milkCenters.value.length && !milkCentersLoading.value) {
                fetchMilkCenters();
            }
        } else {
            resetForm();
        }
    }
);
</script>
