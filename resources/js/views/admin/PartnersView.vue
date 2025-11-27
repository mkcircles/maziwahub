<template>
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-8 py-10 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.3),transparent_60%)] opacity-80"></div>
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.4em] text-white/70">Administration · Partners</p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Partner Management</h1>
                    <p class="text-sm text-white/70">
                        Oversee partner organisations, track their collection footprint, and manage platform access.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:account-group-outline" :size="16" />
                            {{ totalPartners }} partners onboarded
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:check-circle-outline" :size="16" />
                            {{ activePartners }} active
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1">
                            <Icon icon="mdi:clock-outline" :size="16" />
                            {{ totalPendingClaims }} pending claims
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/20"
                        :disabled="partnersLoading"
                        @click="refreshPartners"
                    >
                        <Icon :icon="partnersLoading ? 'mdi:loading' : 'mdi:refresh'" :size="18" :class="partnersLoading ? 'animate-spin' : ''" />
                        Refresh list
                    </button>
                    <button
                        class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                        @click="openCreateModal"
                    >
                        <Icon icon="mdi:plus" :size="18" />
                        Add partner
                    </button>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatisticalCard icon="mdi:account-group-outline" icon-class="text-sky-500">
                <template #title>Total Partners</template>
                <template #default>{{ totalPartners }}</template>
                <template #caption>Organisations on the platform</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:checkbox-marked-circle-outline" icon-class="text-emerald-500">
                <template #title>Active</template>
                <template #default>{{ activePartners }}</template>
                <template #caption>Currently able to access the portal</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:alert-circle-outline" icon-class="text-amber-500">
                <template #title>Pending Claims</template>
                <template #default>{{ totalPendingClaims }}</template>
                <template #caption>Awaiting approval</template>
            </StatisticalCard>
            <StatisticalCard icon="mdi:email-outline" icon-class="text-indigo-500">
                <template #title>Open Invitations</template>
                <template #default>{{ totalPendingInvites }}</template>
                <template #caption>Invitations yet to be accepted</template>
            </StatisticalCard>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white/95 p-8 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Search partners
                        </label>
                        <div class="mt-2 flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2">
                            <Icon icon="mdi:magnify" :size="18" class="text-slate-400" />
                            <input
                                v-model="search"
                                type="search"
                                placeholder="Search by name, email, or registration"
                                class="w-full bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none"
                            />
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status
                        </label>
                        <div class="mt-2 inline-flex items-center gap-2 rounded-full bg-slate-100/60 p-1 text-xs font-semibold text-slate-600">
                            <button
                                v-for="option in statusOptions"
                                :key="option.value"
                                type="button"
                                class="rounded-full px-3 py-1 transition"
                                :class="statusFilter === option.value ? 'bg-slate-900 text-white shadow-sm shadow-slate-900/30' : 'text-slate-600 hover:bg-white'"
                                @click="statusFilter = option.value"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <p class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-500">
                        <Icon icon="mdi:view-list-outline" :size="16" />
                        {{ filteredPartners.length }} partners
                    </p>
                    <button
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                        @click="openCreateModal"
                    >
                        <Icon icon="mdi:plus" :size="16" />
                        New partner
                    </button>
                </div>
            </div>

            <div v-if="partnersError" class="mt-4 rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-3 text-sm text-rose-700">
                {{ partnersError }}
            </div>
            <div v-if="actionError" class="mt-4 rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-3 text-sm text-rose-700">
                {{ actionError }}
            </div>
            <div v-if="actionSuccess" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700">
                {{ actionSuccess }}
            </div>

            <div v-if="partnersLoading" class="mt-6 flex h-56 items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 text-sm text-slate-500">
                Loading partners…
            </div>
            <div
                v-else-if="!filteredPartners.length"
                class="mt-6 flex h-56 flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 text-sm text-slate-500"
            >
                <Icon icon="mdi:inbox-outline" :size="30" class="text-slate-400" />
                No partners match the current filters.
            </div>
            <div v-else class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr class="text-left">
                            <th class="px-6 py-3 font-semibold">Partner</th>
                            <th class="px-6 py-3 font-semibold">Contacts</th>
                            <th class="px-6 py-3 font-semibold">Metrics</th>
                            <th class="px-6 py-3 font-semibold text-right">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr
                            v-for="partner in filteredPartners"
                            :key="partner.id"
                            class="transition hover:bg-slate-50/70"
                        >
                            <td class="px-6 py-4 align-top">
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ partner.name }}
                                </div>
                                <div class="mt-1 flex items-center gap-2 text-xs uppercase tracking-wide text-slate-400">
                                    <Icon icon="mdi:identifier" :size="14" />
                                    <span>{{ partner.registration_number || 'Not registered' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-slate-600">
                                <div class="flex items-center gap-2">
                                    <Icon icon="mdi:email-outline" :size="16" class="text-slate-400" />
                                    <span>{{ partner.email }}</span>
                                </div>
                                <div v-if="partner.phone" class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                    <Icon icon="mdi:phone-outline" :size="14" />
                                    <span>{{ partner.phone }}</span>
                                </div>
                                <div v-if="partner.contact_name" class="mt-2 text-xs text-slate-500">
                                    Contact: <span class="font-medium text-slate-600">{{ partner.contact_name }}</span>
                                    <span v-if="partner.contact_title"> — {{ partner.contact_title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-xs text-slate-500">
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-700">
                                        <Icon icon="mdi:storefront-outline" :size="14" />
                                        {{ partner.milk_collection_centers_count ?? 0 }} centers
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-700">
                                        <Icon icon="mdi:alert-circle-outline" :size="14" />
                                        {{ partner.pending_claims_count ?? 0 }} claims
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-700">
                                        <Icon icon="mdi:email-open-outline" :size="14" />
                                        {{ partner.pending_invitations_count ?? 0 }} invites
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold"
                                    :class="partner.is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-600' : 'border-slate-200 bg-slate-50 text-slate-500'"
                                >
                                    <span class="h-2 w-2 rounded-full" :class="partner.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    {{ partner.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-wrap justify-end gap-2 text-xs">
                                    <button
                                        class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="togglingStatus[partner.id]"
                                        @click="toggleStatus(partner)"
                                    >
                                        <Icon :icon="partner.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'" :size="16" />
                                        {{ partner.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                                        @click="openEditModal(partner)"
                                    >
                                        <Icon icon="mdi:pencil-outline" :size="16" />
                                        Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 rounded-full bg-slate-900 px-3 py-1 font-medium text-white transition hover:bg-slate-700"
                                        @click="goToDetail(partner.id)"
                                    >
                                        <Icon icon="mdi:open-in-new" :size="16" />
                                        Manage
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <PartnerFormModal
            :is-open="showFormModal"
            :partner="editingPartner"
            @close="closeModal"
            @saved="handleSaved"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import PartnerFormModal from '../../components/partners/PartnerFormModal.vue';
import { usePartnerStore, type Partner } from '../../stores/partnerStore';

const partnerStore = usePartnerStore();
const router = useRouter();

const search = ref('');
const statusFilter = ref<'all' | 'active' | 'inactive'>('all');
const showFormModal = ref(false);
const editingPartner = ref<Partner | null>(null);
const actionError = ref<string | null>(null);
const actionSuccess = ref<string | null>(null);
const togglingStatus = ref<Record<number, boolean>>({});

const partnersLoading = computed(() => partnerStore.partnersLoading);
const partnersError = computed(() => partnerStore.partnersError);
const partners = computed(() => partnerStore.partners);

const totalPartners = computed(() => partners.value.length);
const activePartners = computed(() => partners.value.filter(partner => partner.is_active).length);
const totalPendingClaims = computed(() =>
    partners.value.reduce((sum, partner) => sum + (partner.pending_claims_count ?? 0), 0),
);
const totalPendingInvites = computed(() =>
    partners.value.reduce((sum, partner) => sum + (partner.pending_invitations_count ?? 0), 0),
);

const statusOptions: Array<{ label: string; value: 'all' | 'active' | 'inactive' }> = [
    { label: 'All', value: 'all' },
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' },
];

const filteredPartners = computed(() => {
    let list = partners.value;

    if (statusFilter.value !== 'all') {
        const isActive = statusFilter.value === 'active';
        list = list.filter(partner => partner.is_active === isActive);
    }

    return list;
});

const refreshPartners = async () => {
    try {
        await partnerStore.fetchPartners({
            search: search.value || undefined,
        });
    } catch (error) {
        // errors handled in store
    }
};

const openCreateModal = () => {
    editingPartner.value = null;
    showFormModal.value = true;
};

const openEditModal = (partner: Partner) => {
    editingPartner.value = partner;
    showFormModal.value = true;
};

const closeModal = () => {
    showFormModal.value = false;
};

const handleSaved = async () => {
    showFormModal.value = false;
    actionSuccess.value = editingPartner.value ? 'Partner details updated successfully.' : 'Partner created successfully.';
    actionError.value = null;
    await refreshPartners();
};

const goToDetail = (partnerId: number) => {
    router.push({ name: 'admin-partners-detail', params: { id: partnerId } });
};

const toggleStatus = async (partner: Partner) => {
    togglingStatus.value = { ...togglingStatus.value, [partner.id]: true };
    actionError.value = null;
    actionSuccess.value = null;

    try {
        await partnerStore.togglePartnerStatus(partner.id, !partner.is_active);
        actionSuccess.value = `Partner ${partner.is_active ? 'deactivated' : 'activated'} successfully.`;
    } catch (error: any) {
        actionError.value =
            error?.response?.data?.message || partnerStore.partnerError || 'Failed to update partner status.';
    } finally {
        togglingStatus.value = { ...togglingStatus.value, [partner.id]: false };
    }
};

onMounted(async () => {
    if (!partners.value.length) {
        await refreshPartners();
    }
});

let searchTimeout: ReturnType<typeof setTimeout> | undefined;

watch(
    () => search.value,
    value => {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = setTimeout(() => {
            refreshPartners();
        }, 300);
    },
);
</script>

