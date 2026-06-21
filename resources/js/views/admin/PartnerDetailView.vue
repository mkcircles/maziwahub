<template>
    <div class="space-y-6">
        <div v-if="partner" class="space-y-10">
            <section
                class="relative overflow-hidden rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 px-6 py-8 sm:px-8 sm:py-10 text-white shadow-lg">
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(76,201,240,0.15),transparent_60%)] opacity-90">
                </div>
                <div class="relative z-10 flex flex-col gap-6">
                    <router-link to="/admin/partners"
                        class="inline-flex items-center gap-1.5 self-start rounded-full bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-1.5 text-xs font-semibold text-white transition duration-200">
                        <Icon icon="mdi:arrow-left" :size="14" />
                        Back to Partners
                    </router-link>

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-white/60">Partner Overview
                            </p>
                            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl mt-1.5">{{ partner.name }}</h1>
                            <p class="text-xs text-white/70 font-medium mt-2">
                                {{ partner.description || 'No description provided for this partner yet.' }}
                            </p>
                            <div class="mt-4 flex flex-wrap items-center gap-2 text-[10px] sm:text-xs">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-semibold"
                                    :class="partner.is_active ? 'bg-emerald-500/20 text-emerald-100 border border-emerald-500/30' : 'bg-white/10 text-white/80 border border-white/10'">
                                    <Icon
                                        :icon="partner.is_active ? 'mdi:check-circle-outline' : 'mdi:pause-circle-outline'"
                                        :size="14" />
                                    {{ partner.is_active ? 'Active Partner' : 'Inactive Partner' }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                    <Icon icon="mdi:storefront-outline" :size="14" />
                                    {{ partner.milk_collection_centers?.length ?? 0 }} Centers
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                    <Icon icon="mdi:alert-circle-outline" :size="14" />
                                    {{ partner.pending_claims_count ?? 0 }} Pending Claims
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                class="inline-flex items-center gap-1.5 rounded bg-white/10 hover:bg-white/20 border border-white/10 px-3.5 py-1.5 text-xs font-bold text-white transition duration-200 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="togglingStatus" @click="toggleStatus">
                                <Icon :icon="partner.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'"
                                    :size="14" />
                                {{ partner.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 rounded bg-white/10 hover:bg-white/20 border border-white/10 px-3.5 py-1.5 text-xs font-bold text-white transition duration-200"
                                @click="openEditModal">
                                <Icon icon="mdi:pencil" :size="14" />
                                Edit Partner
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 rounded bg-white px-3.5 py-1.5 text-xs font-bold text-surface-900 transition hover:bg-surface-100"
                                @click="openInvitationModal">
                                <Icon icon="mdi:email-plus-outline" :size="14" />
                                Invite Member
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="actionSuccess"
                class="rounded-md border border-emerald-200 bg-emerald-50/80 px-6 py-4 text-sm text-emerald-700 shadow-sm shadow-emerald-100">
                {{ actionSuccess }}
            </section>
            <section v-if="actionError"
                class="rounded-md border border-rose-200 bg-rose-50/80 px-6 py-4 text-sm text-rose-700 shadow-sm shadow-rose-100">
                {{ actionError }}
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatisticalCard icon="mdi:storefront-outline" icon-class="text-primary-600">
                    <template #title>Collection Centers</template>
                    <template #default>{{ partner.milk_collection_centers?.length ?? 0 }}</template>
                    <template #caption>Assigned to this partner</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:alert-circle-outline" icon-class="text-amber-500">
                    <template #title>Pending Claims</template>
                    <template #default>{{ pendingClaims.length }}</template>
                    <template #caption>Awaiting your review</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:email-outline" icon-class="text-primary-500">
                    <template #title>Open Invitations</template>
                    <template #default>{{ pendingInvitations.length }}</template>
                    <template #caption>Pending onboarding</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:account-outline" icon-class="text-emerald-500">
                    <template #title>Team Members</template>
                    <template #default>{{ partner.users?.length ?? 0 }}</template>
                    <template #caption>Active partner accounts</template>
                </StatisticalCard>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-4 rounded-md border border-surface-200 bg-white/95 p-6 shadow-sm shadow-slate-100">
                    <h2 class="text-lg font-semibold text-surface-900">Contact & Registration</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="item in contactDetails" :key="item.label"
                            class="rounded-lg border border-surface-100 bg-surface-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-surface-400">{{ item.label }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-surface-700">{{ item.value }}</p>
                        </div>
                    </div>
                    <div v-if="partner.address" class="rounded-lg border border-surface-100 bg-surface-50/80 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-surface-400">Address</p>
                        <p class="mt-1 text-sm font-medium text-surface-700">{{ partner.address }}</p>
                    </div>
                </div>

                <div class="space-y-4 rounded-md border border-surface-200 bg-white/95 p-6 shadow-sm shadow-slate-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-surface-900">Invitations</h2>
                        <button
                            class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-600 transition hover:bg-surface-100 hover:text-surface-800"
                            @click="openInvitationModal">
                            <Icon icon="mdi:email-plus-outline" :size="16" />
                            Invite member
                        </button>
                    </div>
                    <div v-if="invitationsLoading"
                        class="flex h-40 items-center justify-center rounded-lg border border-dashed border-surface-200 bg-surface-50/80 text-sm text-surface-500">
                        Loading invitations…
                    </div>
                    <div v-else-if="!partnerInvitations.length"
                        class="flex h-40 items-center justify-center rounded-lg border border-dashed border-surface-200 bg-surface-50/80 text-sm text-surface-500">
                        No invitations found. Send one to onboard a team member.
                    </div>
                    <div v-else class="space-y-3">
                        <div v-for="invite in partnerInvitations" :key="invite.id"
                            class="flex flex-col gap-2 rounded-lg border border-surface-100 bg-white p-4 shadow-inner shadow-slate-100">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-surface-900">{{ invite.name || invite.email }}</p>
                                    <p class="text-xs text-surface-500">{{ invite.email }}</p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold"
                                    :class="invitationStatusClass(invite.status)">
                                    <span class="h-2 w-2 rounded-full"
                                        :class="invitationDotClass(invite.status)"></span>
                                    {{ formatInvitationStatus(invite.status) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-surface-500">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-surface-100 px-2 py-0.5 font-medium text-surface-600">
                                    <Icon icon="mdi:account-badge-outline" :size="14" />
                                    {{ invite.role === 'partner_admin' ? 'Partner Admin' : 'Partner Agent' }}
                                </span>
                                <span>
                                    Sent {{ formatDateTime(invite.created_at) }}
                                </span>
                                <span v-if="invite.expires_at">
                                    Expires {{ formatDateTime(invite.expires_at) }}
                                </span>
                            </div>
                            <div class="flex justify-end gap-2" v-if="invite.status === 'pending'">
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-600 transition hover:bg-surface-100 hover:text-surface-800 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="revokingInvitationId === invite.id" @click="revokeInvitation(invite)">
                                    <Icon icon="mdi:cancel" :size="16" />
                                    {{ revokingInvitationId === invite.id ? 'Revoking…' : 'Revoke' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-md border border-surface-200 bg-white/95 p-8 shadow-sm shadow-slate-100">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-surface-900">Milk Collection Centers</h2>
                        <p class="text-sm text-surface-500">
                            Facilities currently associated with {{ partner.name }}.
                        </p>
                    </div>
                    <router-link to="/admin/milk-collection-centers"
                        class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-600 transition hover:bg-surface-100 hover:text-surface-800">
                        <Icon icon="mdi:open-in-new" :size="16" />
                        Manage centers
                    </router-link>
                </div>
                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                    v-if="partner.milk_collection_centers?.length">
                    <CountryMilkCenterCard v-for="center in partner.milk_collection_centers" :key="center.id"
                        :center="center" :minified="true" />
                </div>
                <div v-else
                    class="mt-6 flex h-48 items-center justify-center rounded-lg border border-dashed border-surface-200 bg-surface-50/80 text-sm text-surface-500">
                    No collection centers assigned yet.
                </div>
            </section>

            <section class="rounded-md border border-surface-200 bg-white/95 p-8 shadow-sm shadow-slate-100">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-surface-900">Milk Collection Center Claims</h2>
                        <p class="text-sm text-surface-500">
                            Review and action claims submitted by {{ partner.name }}.
                        </p>
                    </div>
                    <p
                        class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-500">
                        <Icon icon="mdi:file-document-outline" :size="16" />
                        {{ partnerClaims.length }} total claims
                    </p>
                </div>

                <div v-if="claimsLoading"
                    class="mt-6 flex h-48 items-center justify-center rounded-lg border border-dashed border-surface-200 bg-surface-50/80 text-sm text-surface-500">
                    Loading claims…
                </div>
                <div v-else-if="!partnerClaims.length"
                    class="mt-6 flex h-48 items-center justify-center rounded-lg border border-dashed border-surface-200 bg-surface-50/80 text-sm text-surface-500">
                    No claims found for this partner.
                </div>
                <div v-else class="mt-6 space-y-4">
                    <div v-for="claim in partnerClaims" :key="claim.id"
                        class="space-y-3 rounded-lg border border-surface-100 bg-white p-5 shadow-inner shadow-slate-100">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-surface-900">
                                    {{ claim.milk_collection_center?.name ?? 'Milk Collection Center' }}
                                </p>
                                <p class="text-xs text-surface-500">
                                    {{ claim.milk_collection_center?.physical_address || 'Address unavailable' }}
                                </p>
                            </div>
                            <span
                                class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold"
                                :class="claimStatusBadgeClasses(claim.status)">
                                <span class="h-2 w-2 rounded-full" :class="claimDotClass(claim.status)"></span>
                                {{ formatClaimStatus(claim.status) }}
                            </span>
                        </div>
                        <div v-if="claim.message" class="rounded-lg bg-surface-50/80 p-3 text-xs text-surface-600">
                            “{{ claim.message }}”
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs text-surface-500">
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-surface-100 px-2 py-0.5 font-medium text-surface-600">
                                <Icon icon="mdi:account-outline" :size="14" />
                                Requested by {{ claim.requested_by_user_id ? requesterName(claim.requested_by_user_id) : 'Partner Admin' }}
                            </span>
                            <span>
                                Submitted {{ formatDateTime(claim.created_at) }}
                            </span>
                            <span v-if="claim.responded_at">
                                Updated {{ formatDateTime(claim.responded_at) }}
                            </span>
                        </div>

                        <template v-if="claim.status === 'pending' && canModerate">
                            <textarea v-model="responseNotes[claim.id]" rows="2"
                                placeholder="Add response notes (optional)"
                                class="w-full rounded-md border border-surface-200 px-3 py-2 text-sm text-surface-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"></textarea>
                            <div class="flex flex-wrap justify-end gap-2">
                                <button
                                    class="inline-flex items-center gap-2 rounded-full border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-600 transition hover:bg-surface-100 hover:text-surface-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="isProcessing(claim.id)" @click="rejectClaim(claim)">
                                    <Icon icon="mdi:thumb-down-outline" :size="16" />
                                    {{ isProcessing(claim.id) && pendingAction[claim.id] === 'reject' ? 'Rejecting…' : 'Reject' }}
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-70"
                                    :disabled="isProcessing(claim.id)" @click="approveClaim(claim)">
                                    <Icon icon="mdi:check-circle-outline" :size="16" />
                                    {{ isProcessing(claim.id) && pendingAction[claim.id] === 'approve' ? 'Approving…' :
                                        'Approve' }}
                                </button>
                            </div>
                        </template>
                        <div v-else-if="claim.response_notes"
                            class="rounded-lg border border-emerald-200 bg-emerald-50/70 p-3 text-xs text-emerald-700">
                            {{ claim.response_notes }}
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div v-else
            class="flex h-64 items-center justify-center rounded-md border border-dashed border-surface-200 bg-surface-50/80 text-sm text-surface-500">
            Loading partner details…
        </div>

        <PartnerFormModal :is-open="showEditModal" :partner="partner" @close="closeEditModal"
            @saved="handleEditSaved" />
        <InvitePartnerMemberModal :is-open="showInvitationModal" :partner-id="partner?.id ?? null"
            @close="closeInvitationModal" @created="handleInvitationCreated" />
        <section v-if="!partner && actionSuccess"
            class="rounded-md border border-emerald-200 bg-emerald-50/80 px-6 py-4 text-sm text-emerald-700 shadow-sm shadow-emerald-100">
            {{ actionSuccess }}
        </section>
        <section v-if="!partner && actionError"
            class="rounded-md border border-rose-200 bg-rose-50/80 px-6 py-4 text-sm text-rose-700 shadow-sm shadow-rose-100">
            {{ actionError }}
        </section>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import CountryMilkCenterCard from '../../components/geography/CountryMilkCenterCard.vue';
import PartnerFormModal from '../../components/partners/PartnerFormModal.vue';
import InvitePartnerMemberModal from '../../components/partner/InvitePartnerMemberModal.vue';
import { usePartnerStore, type MilkCollectionCenterClaim, type PartnerInvitation } from '../../stores/partnerStore';
import { usePermissions } from '../../composables/usePermissions';

const route = useRoute();
const router = useRouter();
const partnerStore = usePartnerStore();
const { isAdmin, isSuperAdmin } = usePermissions();

const responseNotes = reactive<Record<number, string>>({});
const pendingAction = reactive<Record<number, 'approve' | 'reject' | null>>({});
const processing = reactive<Record<number, boolean>>({});
const togglingStatus = ref(false);
const revokingInvitationId = ref<number | null>(null);
const actionError = ref<string | null>(null);
const actionSuccess = ref<string | null>(null);

const showEditModal = ref(false);
const showInvitationModal = ref(false);

const partnerId = computed(() => Number(route.params.id));
const partner = computed(() => partnerStore.activePartner);
const partnerInvitations = computed(() => partnerStore.invitations);
const claims = computed(() => partnerStore.claims ?? []);
const invitationsLoading = computed(() => partnerStore.invitationsLoading);
const claimsLoading = computed(() => partnerStore.claimsLoading);
const canModerate = computed(() => isAdmin.value || isSuperAdmin.value);

const pendingInvitations = computed(() =>
    partnerInvitations.value.filter(invitation => invitation.status === 'pending'),
);

const pendingClaims = computed(() =>
    claims.value.filter(claim => claim.status === 'pending' && claim.partner_id === partner.value?.id),
);

const partnerClaims = computed(() =>
    claims.value.filter(claim => claim.partner_id === partner.value?.id),
);

const contactDetails = computed(() => {
    if (!partner.value) {
        return [] as Array<{ label: string; value: string }>;
    }

    return [
        { label: 'Email', value: partner.value.email },
        partner.value.phone ? { label: 'Phone', value: partner.value.phone } : null,
        partner.value.registration_number
            ? { label: 'Registration', value: partner.value.registration_number }
            : null,
        partner.value.website ? { label: 'Website', value: partner.value.website } : null,
        partner.value.contact_name
            ? {
                label: 'Primary Contact',
                value: `${partner.value.contact_name}${partner.value.contact_title ? ` — ${partner.value.contact_title}` : ''}`,
            }
            : null,
        partner.value.city || partner.value.country
            ? {
                label: 'Location',
                value: [partner.value.city, partner.value.country].filter(Boolean).join(', '),
            }
            : null,
    ].filter(Boolean) as Array<{ label: string; value: string }>;
});

const invitationStatusClass = (status: PartnerInvitation['status']) => {
    switch (status) {
        case 'accepted':
            return 'border border-emerald-200 bg-emerald-50/80 text-emerald-700';
        case 'revoked':
        case 'expired':
            return 'border border-rose-200 bg-rose-50/80 text-rose-600';
        default:
            return 'border border-amber-200 bg-amber-50/80 text-amber-600';
    }
};

const invitationDotClass = (status: PartnerInvitation['status']) => {
    switch (status) {
        case 'accepted':
            return 'bg-emerald-500';
        case 'revoked':
        case 'expired':
            return 'bg-rose-500';
        default:
            return 'bg-amber-500';
    }
};

const formatInvitationStatus = (status: PartnerInvitation['status']) =>
    status
        .split('_')
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');

const claimStatusBadgeClasses = (status: MilkCollectionCenterClaim['status']) => {
    switch (status) {
        case 'approved':
            return 'border border-emerald-200 bg-emerald-50/80 text-emerald-700';
        case 'rejected':
            return 'border border-rose-200 bg-rose-50/80 text-rose-600';
        default:
            return 'border border-amber-200 bg-amber-50/80 text-amber-600';
    }
};

const claimDotClass = (status: MilkCollectionCenterClaim['status']) => {
    switch (status) {
        case 'approved':
            return 'bg-emerald-500';
        case 'rejected':
            return 'bg-rose-500';
        default:
            return 'bg-amber-500';
    }
};

const formatClaimStatus = (status: MilkCollectionCenterClaim['status']) =>
    status
        .split('_')
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');

const formatDateTime = (value?: string | null) => {
    if (!value) return '---';
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    }).format(new Date(value));
};

const requesterName = (userId: number | null | undefined) => {
    if (!userId) {
        return 'Partner team';
    }
    const match = partner.value?.users?.find(user => user.id === userId);
    return match?.name ?? 'Partner team';
};

const isProcessing = (claimId: number) => processing[claimId] ?? false;

const loadPartner = async (id: number) => {
    if (!Number.isFinite(id)) {
        router.replace({ name: 'admin-partners' });
        return;
    }
    try {
        await partnerStore.fetchPartner(id);
        await partnerStore.fetchClaims();
        actionError.value = null;
    } catch (error: any) {
        actionError.value =
            error?.response?.data?.message || partnerStore.partnerError || 'Failed to load partner details.';
    }
};

const refreshPartner = async () => {
    if (!partnerId.value) return;
    await loadPartner(partnerId.value);
};

const toggleStatus = async () => {
    if (!partner.value) return;
    togglingStatus.value = true;
    actionError.value = null;
    try {
        await partnerStore.togglePartnerStatus(partner.value.id, !partner.value.is_active);
        actionSuccess.value = partner.value.is_active ? 'Partner deactivated.' : 'Partner activated.';
        await refreshPartner();
    } catch (error: any) {
        actionError.value = error?.response?.data?.message || 'Failed to update partner status.';
    } finally {
        togglingStatus.value = false;
    }
};

const openEditModal = () => {
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const handleEditSaved = async () => {
    showEditModal.value = false;
    actionSuccess.value = 'Partner details updated successfully.';
    await refreshPartner();
};

const openInvitationModal = () => {
    showInvitationModal.value = true;
};

const closeInvitationModal = () => {
    showInvitationModal.value = false;
};

const handleInvitationCreated = async () => {
    showInvitationModal.value = false;
    actionSuccess.value = 'Invitation sent successfully.';
    await refreshPartner();
};

const revokeInvitation = async (invite: PartnerInvitation) => {
    if (!partner.value) return;
    revokingInvitationId.value = invite.id;
    actionError.value = null;
    actionSuccess.value = null;
    try {
        await partnerStore.revokeInvitation(partner.value.id, invite.id);
        actionSuccess.value = 'Invitation revoked.';
        await refreshPartner();
    } catch (error: any) {
        actionError.value = error?.response?.data?.message || 'Failed to revoke invitation.';
    } finally {
        revokingInvitationId.value = null;
    }
};

const handleClaimAction = async (
    claim: MilkCollectionCenterClaim,
    type: 'approve' | 'reject',
    action: (claimId: number, payload?: { response_notes?: string | null }) => Promise<MilkCollectionCenterClaim>,
) => {
    processing[claim.id] = true;
    pendingAction[claim.id] = type;
    actionError.value = null;
    actionSuccess.value = null;
    try {
        await action(claim.id, {
            response_notes: responseNotes[claim.id] || undefined,
        });
        actionSuccess.value = type === 'approve' ? 'Claim approved.' : 'Claim rejected.';
        responseNotes[claim.id] = '';
        await partnerStore.fetchClaims();
        await refreshPartner();
    } catch (error: any) {
        actionError.value =
            error?.response?.data?.message || (type === 'approve' ? 'Failed to approve claim.' : 'Failed to reject claim.');
    } finally {
        processing[claim.id] = false;
        pendingAction[claim.id] = null;
    }
};

const approveClaim = (claim: MilkCollectionCenterClaim) =>
    handleClaimAction(claim, 'approve', partnerStore.approveClaim);

const rejectClaim = (claim: MilkCollectionCenterClaim) =>
    handleClaimAction(claim, 'reject', partnerStore.rejectClaim);

watch(
    () => partnerId.value,
    (newId, oldId) => {
        if (newId !== oldId && !Number.isNaN(newId)) {
            loadPartner(newId);
        }
    }
);

onMounted(() => {
    loadPartner(partnerId.value);
});

watch(
    () => partnerClaims.value,
    claimsList => {
        claimsList.forEach(claim => {
            if (responseNotes[claim.id] === undefined) {
                responseNotes[claim.id] = '';
            }
        });
    },
    { immediate: true },
);

</script>
