<template>
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 px-8 py-10 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.28),transparent_65%)] opacity-80"></div>
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.5em] text-white/70">
                        Approval Desk
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight lg:text-4xl">Milk Collection Center Claims</h1>
                    <p class="text-sm text-white/70">
                        Review requests from partner organisations who want to manage existing milk collection centers.
                        Approve or reject claims and keep stakeholders informed with response notes.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:clock-outline" :size="16" />
                            {{ pendingCount }} pending
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:check-circle-outline" :size="16" />
                            {{ approvedCount }} approved
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:close-circle-outline" :size="16" />
                            {{ rejectedCount }} rejected
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20"
                        :disabled="claimsLoading"
                        @click="refreshClaims"
                    >
                        <Icon icon="mdi:refresh" :size="18" class="animate-spin" v-if="claimsLoading" />
                        <Icon icon="mdi:refresh" :size="18" v-else />
                        Refresh
                    </button>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/95 p-8 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="option in statusOptions"
                                :key="option.value"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                                :class="statusFilter === option.value ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-800'"
                                @click="setStatus(option.value)"
                            >
                                <Icon :icon="option.icon" :size="14" />
                                {{ option.label }}
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Search
                        </label>
                        <div class="mt-2 flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2">
                            <Icon icon="mdi:magnify" :size="18" class="text-slate-400" />
                            <input
                                v-model.trim="searchTerm"
                                type="text"
                                placeholder="Search by partner, center, or notes"
                                class="w-full bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-500">
                    <Icon icon="mdi:filter-variant" :size="16" />
                    {{ filteredClaims.length }} results
                </div>
            </div>

            <div v-if="globalError" class="mt-4 rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-3 text-sm text-rose-700">
                {{ globalError }}
            </div>
            <div v-if="successMessage" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700">
                {{ successMessage }}
            </div>

            <div v-if="claimsLoading" class="mt-6 flex h-60 items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 text-sm text-slate-500">
                Loading milk collection center claims…
            </div>
            <div
                v-else-if="!filteredClaims.length"
                class="mt-6 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 px-6 py-12 text-center"
            >
                <Icon icon="mdi:inbox-outline" :size="32" class="text-slate-400" />
                <p class="text-sm font-semibold text-slate-600">No claims match the current filters.</p>
                <p class="text-xs text-slate-500">Try removing a filter or switching to a different status.</p>
            </div>
            <div v-else class="mt-6 grid gap-4 2xl:grid-cols-2">
                <article
                    v-for="claim in filteredClaims"
                    :key="claim.id"
                    class="flex h-full flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-base font-semibold text-slate-900">
                                {{ claim.milk_collection_center?.name ?? 'Milk Collection Center' }}
                            </p>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                {{ claim.partner?.name ?? 'Partner' }}
                            </p>
                        </div>
                        <span
                            :class="['rounded-full px-3 py-1 text-xs font-semibold', claimStatusBadgeClasses(claim.status)]"
                        >
                            {{ formatClaimStatus(claim.status) }}
                        </span>
                    </div>

                    <div class="flex items-start gap-2 text-xs text-slate-500">
                        <Icon icon="mdi:map-marker-outline" :size="16" />
                        <span>{{ claim.milk_collection_center?.physical_address || 'Address unavailable' }}</span>
                    </div>

                    <div v-if="claim.message" class="rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500">
                        “{{ claim.message }}”
                    </div>

                    <div class="space-y-1 text-xs text-slate-400">
                        <p>
                            Requested {{ formatDateTime(claim.created_at) }}
                            <span v-if="claim.requestedBy?.name"> by {{ claim.requestedBy.name }}</span>
                        </p>
                        <p v-if="claim.responded_at">
                            Updated {{ formatDateTime(claim.responded_at) }}
                            <span v-if="claim.respondedBy?.name"> by {{ claim.respondedBy.name }}</span>
                        </p>
                    </div>

                    <div v-if="claim.status === 'pending' && canModerate" class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Response notes
                        </label>
                        <textarea
                            v-model.trim="responseNotes[claim.id]"
                            rows="3"
                            placeholder="Optional: add context for the partner team"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        ></textarea>
                        <div class="flex flex-wrap justify-end gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="isProcessing(claim.id)"
                                @click="rejectClaim(claim)"
                            >
                                <Icon icon="mdi:thumb-down-outline" :size="16" />
                                {{ isProcessing(claim.id) && pendingAction[claim.id] === 'reject' ? 'Rejecting…' : 'Reject' }}
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-70"
                                :disabled="isProcessing(claim.id)"
                                @click="approveClaim(claim)"
                            >
                                <Icon icon="mdi:check" :size="16" />
                                {{ isProcessing(claim.id) && pendingAction[claim.id] === 'approve' ? 'Approving…' : 'Approve' }}
                            </button>
                        </div>
                    </div>

                    <div v-else-if="claim.response_notes" class="rounded-xl bg-emerald-50/70 px-3 py-2 text-xs text-emerald-700">
                        {{ claim.response_notes }}
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue';
import Icon from '../../components/shared/Icon.vue';
import { usePartnerStore, type MilkCollectionCenterClaim } from '../../stores/partnerStore';
import { usePermissions } from '../../composables/usePermissions';

type StatusFilter = 'all' | 'pending' | 'approved' | 'rejected';

const partnerStore = usePartnerStore();
const { isAdmin, isSuperAdmin } = usePermissions();

const statusOptions: Array<{ label: string; value: StatusFilter; icon: string }> = [
    { label: 'All', value: 'all', icon: 'mdi:view-grid-outline' },
    { label: 'Pending', value: 'pending', icon: 'mdi:clock-outline' },
    { label: 'Approved', value: 'approved', icon: 'mdi:check-circle-outline' },
    { label: 'Rejected', value: 'rejected', icon: 'mdi:close-circle-outline' },
];

const statusFilter = ref<StatusFilter>('pending');
const searchTerm = ref('');
const responseNotes = reactive<Record<number, string>>({});
const processing = reactive<Record<number, boolean>>({});
const pendingAction = reactive<Record<number, 'approve' | 'reject' | null>>({});

const globalError = ref<string | null>(null);
const successMessage = ref<string | null>(null);

const canModerate = computed(() => isAdmin.value || isSuperAdmin.value);

const claimsLoading = computed(() => partnerStore.claimsLoading);
const claimsError = computed(() => partnerStore.claimsError);

const allClaims = computed(() => partnerStore.claims ?? []);

const filteredClaims = computed(() => {
    let results = [...allClaims.value];

    if (statusFilter.value !== 'all') {
        results = results.filter(claim => claim.status === statusFilter.value);
    }

    if (searchTerm.value) {
        const query = searchTerm.value.toLowerCase();
        results = results.filter(claim => {
            const centerName = claim.milk_collection_center?.name?.toLowerCase() ?? '';
            const partnerName = claim.partner?.name?.toLowerCase() ?? '';
            const message = claim.message?.toLowerCase() ?? '';
            const notes = claim.response_notes?.toLowerCase() ?? '';
            return (
                centerName.includes(query) ||
                partnerName.includes(query) ||
                message.includes(query) ||
                notes.includes(query)
            );
        });
    }

    return results.sort((a, b) => {
        const dateA = new Date(b.created_at ?? '').getTime();
        const dateB = new Date(a.created_at ?? '').getTime();
        return dateA - dateB;
    });
});

const pendingCount = computed(() => allClaims.value.filter(claim => claim.status === 'pending').length);
const approvedCount = computed(() => allClaims.value.filter(claim => claim.status === 'approved').length);
const rejectedCount = computed(() => allClaims.value.filter(claim => claim.status === 'rejected').length);

const setStatus = (status: StatusFilter) => {
    statusFilter.value = status;
};

const isProcessing = (claimId: number) => processing[claimId] ?? false;

const refreshClaims = async () => {
    globalError.value = null;
    try {
        await partnerStore.fetchClaims();
    } catch (error: any) {
        globalError.value =
            error?.response?.data?.message || claimsError.value || 'Failed to load claims. Please try again.';
    }
};

const handleAction = async (
    claim: MilkCollectionCenterClaim,
    type: 'approve' | 'reject',
    action: (claimId: number, payload?: { response_notes?: string | null }) => Promise<MilkCollectionCenterClaim>,
) => {
    processing[claim.id] = true;
    pendingAction[claim.id] = type;
    globalError.value = null;
    successMessage.value = null;

    try {
        await action(claim.id, {
            response_notes: responseNotes[claim.id] || undefined,
        });
        successMessage.value = type === 'approve' ? 'Claim approved successfully.' : 'Claim rejected.';
        responseNotes[claim.id] = '';
        await partnerStore.fetchClaims();
    } catch (error: any) {
        globalError.value =
            error?.response?.data?.message ||
            claimsError.value ||
            (type === 'approve' ? 'Failed to approve claim.' : 'Failed to reject claim.');
    } finally {
        processing[claim.id] = false;
        pendingAction[claim.id] = null;
    }
};

const approveClaim = (claim: MilkCollectionCenterClaim) =>
    handleAction(claim, 'approve', partnerStore.approveClaim);

const rejectClaim = (claim: MilkCollectionCenterClaim) =>
    handleAction(claim, 'reject', partnerStore.rejectClaim);

const formatDateTime = (value?: string | null) => {
    if (!value) return '';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    }).format(new Date(value));
};

const formatClaimStatus = (status?: string | null) => {
    if (!status) return 'Pending';
    return status
        .split('_')
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
};

const claimStatusBadgeClasses = (status?: string | null) => {
    switch (status) {
        case 'approved':
            return 'bg-emerald-50 text-emerald-600 border border-emerald-200';
        case 'rejected':
            return 'bg-rose-50 text-rose-600 border border-rose-200';
        default:
            return 'bg-amber-50 text-amber-600 border border-amber-200';
    }
};

onMounted(async () => {
    await refreshClaims();
    if (claimsError.value) {
        globalError.value = claimsError.value;
    }
});
</script>



