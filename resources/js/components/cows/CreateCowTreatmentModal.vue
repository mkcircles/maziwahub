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
                                        <h2 class="text-lg font-semibold text-gray-900">Record Treatment</h2>
                                        <p class="text-sm text-gray-500">
                                            Capture veterinary treatment details for this cow.
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
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Treatment Date <span class="text-red-500">*</span></label>
                                            <input
                                                v-model="form.treatment_date"
                                                type="date"
                                                required
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned Vet</label>
                                            <select
                                                v-model="form.vet_id"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select vet</option>
                                                <option v-for="vet in vets" :key="vet.id" :value="vet.id">
                                                    {{ vet.first_name }} {{ vet.last_name }} • {{ vet.license_number }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Diagnosis</label>
                                            <input
                                                v-model.trim="form.diagnosis"
                                                type="text"
                                                placeholder="e.g. Mastitis"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Reason</label>
                                            <textarea
                                                v-model.trim="form.reason"
                                                rows="2"
                                                placeholder="Describe symptoms or reason for treatment"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Medication</label>
                                            <input
                                                v-model.trim="form.medication"
                                                type="text"
                                                placeholder="Name of medicine or vaccine"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dosage</label>
                                            <div class="flex gap-2">
                                                <input
                                                    v-model.trim="form.dosage"
                                                    type="text"
                                                    placeholder="Amount"
                                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                                <input
                                                    v-model.trim="form.dosage_unit"
                                                    type="text"
                                                    placeholder="Unit"
                                                    class="w-28 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                />
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Route</label>
                                            <input
                                                v-model.trim="form.route"
                                                type="text"
                                                placeholder="e.g. Injection"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Follow-up Date</label>
                                            <input
                                                v-model="form.follow_up_date"
                                                type="date"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</label>
                                            <select
                                                v-model="form.status"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="completed">Completed</option>
                                                <option value="in_progress">In progress</option>
                                                <option value="scheduled">Scheduled</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Outcome</label>
                                            <select
                                                v-model="form.outcome"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select outcome</option>
                                                <option value="recovered">Recovered</option>
                                                <option value="improved">Improved</option>
                                                <option value="unchanged">Unchanged</option>
                                                <option value="deteriorated">Deteriorated</option>
                                                <option value="deceased">Deceased</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Cost</label>
                                            <input
                                                v-model.number="form.cost"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                placeholder="Cost"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Lifecycle Status</label>
                                            <select
                                                v-model="form.life_cycle_status"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            >
                                                <option value="">Select status</option>
                                                <option value="active">Active</option>
                                                <option value="sold">Sold</option>
                                                <option value="deceased">Deceased</option>
                                                <option value="slaughtered">Slaughtered</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Next Steps</label>
                                            <textarea
                                                v-model.trim="form.next_steps"
                                                rows="2"
                                                placeholder="Recommended follow-up actions"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Notes</label>
                                            <textarea
                                                v-model.trim="form.notes"
                                                rows="3"
                                                placeholder="Additional remarks"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
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
                                        <Icon icon="mdi:medical-bag" :size="18" />
                                        <span>{{ submitting ? 'Saving...' : 'Save Treatment' }}</span>
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
import Icon from '../../components/shared/Icon.vue';
import { useAuthStore } from '../../stores/authStore';
import type { CowTreatment, Vet } from '../../stores/geographyStore';

const props = defineProps<{
    isOpen: boolean;
    cowId: number | string | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created', payload: CowTreatment): void;
}>();

const authStore = useAuthStore();

const submitting = ref(false);
const errorMessage = ref<string | null>(null);
const vets = ref<Vet[]>([]);
const vetsLoading = ref(false);

const form = reactive({
    treatment_date: new Date().toISOString().slice(0, 10),
    vet_id: '' as number | '' | null,
    diagnosis: '',
    reason: '',
    medication: '',
    dosage: '',
    dosage_unit: '',
    route: '',
    follow_up_date: '',
    status: 'completed',
    outcome: '',
    next_steps: '',
    cost: '' as number | '' | null,
    notes: '',
    life_cycle_status: '',
});

const resetForm = () => {
    Object.assign(form, {
        treatment_date: new Date().toISOString().slice(0, 10),
        vet_id: '',
        diagnosis: '',
        reason: '',
        medication: '',
        dosage: '',
        dosage_unit: '',
        route: '',
        follow_up_date: '',
        status: 'completed',
        outcome: '',
        next_steps: '',
        cost: '',
        notes: '',
        life_cycle_status: '',
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

const fetchVets = async () => {
    vetsLoading.value = true;
    try {
        const response = await axios.get('/vets', {
            params: { active_only: true, per_page: 1000 },
        });
        const payload = response.data;
        const data = Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : [];
        vets.value = data;
    } catch {
        vets.value = [];
    } finally {
        vetsLoading.value = false;
    }
};

const handleSubmit = async () => {
    if (!props.cowId) return;

    submitting.value = true;
    errorMessage.value = null;

    try {
        const response = await axios.post('/cow-treatments', {
            cow_id: Number(props.cowId),
            vet_id: form.vet_id === '' ? null : Number(form.vet_id),
            recorded_by_id: authStore.user?.id ?? null,
            treatment_date: form.treatment_date,
            diagnosis: form.diagnosis || null,
            reason: form.reason || null,
            medication: form.medication || null,
            dosage: form.dosage || null,
            dosage_unit: form.dosage_unit || null,
            route: form.route || null,
            follow_up_date: form.follow_up_date || null,
            status: form.status || null,
            outcome: form.outcome || null,
            next_steps: form.next_steps || null,
            cost: toNullableNumber(form.cost),
            notes: form.notes || null,
            life_cycle_status: form.life_cycle_status || null,
        });

        emit('created', response.data as CowTreatment);
        handleClose();
    } catch (err: any) {
        errorMessage.value = err.response?.data?.message || 'Failed to create treatment record.';
    } finally {
        submitting.value = false;
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            if (!vets.value.length && !vetsLoading.value) {
                fetchVets();
            }
            errorMessage.value = null;
        } else {
            resetForm();
        }
    }
);
</script>
