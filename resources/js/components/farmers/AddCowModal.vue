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
                            class="inline-block w-full max-w-2xl transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle"
                        >
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="flex items-start justify-between border-b border-gray-100 px-6 py-4">
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900">Register Cow</h2>
                                        <p class="text-sm text-gray-500">
                                            Add a new cow for this farmer. Tag numbers must be unique.
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
                                                Tag Number <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model.trim="form.tag_number"
                                                type="text"
                                                required
                                                placeholder="Unique identifier"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Breed
                                            </label>
                                            <input
                                                v-model.trim="form.breed"
                                                type="text"
                                                placeholder="e.g. Friesian"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Health Status
                                            </label>
                                            <input
                                                v-model.trim="form.health_status"
                                                type="text"
                                                placeholder="e.g. Healthy"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Date of Birth
                                            </label>
                                            <input
                                                v-model="form.date_of_birth"
                                                type="date"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Acquired Date
                                            </label>
                                            <input
                                                v-model="form.acquired_date"
                                                type="date"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Milk Capacity (Liters)
                                            </label>
                                            <input
                                                v-model.number="form.milk_capacity_liters"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                placeholder="Average daily capacity"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1 sm:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Notes
                                            </label>
                                            <textarea
                                                v-model.trim="form.notes"
                                                rows="3"
                                                placeholder="Optional notes"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="createCowError" class="px-6">
                                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                        {{ createCowError }}
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white disabled:cursor-not-allowed disabled:opacity-50"
                                        @click="handleClose"
                                        :disabled="creatingCow"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="creatingCow"
                                    >
                                        <Icon icon="mdi:cow" :size="18" />
                                        <span>{{ creatingCow ? 'Saving...' : 'Save Cow' }}</span>
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

const props = defineProps<{
    isOpen: boolean;
    farmerId: number | string | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const farmerStore = useFarmerStore();
const { creatingCow, createCowError } = storeToRefs(farmerStore);

const form = reactive({
    tag_number: '',
    breed: '',
    date_of_birth: '',
    acquired_date: '',
    milk_capacity_liters: '' as number | '' | null,
    health_status: '',
    notes: '',
});

const resetForm = () => {
    Object.assign(form, {
        tag_number: '',
        breed: '',
        date_of_birth: '',
        acquired_date: '',
        milk_capacity_liters: '',
        health_status: '',
        notes: '',
    });
};

const handleClose = () => {
    if (creatingCow.value) return;
    resetForm();
    emit('close');
};

const handleSubmit = async () => {
    if (!props.farmerId) return;

    try {
        await farmerStore.createCow({
            farmer_id: props.farmerId,
            tag_number: form.tag_number,
            breed: form.breed || null,
            date_of_birth: form.date_of_birth || null,
            acquired_date: form.acquired_date || null,
            milk_capacity_liters:
                form.milk_capacity_liters === '' ? null : Number(form.milk_capacity_liters),
            health_status: form.health_status || null,
            notes: form.notes || null,
        });

        emit('created');
        handleClose();
    } catch {
        // error shown via store
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            createCowError.value = null;
        } else {
            resetForm();
        }
    }
);
</script>

