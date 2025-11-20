<template>
    <div class="rounded-lg bg-white shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Cow</th>
                        <th class="px-6 py-3">Diagnosis / Reason</th>
                        <th class="px-6 py-3">Medication</th>
                        <th class="px-6 py-3">Vet</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Outcome</th>
                        <th class="px-6 py-3">Follow Up</th>
                        <th class="px-6 py-3 text-right">Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    <tr v-if="treatments.length === 0" class="hover:bg-gray-50">
                        <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">
                            No treatments recorded for this cow yet.
                        </td>
                    </tr>
                    <tr v-for="treatment in treatments" :key="treatment.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ formatDate(treatment.treatment_date) }}</td>
                        <td class="px-6 py-4">
                            <router-link
                                v-if="cowRoute(treatment)"
                                :to="cowRoute(treatment)"
                                class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                            >
                                {{ cowDisplayName(treatment) }}
                            </router-link>
                            <span v-else>{{ cowDisplayName(treatment) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ treatment.diagnosis ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ treatment.reason ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div>{{ treatment.medication ?? '—' }}</div>
                            <div v-if="treatment.dosage" class="text-xs text-gray-500">
                                {{ treatment.dosage }} {{ treatment.dosage_unit ?? '' }} ({{ treatment.route ?? 'route N/A' }})
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ vetName(treatment) }}</div>
                            <div v-if="vetLicense(treatment)" class="text-xs text-gray-500">
                                License: {{ vetLicense(treatment) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                :class="statusChipClass(treatment.status)"
                            >
                                <Icon :icon="statusChipIcon(treatment.status)" :size="14" />
                                {{ treatment.status ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                :class="outcomeChipClass(treatment.outcome)"
                            >
                                <Icon :icon="outcomeChipIcon(treatment.outcome)" :size="14" />
                                {{ treatment.outcome ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ formatDate(treatment.follow_up_date) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            {{ formatCurrency(treatment.cost) }}
                        </td>
                        <td class="px-6 py-4">{{ recordedByName(treatment) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PropType } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import type { CowTreatment } from '../../stores/geographyStore';

const props = defineProps({
    treatments: {
        type: Array as PropType<CowTreatment[]>,
        default: () => [],
    },
    formatDate: {
        type: Function as PropType<(value?: string | null) => string>,
        required: true,
    },
    formatCurrency: {
        type: Function as PropType<(value?: number | string | null) => string>,
        required: true,
    },
    formatLiters: {
        type: Function as PropType<(value?: number | string | null) => string>,
        required: false,
        default: () => () => '',
    },
});

const statusChipClass = (status?: string | null) => {
    if (!status) return 'bg-gray-100 text-gray-600';
    const normalized = status.toLowerCase();
    if (normalized.includes('scheduled')) return 'bg-amber-100 text-amber-700';
    if (normalized.includes('in_progress') || normalized.includes('progress')) return 'bg-blue-100 text-blue-700';
    if (normalized.includes('completed')) return 'bg-emerald-100 text-emerald-700';
    if (normalized.includes('cancel')) return 'bg-gray-200 text-gray-600';
    return 'bg-gray-100 text-gray-600';
};

const statusChipIcon = (status?: string | null) => {
    if (!status) return 'mdi:progress-clock';
    const normalized = status.toLowerCase();
    if (normalized.includes('scheduled')) return 'mdi:calendar-clock';
    if (normalized.includes('in_progress') || normalized.includes('progress')) return 'mdi:progress-clock';
    if (normalized.includes('completed')) return 'mdi:check-circle-outline';
    if (normalized.includes('cancel')) return 'mdi:close-circle-outline';
    return 'mdi:progress-clock';
};

const outcomeChipClass = (outcome?: string | null) => {
    if (!outcome) return 'bg-gray-100 text-gray-600';
    const normalized = outcome.toLowerCase();
    if (normalized.includes('recover')) return 'bg-emerald-100 text-emerald-700';
    if (normalized.includes('improved')) return 'bg-blue-100 text-blue-700';
    if (normalized.includes('unchanged')) return 'bg-amber-100 text-amber-700';
    if (normalized.includes('deterior') || normalized.includes('deceased')) return 'bg-red-100 text-red-700';
    return 'bg-gray-100 text-gray-600';
};

const outcomeChipIcon = (outcome?: string | null) => {
    if (!outcome) return 'mdi:medical-bag';
    const normalized = outcome.toLowerCase();
    if (normalized.includes('recover')) return 'mdi:emoticon-happy-outline';
    if (normalized.includes('improved')) return 'mdi:trending-up';
    if (normalized.includes('unchanged')) return 'mdi:minus-circle-outline';
    if (normalized.includes('deterior') || normalized.includes('deceased')) return 'mdi:alert-circle-outline';
    return 'mdi:medical-bag';
};

const vetFromRecord = (treatment: CowTreatment) => (treatment.vet ?? (treatment as any).vet ?? (treatment as any).vet_info ?? null);

const vetName = (treatment: CowTreatment) => {
    const vet = vetFromRecord(treatment);
    if (!vet) return 'Unassigned';
    return `${vet.first_name ?? ''} ${vet.last_name ?? ''}`.trim() || 'Unnamed vet';
};

const vetLicense = (treatment: CowTreatment) => {
    const vet = vetFromRecord(treatment);
    return vet?.license_number ?? null;
};

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
