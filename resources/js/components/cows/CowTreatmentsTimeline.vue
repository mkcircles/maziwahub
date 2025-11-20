<template>
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100">
        <h2 class="text-lg font-semibold text-gray-900">Treatment Timeline</h2>
        <p class="text-sm text-gray-500">Track treatment history and follow-up status.</p>
        <div v-if="treatments.length === 0" class="mt-6 rounded-2xl border border-slate-100 bg-slate-50/80 p-5 text-sm text-gray-600">
            No treatments recorded yet.
        </div>
        <div v-else class="mt-6 space-y-6">
            <div v-for="treatment in sortedTreatments" :key="treatment.id" class="relative flex gap-4">
                <span class="absolute left-[10px] top-0 h-full w-px bg-slate-200" aria-hidden="true"></span>
                <span class="relative z-10 mt-1 h-4 w-4 rounded-full border-2 border-slate-100 bg-blue-500"></span>
                <div class="flex-1 rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ formatDate(treatment.treatment_date) }}</p>
                            <p class="text-xs uppercase tracking-wide text-gray-500">
                                {{ treatment.status ?? 'Status N/A' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <Icon icon="mdi:account-heart-outline" :size="14" />
                            {{ treatment.vet?.first_name ? `${treatment.vet.first_name} ${treatment.vet.last_name}` : 'Vet not assigned' }}
                        </div>
                    </div>
                    <div class="mt-3 space-y-2 text-sm text-gray-700">
                        <div v-if="treatment.diagnosis" class="font-medium text-gray-900">Diagnosis: {{ treatment.diagnosis }}</div>
                        <div v-if="treatment.reason" class="text-xs text-gray-500">Reason: {{ treatment.reason }}</div>
                        <div class="text-xs text-gray-500">
                            Cow:
                            <router-link
                                v-if="cowRoute(treatment)"
                                :to="cowRoute(treatment)"
                                class="font-medium text-blue-600 hover:text-blue-700"
                            >
                                {{ cowDisplayName(treatment) }}
                            </router-link>
                            <span v-else class="font-medium text-gray-900">{{ cowDisplayName(treatment) }}</span>
                        </div>
                        <div v-if="treatment.medication">
                            <span class="font-medium text-gray-900">Medication:</span>
                            {{ treatment.medication }}
                            <span v-if="treatment.dosage">• {{ treatment.dosage }} {{ treatment.dosage_unit ?? '' }}</span>
                            <span v-if="treatment.route" class="text-xs text-gray-500"> ({{ treatment.route }})</span>
                        </div>
                        <div v-if="treatment.notes">{{ treatment.notes }}</div>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                        <div class="flex items-center gap-1">
                            <Icon icon="mdi:calendar-clock" :size="14" />
                            Follow-up: {{ formatDate(treatment.follow_up_date) }}
                        </div>
                        <div class="flex items-center gap-1">
                            <Icon icon="mdi:medical-bag" :size="14" />
                            Outcome: {{ treatment.outcome ?? 'Pending' }}
                        </div>
                        <div class="flex items-center gap-1">
                            <Icon icon="mdi:currency-usd" :size="14" />
                            {{ formatCurrency(treatment.cost) }}
                        </div>
                        <div class="flex items-center gap-1">
                            <Icon icon="mdi:account-check-outline" :size="14" />
                            Logged by: {{ recordedByName(treatment) }}
                        </div>
                        <div v-if="treatment.next_steps" class="flex items-center gap-1">
                            <Icon icon="mdi:clipboard-text-outline" :size="14" />
                            Next: {{ treatment.next_steps }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import type { CowTreatment } from '../../stores/geographyStore';

const props = defineProps<{
    treatments: CowTreatment[];
    formatDate: (value?: string | null) => string;
    formatCurrency: (value?: number | string | null) => string;
}>();

const sortedTreatments = computed(() =>
    [...props.treatments].sort((a, b) => {
        const dateA = new Date(a.treatment_date ?? '').getTime();
        const dateB = new Date(b.treatment_date ?? '').getTime();
        return dateB - dateA;
    })
);

const recordedByName = (treatment: CowTreatment) => {
    const recorded = (treatment as any).recordedBy ?? (treatment as any).recorded_by ?? null;
    if (!recorded) return '—';
    if (typeof recorded === 'string') return recorded;
    return recorded.name ?? recorded.full_name ?? '—';
};

const cowFromRecord = (treatment: CowTreatment) => (treatment.cow ?? (treatment as any).cow ?? null);

const cowDisplayName = (treatment: CowTreatment) => {
    const recordCow = cowFromRecord(treatment);
    if (recordCow?.tag_number) {
        return recordCow.tag_number;
    }
    if (recordCow?.id) {
        return `Cow #${recordCow.id}`;
    }
    if (treatment.cow_id) {
        return `Cow #${treatment.cow_id}`;
    }
    return 'Unknown cow';
};

const cowRoute = (treatment: CowTreatment) => {
    const recordCow = cowFromRecord(treatment);
    const id = recordCow?.id ?? treatment.cow_id;
    if (!id) return null;
    return `/admin/cows/${id}`;
};
</script>
