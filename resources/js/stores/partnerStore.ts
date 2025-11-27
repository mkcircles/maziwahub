import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import axios from 'axios';

export interface Partner {
    id: number;
    name: string;
    email: string;
    phone?: string | null;
    address?: string | null;
    registration_number?: string | null;
    description?: string | null;
    contact_name?: string | null;
    contact_title?: string | null;
    website?: string | null;
    country?: string | null;
    city?: string | null;
    postal_code?: string | null;
    is_active: boolean;
    milk_collection_centers_count?: number;
    pending_claims_count?: number;
    pending_invitations_count?: number;
    created_at?: string;
    updated_at?: string;
    milk_collection_centers?: Array<any>;
    invitations?: PartnerInvitation[];
    users?: Array<any>;
}

export interface PartnerInvitation {
    id: number;
    partner_id: number;
    invited_by_user_id?: number | null;
    email: string;
    name?: string | null;
    role: 'partner_admin' | 'partner_agent';
    token: string;
    status: 'pending' | 'accepted' | 'revoked' | 'expired';
    expires_at?: string | null;
    accepted_at?: string | null;
    revoked_at?: string | null;
    notes?: string | null;
    created_at?: string;
    updated_at?: string;
}

export interface MilkCollectionCenterClaim {
    id: number;
    milk_collection_center_id: number;
    partner_id: number;
    requested_by_user_id?: number | null;
    status: 'pending' | 'approved' | 'rejected';
    message?: string | null;
    responded_by_user_id?: number | null;
    responded_at?: string | null;
    response_notes?: string | null;
    created_at?: string;
    updated_at?: string;
    milk_collection_center?: any;
    partner?: Partner;
}

export const usePartnerStore = defineStore('partnerStore', () => {
    const partners = ref<Partner[]>([]);
    const partnersLoading = ref(false);
    const partnersError = ref<string | null>(null);

    const activePartner = ref<Partner | null>(null);
    const partnerLoading = ref(false);
    const partnerError = ref<string | null>(null);

    const invitations = ref<PartnerInvitation[]>([]);
    const invitationsLoading = ref(false);
    const invitationsError = ref<string | null>(null);

    const claims = ref<MilkCollectionCenterClaim[]>([]);
    const claimsLoading = ref(false);
    const claimsError = ref<string | null>(null);

    const hasActivePartner = computed(() => Boolean(activePartner.value));

    async function fetchPartners(params: { search?: string } = {}) {
        partnersLoading.value = true;
        partnersError.value = null;

        try {
            const response = await axios.get<Partner[]>('partners', {
                params: {
                    search: params.search || undefined,
                },
            });
            partners.value = response.data ?? [];
        } catch (error: any) {
            partnersError.value = error.response?.data?.message || 'Failed to load partners.';
            partners.value = [];
            throw error;
        } finally {
            partnersLoading.value = false;
        }
    }

    async function createPartner(payload: Record<string, any>) {
        const response = await axios.post<Partner>('partners', payload);
        partners.value = [response.data, ...partners.value];
        return response.data;
    }

    async function updatePartner(partnerId: number, payload: Record<string, any>) {
        const response = await axios.put<Partner>(`partners/${partnerId}`, payload);

        if (activePartner.value?.id === partnerId) {
            activePartner.value = response.data;
        }

        partners.value = partners.value.map((partner) =>
            partner.id === partnerId ? response.data : partner,
        );

        return response.data;
    }

    async function fetchPartner(partnerId: number) {
        partnerLoading.value = true;
        partnerError.value = null;

        try {
            const response = await axios.get<Partner>(`partners/${partnerId}`);
            activePartner.value = response.data;
            invitations.value = response.data.invitations ?? [];
            return response.data;
        } catch (error: any) {
            partnerError.value = error.response?.data?.message || 'Failed to load partner.';
            activePartner.value = null;
            invitations.value = [];
            throw error;
        } finally {
            partnerLoading.value = false;
        }
    }

    function setActivePartner(partner: Partner | null) {
        activePartner.value = partner;
    }

    async function refreshActivePartner() {
        if (!activePartner.value?.id) {
            return null;
        }

        return fetchPartner(activePartner.value.id);
    }

    async function togglePartnerStatus(partnerId: number, isActive: boolean) {
        return updatePartner(partnerId, { is_active: isActive });
    }

    async function createInvitation(
        partnerId: number,
        payload: {
            email: string;
            name?: string;
            role?: 'partner_admin' | 'partner_agent';
            expires_at?: string | null;
            notes?: string | null;
        },
    ) {
        invitationsLoading.value = true;
        invitationsError.value = null;

        try {
            const response = await axios.post<PartnerInvitation>(`partners/${partnerId}/invitations`, {
                role: payload.role ?? 'partner_admin',
                ...payload,
            });
            invitations.value = [response.data, ...invitations.value];
            return response.data;
        } catch (error: any) {
            invitationsError.value = error.response?.data?.message || 'Failed to invite partner member.';
            throw error;
        } finally {
            invitationsLoading.value = false;
        }
    }

    async function revokeInvitation(partnerId: number, invitationId: number) {
        try {
            const response = await axios.delete<PartnerInvitation>(
                `partners/${partnerId}/invitations/${invitationId}`,
            );

            invitations.value = invitations.value.map((invitation) =>
                invitation.id === invitationId ? response.data : invitation,
            );

            return response.data;
        } catch (error: any) {
            invitationsError.value = error.response?.data?.message || 'Failed to revoke invitation.';
            throw error;
        }
    }

    async function fetchClaims(params: { status?: string } = {}) {
        claimsLoading.value = true;
        claimsError.value = null;

        try {
            const response = await axios.get<MilkCollectionCenterClaim[]>('milk-collection-center-claims', {
                params,
            });
            claims.value = response.data ?? [];
        } catch (error: any) {
            claimsError.value = error.response?.data?.message || 'Failed to load claims.';
            claims.value = [];
            throw error;
        } finally {
            claimsLoading.value = false;
        }
    }

    async function requestClaim(centerId: number, payload: { message?: string | null } = {}) {
        const response = await axios.post<MilkCollectionCenterClaim>(
            `milk-collection-centers/${centerId}/claims`,
            payload,
        );

        claims.value = [response.data, ...claims.value];
        return response.data;
    }

    async function approveClaim(claimId: number, payload: { response_notes?: string | null } = {}) {
        const response = await axios.post<MilkCollectionCenterClaim>(
            `milk-collection-center-claims/${claimId}/approve`,
            payload,
        );

        claims.value = claims.value.map((claim) =>
            claim.id === claimId ? response.data : claim,
        );

        return response.data;
    }

    async function rejectClaim(claimId: number, payload: { response_notes?: string | null } = {}) {
        const response = await axios.post<MilkCollectionCenterClaim>(
            `milk-collection-center-claims/${claimId}/reject`,
            payload,
        );

        claims.value = claims.value.map((claim) =>
            claim.id === claimId ? response.data : claim,
        );

        return response.data;
    }

    return {
        partners,
        partnersLoading,
        partnersError,
        activePartner,
        partnerLoading,
        partnerError,
        invitations,
        invitationsLoading,
        invitationsError,
        claims,
        claimsLoading,
        claimsError,
        hasActivePartner,
        fetchPartners,
        createPartner,
        updatePartner,
        fetchPartner,
        setActivePartner,
        refreshActivePartner,
        createInvitation,
        revokeInvitation,
        fetchClaims,
        requestClaim,
        approveClaim,
        rejectClaim,
        togglePartnerStatus,
    };
});


