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
                                        <h2 class="text-lg font-semibold text-gray-900">Record Milk Delivery</h2>
                                        <p class="text-sm text-gray-500">
                                            Capture a new delivery for this milk collection center.
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
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Farmer ID <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model.number="form.farmer_id"
                                                type="text"
                                                min="5"
                                                required
                                                placeholder="Enter farmer identifier"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Delivery Date <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model="form.delivery_date"
                                                type="date"
                                                required
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Volume (Liters) <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model.number="form.volume_liters"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                required
                                                placeholder="Total volume in liters"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Price per Liter (Optional)
                                            </label>
                                            <input
                                                v-model.number="form.price_per_liter"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                placeholder="Unit price"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Quality Grade
                                            </label>
                                            <input
                                                v-model.trim="form.quality_grade"
                                                type="text"
                                                placeholder="e.g. A, B, C"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Recorded By
                                            </label>
                                            <input
                                                v-model.trim="form.recorded_by"
                                                type="text"
                                                placeholder="Staff member"
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
                                                placeholder="Additional remarks"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="error" class="px-6">
                                    <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                        {{ error }}
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
                                        <Icon icon="mdi:bucket" :size="18" />
                                        <span>{{ submitting ? 'Saving...' : 'Save Delivery' }}</span>
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
import { reactive, watch, computed } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import { useFarmerStore } from '../../stores/farmerStore';

const props = defineProps<{
    isOpen: boolean;
    centerId: number | string | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const farmerStore = useFarmerStore();
const submitting = computed(() => farmerStore.creating);
const error = computed(() => farmerStore.createError);

const form = reactive({
    farmer_id: '' as number | '' | null,
    delivery_date: '',
    volume_liters: '' as number | '' | null,
    quality_grade: '',
    price_per_liter: '' as number | '' | null,
    recorded_by: '',
    notes: '',
});

const resetForm = () => {
    Object.assign(form, {
        farmer_id: '',
        delivery_date: '',
        volume_liters: '',
        quality_grade: '',
        price_per_liter: '',
        recorded_by: '',
        notes: '',
    });
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

const handleSubmit = async () => {
    if (!props.centerId) return;

    try {
        await farmerStore.createMilkDelivery({
            milk_collection_center_id: Number(props.centerId),
            farmer_id: form.farmer_id === '' ? null : Number(form.farmer_id),
            delivery_date: form.delivery_date,
            volume_liters: toNullableNumber(form.volume_liters) ?? 0,
            quality_grade: form.quality_grade || null,
            price_per_liter: toNullableNumber(form.price_per_liter),
            recorded_by: form.recorded_by || null,
            notes: form.notes || null,
        });

        emit('created');
        handleClose();
    } catch {
        // error surfaced via store
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
</script>
