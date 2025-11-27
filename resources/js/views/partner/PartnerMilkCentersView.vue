<template>
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 px-8 py-10 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(56,189,248,0.25),transparent_65%)] opacity-80"></div>
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Partner · Milk Collection
                    </p>
                    <h2 class="text-3xl font-semibold tracking-tight lg:text-4xl">
                        Manage Your Collection Network
                    </h2>
                    <p class="text-sm text-white/70">
                        Register new centers, keep tabs on operations, and claim existing facilities to ensure every
                        liter is captured under your partnership umbrella.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                        @click="openCreateModal"
                    >
                        <Icon icon="mdi:plus" :size="18" />
                        Register new center
                    </button>
                    <router-link
                        to="/partner/dashboard"
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20"
                    >
                        <Icon icon="mdi:view-dashboard-outline" :size="18" />
                        Back to overview
                    </router-link>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Your collection centers</h3>
                    <p class="text-sm text-slate-500">
                        {{ milkCentersCount }} centers registered under {{ partnerName }}.
                    </p>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <Icon icon="mdi:information-outline" :size="16" />
                    Newly added or claimed centers appear instantly in this list.
                </div>
            </div>

            <div v-if="centers.length" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="center in centers"
                    :key="center.id"
                    :id="`center-card-${center.id}`"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white/95 p-5 transition"
                    :class="focusId === center.id ? 'ring-2 ring-sky-500' : 'hover:border-slate-300 hover:bg-white'"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-base font-semibold text-slate-900">{{ center.name }}</h4>
                            <p class="mt-1 text-xs uppercase tracking-wide text-slate-400">
                                {{ center.registration_number || 'Unregistered' }}
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-600"
                        >
                            <Icon icon="mdi:thermometer-lines" :size="14" />
                            {{ center.cooler_capacity_liters ? `${center.cooler_capacity_liters} L` : 'Capacity —' }}
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ center.physical_address }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-[11px] uppercase tracking-wide text-slate-400">
                        <span
                            v-for="chip in formatArea(center.area)"
                            :key="chip"
                            class="rounded-full bg-slate-100 px-2 py-0.5"
                        >
                            {{ chip }}
                        </span>
                    </div>

                    <dl class="mt-5 grid grid-cols-2 gap-3 text-xs text-slate-500">
                        <div>
                            <dt class="uppercase tracking-[0.2em]">Manager</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-800">
                                {{ center.manager_name || 'Not set' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="uppercase tracking-[0.2em]">Phone</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-800">
                                {{ center.manager_phone || '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="uppercase tracking-[0.2em]">Farmers</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ center.farmers_count ?? 0 }}
                            </dd>
                        </div>
                        <div>
                            <dt class="uppercase tracking-[0.2em]">Claims</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">
                                {{ center.pending_claims_count ?? 0 }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-2 text-xs">
                        <router-link
                            :to="{ name: 'partner-farmers', query: { center: center.id } }"
                            class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                        >
                            Farmers
                            <Icon icon="mdi:arrow-right" :size="14" />
                        </router-link>
                        <router-link
                            :to="{ name: 'partner-dashboard', query: { highlight: center.id } }"
                            class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                        >
                            Insights
                            <Icon icon="mdi:chart-bar" :size="14" />
                        </router-link>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-12 text-center text-sm text-slate-500"
            >
                No milk collection centers yet. Register your first center to get started with operations tracking.
                <div class="mt-4">
                    <button
                        class="inline-flex items-center gap-2 rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600"
                        @click="openCreateModal"
                    >
                        <Icon icon="mdi:plus" :size="16" />
                        Add milk collection center
                    </button>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Claim an existing center</h3>
                    <p class="text-sm text-slate-500">
                        Search the directory and request ownership. Admins will review and approve claims.
                    </p>
                </div>
                <div class="text-xs text-slate-500">
                    {{ filteredResults.length ? `${filteredResults.length} results` : 'No results yet' }}
                </div>
            </div>

            <form class="mt-6 flex flex-col gap-4 md:flex-row" @submit.prevent="performSearch">
                <div class="flex-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Search by name or registration number
                    </label>
                    <input
                        v-model.trim="claimSearch"
                        type="text"
                        placeholder="e.g. Nyendo MCC or MCC-UG-001"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                    />
                </div>
                <div class="flex w-full gap-3 md:w-auto md:flex-col">
                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 md:flex-none"
                        :disabled="searching"
                    >
                        <Icon icon="mdi:magnify" :size="18" />
                        {{ searching ? 'Searching…' : 'Search centers' }}
                    </button>
                    <button
                        type="button"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 md:flex-none"
                        @click="resetSearch"
                    >
                        Clear
                    </button>
                </div>
            </form>

            <div v-if="claimError" class="mt-4 rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-3 text-sm text-rose-700">
                {{ claimError }}
            </div>
            <div
                v-if="claimSuccess"
                class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/70 px-4 py-3 text-sm text-emerald-700"
            >
                {{ claimSuccess }}
            </div>

            <div v-if="filteredResults.length" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="center in filteredResults"
                    :key="center.id"
                    class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-slate-900">{{ center.name }}</p>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                {{ center.registration_number || 'Unregistered' }}
                            </p>
                        </div>
                        <span class="text-xs font-medium text-slate-400">
                            {{ center.partner_id ? 'Assigned' : 'Unassigned' }}
                        </span>
                    </div>
                    <p class="mt-3 leading-relaxed">{{ center.physical_address }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-[11px] uppercase tracking-wide text-slate-400">
                        <span v-for="chip in formatArea(center.area)" :key="chip" class="rounded-full bg-slate-100 px-2 py-0.5">
                            {{ chip }}
                        </span>
                    </div>
                    <textarea
                        v-model="claimMessages[center.id]"
                        rows="2"
                        class="mt-4 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        placeholder="Add an optional note for the admin team"
                    ></textarea>
                    <button
                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="claimingCenterId === center.id"
                        @click="submitClaim(center.id)"
                    >
                        <Icon icon="mdi:hand-extended" :size="18" />
                        {{ claimingCenterId === center.id ? 'Submitting…' : 'Request claim' }}
                    </button>
                </div>
            </div>
            <div
                v-else-if="searched"
                class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-10 text-center text-sm text-slate-500"
            >
                No centers matched your search or they are already assigned. Try a different keyword or contact the
                admin team for assistance.
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Claim history</h3>
                    <p class="text-sm text-slate-500">
                        Track approvals and rejections for milk collection centers tied to your partner.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <Icon icon="mdi:history" :size="16" />
                    {{ resolvedClaims.length }} resolved · {{ pendingClaims.length }} pending
                </div>
            </div>

            <div v-if="sortedResolvedClaims.length" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="claim in sortedResolvedClaims"
                    :key="claim.id"
                    class="flex h-full flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-slate-900">
                                {{ claim.milk_collection_center?.name ?? 'Milk Center' }}
                            </p>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                {{ claim.partner?.name ?? partnerName }}
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
                        <span>
                            {{ claim.milk_collection_center?.physical_address || 'Address unavailable' }}
                        </span>
                    </div>

                    <div v-if="claim.message" class="rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500">
                        “{{ claim.message }}”
                    </div>

                    <div v-if="claim.response_notes" class="rounded-xl bg-emerald-50/60 px-3 py-2 text-xs text-emerald-700">
                        {{ claim.response_notes }}
                    </div>

                    <div class="mt-auto space-y-1 text-xs text-slate-400">
                        <p>
                            Requested {{ formatDateTime(claim.created_at) }}
                            <span v-if="claim.requestedBy?.name"> by {{ claim.requestedBy.name }}</span>
                        </p>
                        <p v-if="claim.responded_at">
                            Resolved {{ formatDateTime(claim.responded_at) }}
                            <span v-if="claim.respondedBy?.name"> by {{ claim.respondedBy.name }}</span>
                        </p>
                    </div>
                </article>
            </div>
            <p
                v-else
                class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-10 text-sm text-slate-500"
            >
                No resolved claims yet. Once admin teams approve or reject your requests, they’ll appear here.
            </p>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/90 p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Team invitations</h3>
                    <p class="text-sm text-slate-500">
                        Invite partner admins or agents to collaborate. Invitations expire automatically if not accepted.
                    </p>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                    @click="openInvitationModal"
                >
                    <Icon icon="mdi:email-plus-outline" :size="18" />
                    Invite member
                </button>
            </div>

    <div
        v-if="inviteSuccess"
        class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700"
    >
        {{ inviteSuccess }}
    </div>
    <div
        v-if="inviteError"
        class="mt-4 rounded-xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm text-rose-700"
    >
        {{ inviteError }}
    </div>

            <div v-if="pendingInvitations.length" class="mt-6 grid gap-4 md:grid-cols-2">
                <div
                    v-for="invite in pendingInvitations"
                    :key="invite.id"
                    class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600"
                >
                    <div class="flex items-center justify-between text-xs uppercase tracking-wide text-slate-400">
                        <span>{{ invite.email }}</span>
                        <span>Pending</span>
                    </div>
                    <p class="mt-2 text-base font-semibold text-slate-900">
                        {{ invite.name || 'Awaiting acceptance' }}
                    </p>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">
                        {{ invite.role.replace('_', ' ') }}
                    </p>
                    <p class="mt-3 text-xs text-slate-500">
                        Sent {{ formatDate(invite.created_at) }}
                    </p>
                    <div class="mt-4 flex justify-end">
                        <button
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="revokingInvitationId === invite.id"
                            @click="revokeInvitation(invite.id)"
                        >
                            <Icon icon="mdi:close-circle-outline" :size="16" />
                            {{ revokingInvitationId === invite.id ? 'Revoking…' : 'Revoke' }}
                        </button>
                    </div>
                </div>
            </div>
            <p v-else class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-10 text-sm text-slate-500">
                No pending invitations. Invite team members to manage MCCs or capture deliveries on your behalf.
            </p>
        </section>

        <CreateMilkCollectionCenterModal :is-open="createModalOpen" @close="closeCreateModal" @created="handleCenterCreated" />
        <InvitePartnerMemberModal
            :is-open="invitationModalOpen"
            :partner-id="partnerId"
            @close="closeInvitationModal"
            @created="handleInvitationCreated"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, nextTick, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import CreateMilkCollectionCenterModal from '../../components/milk-centers/CreateMilkCollectionCenterModal.vue';
import InvitePartnerMemberModal from '../../components/partner/InvitePartnerMemberModal.vue';
import { useAuthStore } from '../../stores/authStore';
import { usePartnerStore, type PartnerInvitation } from '../../stores/partnerStore';
import { useGeographyStore } from '../../stores/geographyStore';

const authStore = useAuthStore();
const partnerStore = usePartnerStore();
const geographyStore = useGeographyStore();
const route = useRoute();
const router = useRouter();

const createModalOpen = ref(false);
const invitationModalOpen = ref(false);
const claimSearch = ref('');
const searched = ref(false);
const claimError = ref<string | null>(null);
const claimSuccess = ref<string | null>(null);
const claimingCenterId = ref<number | null>(null);
const claimMessages = reactive<Record<number, string>>({});
const inviteSuccess = ref<string | null>(null);
const inviteError = ref<string | null>(null);
const revokingInvitationId = ref<number | null>(null);

const partnerId = computed(() => partnerStore.activePartner?.id ?? authStore.user?.partner_id ?? null);
const activePartner = computed(() => partnerStore.activePartner);

const centers = computed(() => activePartner.value?.milk_collection_centers ?? []);
const milkCentersCount = computed(() => centers.value.length);
const partnerName = computed(() => activePartner.value?.name ?? 'your partner');

const pendingInvitations = computed<PartnerInvitation[]>(() =>
    (partnerStore.invitations ?? []).filter(invite => invite.status === 'pending'),
);

const claims = computed(() => partnerStore.claims ?? []);
const pendingClaims = computed(() =>
    claims.value.filter(claim => claim.status === 'pending'),
);
const approvedClaims = computed(() =>
    claims.value.filter(claim => claim.status === 'approved'),
);
const rejectedClaims = computed(() =>
    claims.value.filter(claim => claim.status === 'rejected'),
);
const resolvedClaims = computed(() =>
    claims.value.filter(claim => claim.status !== 'pending'),
);

const sortedResolvedClaims = computed(() =>
    [...resolvedClaims.value].sort((a, b) => {
        const left = new Date(b.responded_at ?? b.updated_at ?? b.created_at ?? '').getTime();
        const right = new Date(a.responded_at ?? a.updated_at ?? a.created_at ?? '').getTime();
        return left - right;
    }),
);

const searching = computed(() => geographyStore.loading.value);

const focusId = computed(() => {
    const value = Number(route.query.focus);
    return Number.isFinite(value) && value > 0 ? value : null;
});

const partnerClaimedCenterIds = computed(() =>
    pendingClaims.value.map(claim => claim.milk_collection_center_id),
);

const filteredResults = computed(() => {
    const partnerOwnedIds = new Set(centers.value.map(center => center.id));
    const pendingClaimIds = new Set(partnerClaimedCenterIds.value);

    const centers = geographyStore.milkCenters.value ?? [];

    return centers
        .filter(center => !partnerOwnedIds.has(center.id))
        .filter(center => !pendingClaimIds.has(center.id))
        .filter(center => !center.partner_id)
        .slice(0, 9);
});

const formatArea = (area?: Record<string, string>) => {
    if (!area) return [];
    return Object.values(area);
};

const formatDate = (value?: string | null) => {
    if (!value) return '';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
    }).format(new Date(value));
};

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

const openCreateModal = () => {
    createModalOpen.value = true;
};

const closeCreateModal = () => {
    createModalOpen.value = false;
};

const openInvitationModal = () => {
    inviteSuccess.value = null;
    inviteError.value = null;
    invitationModalOpen.value = true;
};

const closeInvitationModal = () => {
    invitationModalOpen.value = false;
};

const handleInvitationCreated = async () => {
    inviteSuccess.value = 'Invitation sent successfully.';
    inviteError.value = null;
    await refreshPartner();
};

const revokeInvitation = async (invitationId: number) => {
    if (!partnerId.value) return;
    revokingInvitationId.value = invitationId;
    inviteError.value = null;
    inviteSuccess.value = null;

    try {
        await partnerStore.revokeInvitation(partnerId.value, invitationId);
        inviteSuccess.value = 'Invitation revoked.';
        await refreshPartner();
    } catch (error: any) {
        inviteError.value =
            error?.response?.data?.message || partnerStore.invitationsError || 'Failed to revoke invitation.';
    } finally {
        revokingInvitationId.value = null;
    }
};

const handleCenterCreated = async () => {
    claimSuccess.value = 'Milk collection center added successfully.';
    claimError.value = null;
    claimSearch.value = '';
    await refreshPartner();
};

const refreshPartner = async () => {
    if (!partnerId.value) return;
    await partnerStore.fetchPartner(partnerId.value);
    await partnerStore.fetchClaims();
};

const performSearch = async () => {
    if (!claimSearch.value) {
        claimError.value = 'Enter a search term to find existing centers.';
        claimSuccess.value = null;
        return;
    }

    claimError.value = null;
    claimSuccess.value = null;
    searched.value = true;

    try {
        await geographyStore.getMilkCollectionCenters({
            search: claimSearch.value,
            per_page: 15,
        });
    } catch (error: any) {
        claimError.value =
            error?.response?.data?.message || 'Failed to search centers. Please try again or refine your query.';
    }
};

const resetSearch = () => {
    claimSearch.value = '';
    searched.value = false;
    claimError.value = null;
    claimSuccess.value = null;
};

const submitClaim = async (centerId: number) => {
    if (!partnerId.value) return;
    claimingCenterId.value = centerId;
    claimError.value = null;
    claimSuccess.value = null;

    try {
        await partnerStore.requestClaim(centerId, {
            message: claimMessages[centerId] || undefined,
        });
        claimSuccess.value = 'Claim request submitted. Admin team will review shortly.';
        delete claimMessages[centerId];
        await partnerStore.fetchClaims();
    } catch (error: any) {
        claimError.value =
            error?.response?.data?.message || 'Unable to submit claim. This center might already be claimed.';
    } finally {
        claimingCenterId.value = null;
    }
};

const initialize = async (id: number) => {
    inviteSuccess.value = null;
    inviteError.value = null;
    if (!activePartner.value || activePartner.value.id !== id) {
        await partnerStore.fetchPartner(id);
    }
    await partnerStore.fetchClaims();
};

watch(
    partnerId,
    async id => {
        if (!id) return;
        await initialize(id);
    },
    { immediate: true },
);

watch(
    () => focusId.value,
    async id => {
        if (!id) return;
        await nextTick();
        const el = document.getElementById(`center-card-${id}`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        const nextQuery = { ...route.query };
        delete nextQuery.focus;
        router.replace({ query: nextQuery });
    },
);
</script>

