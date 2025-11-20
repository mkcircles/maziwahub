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
                @keydown.esc.prevent="handleClose"
                @click.self="handleClose"
            >
                <div class="fixed inset-0 bg-slate-900/60"></div>

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
                            class="inline-block w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-10 sm:align-middle"
                        >
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                                    <div class="space-y-1.5">
                                        <h2 class="text-lg font-semibold text-slate-900">Record Feeding Update</h2>
                                        <p class="text-sm text-slate-500">
                                            Log how this farmer is currently feeding their herd. Active records remain open until you end them.
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none"
                                        @click="handleClose"
                                    >
                                        <Icon icon="mdi:close" :size="20" />
                                    </button>
                                </div>

                                <div class="space-y-6 px-6">
                                    <div class="grid gap-4 md:grid-cols-3">
                                        <div class="md:col-span-3 space-y-1.5">
                                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
                                                Feeding Type <span class="text-red-500">*</span>
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    v-for="option in feedingTypeOptions"
                                                    :key="option.value"
                                                    type="button"
                                                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold transition"
                                                    :class="form.feeding_type === option.value
                                                        ? 'border-slate-900 bg-slate-900 text-white shadow-sm shadow-slate-400/40'
                                                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                                                    @click="updateFeedingType(option.value)"
                                                >
                                                    <Icon :icon="option.icon" :size="14" />
                                                    {{ option.label }}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="space-y-1 md:col-span-2" :class="{ 'opacity-50': isOtherType }">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Feeding Method
                                                <span v-if="!isOtherType" class="text-red-500">*</span>
                                            </label>
                                            <select
                                                v-model.number="form.feeding_method_id"
                                                :disabled="isOtherType"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed"
                                            >
                                                <option :value="null">Select method</option>
                                                <option
                                                    v-for="method in methodOptions"
                                                    :key="method.id"
                                                    :value="method.id"
                                                :disabled="!method.is_active"
                                            >
                                                    {{ method.name }}
                                                </option>
                                            </select>
                                            <p v-if="requiresDetails" class="text-xs text-amber-600">
                                                This method typically requires additional details. Consider adding notes or supplements.
                                            </p>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Started At
                                            </label>
                                            <input
                                                v-model="form.started_at"
                                                type="datetime-local"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>

                                        <div class="space-y-1">
                                            <label class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                <input
                                                    v-model="form.is_ongoing"
                                                    type="checkbox"
                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                                />
                                                Still ongoing
                                            </label>
                                            <p class="text-[11px] text-slate-500">
                                                Uncheck if this feeding arrangement has ended.
                                            </p>
                                        </div>

                                        <div class="space-y-1" :class="{ 'opacity-50': form.is_ongoing }">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Ended At
                                            </label>
                                            <input
                                                v-model="form.ended_at"
                                                type="datetime-local"
                                                :disabled="form.is_ongoing"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div class="space-y-1 md:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Feeding Notes
                                            </label>
                                            <textarea
                                                v-model.trim="form.notes"
                                                rows="3"
                                                placeholder="Describe ration details, supplements, preparation, etc."
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            ></textarea>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Feeding Frequency (per day)
                                            </label>
                                            <input
                                                v-model.trim="form.frequency"
                                                type="text"
                                                placeholder="e.g. Twice daily"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Supplements Used
                                            </label>
                        <input
                                                v-model.trim="form.supplements"
                                                type="text"
                                                placeholder="e.g. Mineral lick, dairy meal"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            />
                                        </div>

                                        <div class="space-y-2 md:col-span-2 rounded-2xl border border-slate-200/70 bg-slate-50/80 p-4">
                                            <label class="flex items-start gap-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                <input
                                                    v-model="form.update_farmer_notes"
                                                    type="checkbox"
                                                    class="mt-0.5 h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                                />
                                                Update farmer level feeding notes
                                            </label>
                                            <textarea
                                                v-model.trim="form.farmer_notes"
                                                :disabled="!form.update_farmer_notes"
                                                rows="2"
                                                placeholder="Summary that appears on the farmer profile"
                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div v-if="errorMessage" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                        {{ errorMessage }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/70 px-6 py-4">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                        @click="handleClose"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="isSubmitting || loading || !isValid"
                                    >
                                        <Icon
                                            v-if="isSubmitting"
                                            icon="mdi:loading"
                                            :size="18"
                                            class="animate-spin"
                                        />
                                        <Icon v-else icon="mdi:content-save" :size="18" />
                                        {{ isSubmitting ? 'Saving...' : 'Save feeding update' }}
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
import { computed, reactive, ref, watch } from 'vue';
import type { PropType } from 'vue';
import Icon from '../shared/Icon.vue';
import { useFarmerStore } from '../../stores/farmerStore';
import type { FeedingMethod } from '../../stores/geographyStore';

const props = defineProps({
    isOpen: { type: Boolean, default: false },
    farmerId: { type: [Number, String] as PropType<number | string | null>, default: null },
    feedingMethods: { type: Array as PropType<FeedingMethod[]>, default: () => [] },
    defaultType: {
        type: String as PropType<'primary' | 'supplemental' | 'other'>,
        default: 'primary',
    },
    currentMethodId: { type: Number as PropType<number | null>, default: null },
    loading: { type: Boolean, default: false },
});

const emit = defineEmits<{
    (event: 'close'): void;
    (event: 'created'): void;
}>();

const farmerStore = useFarmerStore();

const form = reactive({
    feeding_type: props.defaultType,
    feeding_method_id: props.currentMethodId as number | null,
    started_at: '',
    ended_at: '',
    is_ongoing: true,
    notes: '',
    frequency: '',
    supplements: '',
    update_farmer_notes: false,
    farmer_notes: '',
});

const isSubmitting = ref(false);
const errorMessage = ref<string | null>(null);

const feedingTypeOptions = [
    { value: 'primary', label: 'Primary', icon: 'mdi:leaf' },
    { value: 'supplemental', label: 'Supplemental', icon: 'mdi:food-variant' },
    { value: 'other', label: 'Other', icon: 'mdi:tooltip-edit' },
] as const;

const methodOptions = computed(() =>
    [...props.feedingMethods].sort((a, b) => {
        if (a.is_active === b.is_active) {
            return a.name.localeCompare(b.name);
        }
        return a.is_active ? -1 : 1;
    }),
);

const selectedMethod = computed(() =>
    methodOptions.value.find((method) => method.id === form.feeding_method_id) ?? null,
);

const requiresDetails = computed(() => Boolean(selectedMethod.value?.requires_details));
const isOtherType = computed(() => form.feeding_type === 'other');

const isValid = computed(() => {
    if (!form.feeding_type) return false;
    if (!props.farmerId && props.farmerId !== 0) return false;
    if (!isOtherType.value && !form.feeding_method_id) return false;
    if (!form.is_ongoing && form.ended_at && form.started_at && form.ended_at < form.started_at) {
        return false;
    }
    return true;
});

const resetForm = () => {
    form.feeding_type = props.defaultType;
    form.feeding_method_id = props.currentMethodId ?? null;
    form.started_at = '';
    form.ended_at = '';
    form.is_ongoing = true;
    form.notes = '';
    form.frequency = '';
    form.supplements = '';
    form.update_farmer_notes = false;
    form.farmer_notes = '';
    errorMessage.value = null;

    if (form.feeding_type === 'other') {
        form.feeding_method_id = null;
    }
};

const updateFeedingType = (value: 'primary' | 'supplemental' | 'other') => {
    form.feeding_type = value;
    if (value === 'other') {
        form.feeding_method_id = null;
    } else if (!form.feeding_method_id) {
        form.feeding_method_id = props.currentMethodId ?? null;
    }
};

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            resetForm();
        }
    },
);

watch(
    () => props.defaultType,
    (type) => {
        if (!props.isOpen) return;
        form.feeding_type = type;
        if (type !== 'other') {
            form.feeding_method_id = props.currentMethodId ?? null;
        }
    },
);

watch(
    () => props.currentMethodId,
    (methodId) => {
        if (!props.isOpen) return;
        if (!isOtherType.value) {
            form.feeding_method_id = methodId ?? null;
        }
    },
);

watch(
    () => form.is_ongoing,
    (ongoing) => {
        if (ongoing) {
            form.ended_at = '';
        }
    },
);

const buildMetadata = () => {
    const meta: Record<string, any> = {};
    if (form.frequency) meta.frequency = form.frequency;
    if (form.supplements) meta.supplements = form.supplements;
    return meta;
};

const handleSubmit = async () => {
    if (!props.farmerId && props.farmerId !== 0) {
        errorMessage.value = 'Farmer reference missing.';
        return;
    }

    if (!isValid.value) {
        errorMessage.value = 'Please complete the required fields before saving.';
        return;
    }

    const payload: Record<string, any> = {
        feeding_type: form.feeding_type,
    };

    if (!isOtherType.value) {
        payload.feeding_method_id = form.feeding_method_id;
    }

    if (form.started_at) {
        payload.started_at = form.started_at;
    }

    if (!form.is_ongoing && form.ended_at) {
        payload.ended_at = form.ended_at;
    }

    if (form.notes) {
        payload.notes = form.notes;
    }

    const metadata = buildMetadata();
    if (Object.keys(metadata).length) {
        payload.metadata = metadata;
    }

    if (form.update_farmer_notes && form.farmer_notes) {
        payload.feeding_notes = form.farmer_notes;
        if (!payload.metadata) {
            payload.metadata = metadata;
        }
        payload.feeding_metadata = metadata;
    }

    errorMessage.value = null;
    isSubmitting.value = true;

    try {
        await farmerStore.recordFeedingHistory(props.farmerId!, payload);
        emit('created');
        resetForm();
    } catch (err: any) {
        errorMessage.value = err?.response?.data?.message || 'Failed to record feeding information.';
    } finally {
        isSubmitting.value = false;
    }
};

const handleClose = () => {
    if (isSubmitting.value) return;
    emit('close');
};
</script>


