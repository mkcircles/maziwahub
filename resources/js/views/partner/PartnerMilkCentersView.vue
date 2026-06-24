<template>
    <div class="space-y-6 pb-16 min-h-full">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-surface-900">Collection Network</h1>
                <p class="text-xs text-surface-500 font-medium">Register centers, request claims for existing facilities, and invite team members.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="ynex-btn-primary py-1.5 px-3 text-xs flex items-center gap-1.5"
                    @click="openCreateModal"
                >
                    <Icon icon="mdi:plus" :size="16" />
                    Register New Center
                </button>
                <router-link
                    to="/partner/dashboard"
                    class="ynex-btn-secondary py-1.5 px-3 text-xs flex items-center gap-1.5"
                >
                    <Icon icon="mdi:view-dashboard-outline" :size="16" />
                    Dashboard
                </router-link>
            </div>
        </div>

        <!-- Your Collection Centers -->
        <div class="ynex-card">
            <div class="ynex-card-header">
                <div>
                    <h2 class="text-sm font-bold text-surface-800">Your Collection Centers</h2>
                    <p class="text-[11px] text-surface-400 font-medium mt-0.5">{{ milkCentersCount }} active facilities registered under {{ partnerName }}.</p>
                </div>
            </div>
            <div class="p-5">
                <div v-if="centers.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="center in centers"
                        :key="center.id"
                        :id="`center-card-${center.id}`"
                        class="pl-7 pr-5 py-5 rounded border border-surface-200 bg-white relative overflow-hidden transition duration-300 hover:shadow-lg hover:-translate-y-1 center-card-container"
                        :class="focusId === center.id ? 'ring-2 ring-primary-500' : ''"
                    >
                        <!-- Left indicator bar -->
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary-500 to-indigo-600"></div>

                        <!-- Top Title & Badge row -->
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded bg-primary-50 text-primary-600 flex items-center justify-center border border-primary-100 flex-shrink-0">
                                <Icon icon="mdi:storefront-outline" :size="20" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm font-bold text-surface-800 truncate" :title="center.name">{{ center.name }}</h4>
                                <p class="text-[9px] font-bold uppercase tracking-wider text-surface-400 mt-0.5">
                                    {{ center.registration_number || 'Unregistered' }}
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded bg-primary-50 px-2 py-0.5 text-[10px] font-bold text-primary-700 border border-primary-100 flex-shrink-0">
                                <Icon icon="mdi:thermometer-lines" :size="12" />
                                {{ center.cooler_capacity_liters ? `${center.cooler_capacity_liters} L` : 'Capacity —' }}
                            </span>
                        </div>

                        <!-- Address description -->
                        <p class="mt-3.5 text-xs leading-relaxed text-surface-500 line-clamp-2 min-h-[2.5rem]">
                            {{ center.physical_address }}
                        </p>

                        <!-- Region / Location chips -->
                        <div class="mt-3 flex flex-wrap gap-1 text-[9px] uppercase font-bold tracking-wider text-surface-400">
                            <span
                                v-for="chip in formatArea(center.area)"
                                :key="chip"
                                class="rounded bg-surface-100 px-2 py-0.5 text-surface-600 border border-surface-200/30"
                            >
                                {{ chip }}
                            </span>
                        </div>

                        <!-- Info Grid Panel (Four Quadrants) -->
                        <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-surface-100">
                            <div class="bg-surface-50 p-2 rounded border border-surface-200/50">
                                <span class="text-[9px] font-bold text-surface-400 uppercase tracking-wider block">Manager</span>
                                <span class="text-xs font-bold text-surface-700 block truncate" :title="center.manager_name">{{ center.manager_name || 'Not set' }}</span>
                            </div>
                            <div class="bg-surface-50 p-2 rounded border border-surface-200/50">
                                <span class="text-[9px] font-bold text-surface-400 uppercase tracking-wider block">Phone</span>
                                <span class="text-xs font-bold text-surface-700 block truncate" :title="center.manager_phone">{{ center.manager_phone || '—' }}</span>
                            </div>
                            <div class="bg-surface-50 p-2 rounded border border-surface-200/50">
                                <span class="text-[9px] font-bold text-surface-400 uppercase tracking-wider block">Farmers</span>
                                <span class="text-xs font-bold text-surface-700 block">{{ center.farmers_count ?? 0 }}</span>
                            </div>
                            <div class="bg-surface-50 p-2 rounded border border-surface-200/50">
                                <span class="text-[9px] font-bold text-surface-400 uppercase tracking-wider block">Claims</span>
                                <span class="text-xs font-bold text-surface-700 block">{{ center.pending_claims_count ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- Action Links -->
                        <div class="mt-5 pt-3.5 border-t border-surface-100 flex items-center justify-between">
                            <router-link
                                :to="{ name: 'partner-milk-center-detail', params: { id: center.id } }"
                                class="text-xs font-bold text-primary-600 hover:text-primary-700 inline-flex items-center gap-1 transition-colors duration-150"
                            >
                                <Icon icon="mdi:eye-outline" :size="14" />
                                Details
                            </router-link>
                            <router-link
                                :to="{ name: 'partner-farmers', query: { center: center.id } }"
                                class="text-xs font-semibold text-surface-500 hover:text-primary-600 inline-flex items-center gap-1 transition-colors duration-150"
                            >
                                <Icon icon="mdi:account-group-outline" :size="14" />
                                Farmers
                            </router-link>
                            <router-link
                                :to="{ name: 'partner-dashboard', query: { highlight: center.id } }"
                                class="text-xs font-semibold text-surface-500 hover:text-primary-600 inline-flex items-center gap-1 transition-colors duration-150"
                            >
                                <Icon icon="mdi:chart-bar" :size="14" />
                                Insights
                            </router-link>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center py-16 border-2 border-dashed border-surface-200 bg-surface-50 rounded text-center"
                >
                    <p class="text-sm font-bold text-surface-600">No milk collection centers yet.</p>
                    <button
                        class="ynex-btn-primary py-1.5 px-4 text-xs mt-3"
                        @click="openCreateModal"
                    >
                        <Icon icon="mdi:plus" :size="16" /> Register First Center
                    </button>
                </div>
            </div>
        </div>

        <!-- Claim Existing Center -->
        <div class="ynex-card">
            <div class="ynex-card-header flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-surface-800">Claim an Existing Center</h2>
                    <p class="text-[11px] text-surface-400 font-medium mt-0.5">Search the directory and request ownership. Admins will review and approve claims.</p>
                </div>
                <div class="text-xs font-bold text-surface-500 bg-surface-100 px-2 py-0.5 rounded">
                    {{ filteredResults.length ? `${filteredResults.length} results` : 'No results' }}
                </div>
            </div>
            <div class="p-5">
                <form class="flex flex-col gap-4 md:flex-row" @submit.prevent="performSearch">
                    <div class="flex-1">
                        <input
                            v-model.trim="claimSearch"
                            type="text"
                            placeholder="Search by name or registration number..."
                            class="w-full rounded border border-surface-200 px-4 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                        />
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="ynex-btn-primary py-2 px-4 text-xs"
                            :disabled="searching"
                        >
                            <Icon icon="mdi:magnify" :size="16" />
                            {{ searching ? 'Searching…' : 'Search' }}
                        </button>
                        <button
                            type="button"
                            class="ynex-btn-secondary py-2 px-4 text-xs"
                            @click="resetSearch"
                        >
                            Clear
                        </button>
                    </div>
                </form>

                <div v-if="claimError" class="mt-4 p-3 bg-red-50 border border-red-200 text-xs text-red-750 rounded">
                    {{ claimError }}
                </div>
                <div v-if="claimSuccess" class="mt-4 p-3 bg-emerald-50 border border-emerald-200 text-xs text-emerald-705 rounded">
                    {{ claimSuccess }}
                </div>

                <div v-if="filteredResults.length" class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="center in filteredResults"
                        :key="center.id"
                        class="p-4 rounded border border-surface-200 bg-white text-xs flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-surface-800">{{ center.name }}</p>
                                    <p class="text-[10px] text-surface-400 mt-0.5">{{ center.registration_number || 'Unregistered' }}</p>
                                </div>
                            </div>
                            <p class="mt-2 text-surface-500 leading-relaxed">{{ center.physical_address }}</p>
                            <textarea
                                v-model="claimMessages[center.id]"
                                rows="2"
                                class="mt-3 w-full rounded border border-surface-200 px-3 py-2 text-[11px] text-surface-700 focus:border-primary-500 focus:outline-none"
                                placeholder="Add note for admin review..."
                            ></textarea>
                        </div>
                        <button
                            class="mt-3 w-full ynex-btn-primary py-1.5 text-xs"
                            :disabled="claimingCenterId === center.id"
                            @click="submitClaim(center.id)"
                        >
                            {{ claimingCenterId === center.id ? 'Submitting…' : 'Request Claim' }}
                        </button>
                    </div>
                </div>
                <div
                    v-else-if="searched"
                    class="mt-6 py-8 border border-dashed border-surface-200 bg-surface-50 rounded text-center text-xs text-surface-400 font-semibold"
                >
                    No centers matched your search.
                </div>
            </div>
        </div>

        <!-- History & Invites Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Claim History -->
            <div class="ynex-card">
                <div class="ynex-card-header flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-surface-800">Claim History</h2>
                        <p class="text-[11px] text-surface-400 font-medium mt-0.5">{{ resolvedClaims.length }} resolved claims.</p>
                    </div>
                </div>
                <div class="p-5">
                    <div v-if="sortedResolvedClaims.length" class="space-y-4 max-h-[360px] overflow-y-auto pr-1">
                        <article
                            v-for="claim in sortedResolvedClaims"
                            :key="claim.id"
                            class="p-4 rounded border border-surface-200 bg-white text-xs flex flex-col gap-2"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-surface-800">{{ claim.milk_collection_center?.name ?? 'Milk Center' }}</p>
                                    <p class="text-[10px] text-surface-400 mt-0.5">{{ claim.partner?.name ?? partnerName }}</p>
                                </div>
                                <span :class="['rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider', claimStatusBadgeClasses(claim.status)]">
                                    {{ formatClaimStatus(claim.status) }}
                                </span>
                            </div>
                            <p v-if="claim.message" class="p-2 bg-surface-50 rounded text-[11px] text-surface-500 italic">“{{ claim.message }}”</p>
                            <p v-if="claim.response_notes" class="p-2 bg-emerald-50 text-emerald-700 rounded text-[11px] font-semibold">{{ claim.response_notes }}</p>
                        </article>
                    </div>
                    <p v-else class="text-xs text-surface-400 font-semibold py-8 border border-dashed border-surface-200 bg-surface-50 rounded text-center">
                        No resolved claims yet.
                    </p>
                </div>
            </div>

            <!-- Team Invitations -->
            <div class="ynex-card">
                <div class="ynex-card-header flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-surface-800">Team Invitations</h2>
                        <p class="text-[11px] text-surface-400 font-medium mt-0.5">{{ pendingInvitations.length }} pending invitations.</p>
                    </div>
                    <button
                        class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-0.5"
                        @click="openInvitationModal"
                    >
                        <Icon icon="mdi:email-plus-outline" :size="14" /> Invite
                    </button>
                </div>
                <div class="p-5">
                    <div v-if="inviteSuccess" class="mb-4 p-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded text-xs">
                        {{ inviteSuccess }}
                    </div>
                    <div v-if="inviteError" class="mb-4 p-2 bg-red-50 text-red-750 border border-red-100 rounded text-xs">
                        {{ inviteError }}
                    </div>

                    <div v-if="pendingInvitations.length" class="space-y-4 max-h-[360px] overflow-y-auto pr-1">
                        <div
                            v-for="invite in pendingInvitations"
                            :key="invite.id"
                            class="p-4 rounded border border-surface-200 bg-white text-xs flex flex-col gap-2"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-surface-800">{{ invite.email }}</span>
                                <button
                                    class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase tracking-wider"
                                    :disabled="revokingInvitationId === invite.id"
                                    @click="revokeInvitation(invite.id)"
                                >
                                    {{ revokingInvitationId === invite.id ? 'Revoking…' : 'Revoke' }}
                                </button>
                            </div>
                            <p class="text-[10px] text-surface-400 font-bold uppercase tracking-wider mt-0.5">Role: {{ invite.role.replace('_', ' ') }}</p>
                        </div>
                    </div>
                    <p v-else class="text-xs text-surface-400 font-semibold py-8 border border-dashed border-surface-200 bg-surface-50 rounded text-center">
                        No pending invitations.
                    </p>
                </div>
            </div>
        </div>

        <MilkCollectionCenterFormModal :is-open="createModalOpen" @close="closeCreateModal" @created="handleCenterCreated" />
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
import MilkCollectionCenterFormModal from '../../components/milk-centers/MilkCollectionCenterFormModal.vue';
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

    const allCenters = geographyStore.milkCenters ?? [];

    return allCenters
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

<style scoped>
.center-card-container {
    container-type: inline-size;
}
</style>

