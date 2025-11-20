<template>
    <div
        class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-br from-white/95 via-white/90 to-slate-50/80 p-6 shadow-lg shadow-slate-100 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-200"
    >
        <div class="pointer-events-none absolute -right-8 -top-10 h-32 w-32 rounded-full bg-sky-300/30 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-20px] left-[-30px] h-32 w-32 rounded-full bg-emerald-200/25 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-12 bg-gradient-to-b from-white/40 to-transparent"></div>

        <div class="relative flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-600 shadow-inner shadow-sky-200">
                    <Icon icon="mdi:storefront-outline" :size="22" />
                </span>
                <div class="space-y-1">
                    <h3 class="text-base font-semibold text-slate-900">
                        <router-link
                            :to="`/admin/milk-collection-centers/${center.id}`"
                            class="inline-flex text-sm items-center gap-1 text-slate-900 transition-colors duration-200 hover:text-sky-600"
                        >
                            {{ center.name }}
                            <Icon icon="mdi:open-in-new" :size="14" class="text-sky-500" />
                        </router-link>
                    </h3>
                    <p class="text-[11px] font-medium uppercase tracking-[0.2em] text-slate-400">
                        {{ registrationLabel }}
                    </p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-1 text-[11px] font-semibold text-sky-700 shadow-inner shadow-sky-100">
                <Icon icon="mdi:snowflake-variant" :size="16" />
                {{ coolerCapacityLabel }}
            </span>
        </div>

        <div v-if="!props.minified" class="relative mt-6 space-y-4 text-sm">
            <div class="flex items-start gap-3 rounded-2xl bg-slate-50/70 p-3 text-slate-600">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                    <Icon icon="mdi:map-marker-outline" :size="18" />
                </span>
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Physical Address
                    </p>
                    <p class="text-sm font-medium text-slate-700">
                        {{ center.physical_address }}
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-3 rounded-2xl border border-dashed border-slate-200/70 bg-white/70 p-3 text-slate-500 shadow-sm">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                    <Icon icon="mdi:map-search-outline" :size="18" />
                </span>
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Location Trail
                    </p>
                    <p class="text-sm font-medium text-slate-600">
                        {{ displayLocation }}
                    </p>
                </div>
            </div>

            <div
                v-if="managerDetails.length"
                class="grid gap-3 rounded-2xl border border-slate-100 bg-white/80 p-4 shadow-inner shadow-slate-100 sm:grid-cols-2"
            >
                <div v-for="detail in managerDetails" :key="detail.label" class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        {{ detail.label }}
                    </p>
                    <p class="text-sm font-medium text-slate-700">
                        {{ detail.value }}
                    </p>
                </div>
            </div>
        </div>

        <div class="relative mt-5 flex flex-wrap gap-2">
            <span
                v-for="feature in featureBadges"
                :key="feature.label"
                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold transition"
                :class="feature.class"
            >
                <Icon :icon="feature.icon" :size="14" />
                {{ feature.label }}
            </span>
        </div>

        <div v-if="!props.minified && facilityHighlights.length" class="mt-4 flex flex-wrap gap-2">
            <div
                v-for="highlight in facilityHighlights"
                :key="highlight.label"
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-100 bg-white/80 px-3 py-1 text-xs font-medium text-slate-600 shadow-inner shadow-slate-100"
            >
                <Icon :icon="highlight.icon" :size="14" class="text-slate-400" />
                <span class="uppercase tracking-wide text-[10px] text-slate-400">{{ highlight.label }}</span>
                <span class="font-semibold text-slate-700">{{ highlight.value }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Icon from '../shared/Icon.vue';
import type { MilkCollectionCenter } from '../../stores/geographyStore';

type ExtendedCenter = MilkCollectionCenter & {
    registration_number?: string | null;
    cooler_capacity_liters?: number | null;
    has_testing_equipment?: boolean;
    has_washing_bay?: boolean;
    manager_name?: string | null;
    manager_phone?: string | null;
    staff_count?: number | null;
    power_source?: string | null;
    established_date?: string | null;
    physical_address: string;
    location?: {
        country?: string | null;
        region?: string | null;
        district?: string | null;
        county?: string | null;
        subcounty?: string | null;
        parish?: string | null;
        village?: string | null;
    } | null;
};

const props = defineProps<{
    center: ExtendedCenter;
    formatLocation?: (center: MilkCollectionCenter) => string;
    minified?: boolean;
}>();

const registrationLabel = computed(() => props.center.registration_number ?? 'Unregistered facility');

const coolerCapacityLabel = computed(() => {
    const capacity = props.center.cooler_capacity_liters ?? 0;
    return `${capacity.toLocaleString()} L`;
});

const displayLocation = computed(() => {
    if (props.formatLocation) {
        return props.formatLocation(props.center);
    }
    if (props.center.location) {
        const {
            country,
            region,
            district,
            county,
            subcounty,
            parish,
            village,
        } = props.center.location;
        const trail = [country, region, district, county, subcounty, parish, village].filter(Boolean);
        if (trail.length) {
            return trail.join(' • ');
        }
    }
    return 'Location details unavailable';
});

const managerDetails = computed(() => {
    const details: Array<{ label: string; value: string }> = [];

    if (props.center.manager_name) {
        details.push({
            label: 'Manager',
            value: props.center.manager_name,
        });
    }
    if (props.center.manager_phone) {
        details.push({
            label: 'Contact',
            value: formatPhone(props.center.manager_phone),
        });
    }
    if (props.center.staff_count != null) {
        details.push({
            label: 'Staff Count',
            value: `${props.center.staff_count} team member${props.center.staff_count === 1 ? '' : 's'}`,
        });
    }
    if (props.center.power_source) {
        details.push({
            label: 'Power Source',
            value: props.center.power_source,
        });
    }

    return details;
});

const featureBadges = computed(() => {
    const badges: Array<{ label: string; icon: string; class: string }> = [
        {
            label: props.center.has_testing_equipment ? 'Testing Equipped' : 'Testing Unavailable',
            icon: 'mdi:flask-outline',
            class: props.center.has_testing_equipment
                ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                : 'bg-slate-50 text-slate-500 border border-slate-100',
        },
        {
            label: props.center.has_washing_bay ? 'Washing Bay' : 'No Washing Bay',
            icon: 'mdi:water-outline',
            class: props.center.has_washing_bay
                ? 'bg-sky-50 text-sky-600 border border-sky-100'
                : 'bg-slate-50 text-slate-500 border border-slate-100',
        },
    ];

    return badges;
});

const facilityHighlights = computed(() => {
    const highlights: Array<{ label: string; value: string; icon: string }> = [];

    if (props.center.staff_count != null) {
        highlights.push({
            label: 'Staff',
            value: `${props.center.staff_count}`,
            icon: 'mdi:account-group-outline',
        });
    }

    if (props.center.power_source) {
        highlights.push({
            label: 'Power',
            value: props.center.power_source,
            icon: 'mdi:flash-outline',
        });
    }

    if (props.center.established_date) {
        highlights.push({
            label: 'Est.',
            value: formatDate(props.center.established_date),
            icon: 'mdi:calendar-blank-outline',
        });
    }

    return highlights;
});

function formatPhone(value: string): string {
    const trimmed = value.trim();
    if (!trimmed) return value;
    if (trimmed.startsWith('+')) return trimmed;
    if (/^\d+$/.test(trimmed)) {
        if (trimmed.length === 10) {
            return `+${trimmed}`;
        }
    }
    return trimmed;
}

function formatDate(value: string): string {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleDateString();
}
</script>

