<template>
    <div class="space-y-6">
        <div class="ynex-page-header">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-surface-900 dark:text-white">Veterinary Network</h1>
                <p class="text-xs text-surface-500 font-medium mt-1">Manage registered vets and track their engagement across milk collection centers.</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="ynex-btn-secondary py-1.5 px-3.5 text-xs flex items-center gap-1.5"
                    :disabled="loading"
                    @click="refresh"
                >
                    <Icon icon="mdi:refresh" :size="16" />
                    Refresh
                </button>
                <button
                    class="ynex-btn-primary py-1.5 px-3.5 text-xs flex items-center gap-1.5 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="openCreateModal"
                >
                    <Icon icon="mdi:account-plus" :size="16" />
                    Onboard Vet
                </button>
            </div>
        </div>

        <div v-if="loading" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">Loading vets...</div>
        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
        <div v-else>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatisticalCard icon="mdi:stethoscope" icon-class="text-primary-600">
                    <template #title>Total Vets</template>
                    <template #default>{{ pagination.total }}</template>
                    <template #caption>Registered across the platform</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:check-circle" icon-class="text-emerald-500">
                    <template #title>Active</template>
                    <template #default>{{ activeCount }}</template>
                    <template #caption>Available for assignments</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:calendar-alert" icon-class="text-amber-500">
                    <template #title>Expiring Soon</template>
                    <template #default>{{ expiringSoonCount }}</template>
                    <template #caption>Licenses expiring in 30 days</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:medical-bag" icon-class="text-purple-500">
                    <template #title>Assigned to MCC</template>
                    <template #default>{{ assignedCount }}</template>
                    <template #caption>Linked to a collection center</template>
                </StatisticalCard>
            </div>

            <section class="space-y-4 mt-4 rounded-lg bg-white p-6 shadow">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex flex-1 flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Search</label>
                            <div class="mt-1 flex rounded-lg border border-surface-200">
                                <input
                                    v-model="searchTerm"
                                    type="search"
                                    placeholder="Search by name or license number"
                                    class="flex-1 rounded-l-lg border-none text-surface-700 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-200"
                                    @keyup.enter="applyFilters"
                                />
                                <button
                                    class="rounded-r-lg border-l border-surface-200 bg-surface-50 px-3 text-sm font-medium text-surface-600 hover:bg-surface-100"
                                    @click="applyFilters"
                                >
                                    Search
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Status</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input id="active-only" type="checkbox" v-model="activeOnly" class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500" />
                                <label for="active-only" class="text-sm text-surface-700">Active vets only</label>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="inline-flex items-center gap-2 rounded-lg border border-surface-200 px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50"
                            @click="resetFilters"
                        >
                            <Icon icon="mdi:filter-off" :size="16" />
                            Reset filters
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200">
                        <thead class="bg-surface-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-surface-500">
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">License</th>
                                <th class="px-6 py-3">Contacts</th>
                                <!-- <th class="px-6 py-3">Specialization</th>
                                <th class="px-6 py-3">Assigned MCC</th> -->
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-200 text-sm text-surface-700">
                            <tr v-if="vets.length === 0">
                                <td colspan="7" class="px-6 py-6 text-center text-sm text-surface-500">No vets found for the current filters.</td>
                            </tr>
                            <tr v-for="vet in vets" :key="vet.id" class="hover:bg-surface-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-surface-900">{{ vet.first_name }} {{ vet.last_name }}</div>
                                    <div class="text-xs text-surface-500">Joined {{ formatDate(vet.created_at) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-surface-900">{{ vet.license_number }}</div>
                                    <div class="text-xs text-surface-500">Expiry: {{ formatDate(vet.license_expiry_date) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>{{ vet.phone_number ?? '—' }}</div>
                                    <div class="text-xs text-surface-500">{{ vet.email ?? '—' }}</div>
                                </td>
                                <!-- <td class="px-6 py-4">{{ vet.specialization ?? '—' }}</td>
                                <td class="px-6 py-4">{{ vet.milkCollectionCenter?.name ?? 'Unassigned' }}</td> -->
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                                        :class="vet.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-surface-200 text-surface-600'"
                                    >
                                        <Icon :icon="vet.is_active ? 'mdi:check-circle-outline' : 'mdi:pause-circle-outline'" :size="14" />
                                        {{ vet.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <router-link
                                            :to="`/admin/vets/${vet.id}`"
                                            class="inline-flex items-center gap-1 rounded-lg border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-700 hover:bg-surface-50"
                                        >
                                            <Icon icon="mdi:eye-outline" :size="14" />
                                            View
                                        </router-link>
                                        <button
                                            class="inline-flex items-center gap-1 rounded-lg border border-surface-200 px-3 py-1 text-xs font-semibold text-surface-700 hover:bg-surface-50"
                                            @click="toggleActive(vet)"
                                            :disabled="updating && vet.id === updatingVetId"
                                        >
                                            <Icon :icon="vet.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'" :size="14" />
                                            {{ vet.is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="pagination.last_page > 1" class="flex items-center justify-between border-t border-surface-100 pt-4 text-sm text-surface-600">
                    <div>
                        Showing page {{ pagination.current_page }} of {{ pagination.last_page }} • {{ pagination.total }} total
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-surface-200 px-3 py-1 hover:bg-surface-50 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="changePage(pagination.current_page - 1)"
                            :disabled="pagination.current_page <= 1"
                        >
                            Previous
                        </button>
                        <button
                            class="rounded-lg border border-surface-200 px-3 py-1 hover:bg-surface-50 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="changePage(pagination.current_page + 1)"
                            :disabled="pagination.current_page >= pagination.last_page"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </section>
        </div>

        <CreateVetModal :is-open="showCreateModal" @close="closeCreateModal" @created="handleVetCreated" />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import { useVetStore } from '../../stores/vetStore';
import type { Vet } from '../../stores/geographyStore';
import CreateVetModal from '../../components/vets/CreateVetModal.vue';

const vetStore = useVetStore();
const { vets, loading, error } = storeToRefs(vetStore);
const pagination = vetStore.pagination;

const searchTerm = ref(vetStore.filters.search);
const activeOnly = ref(vetStore.filters.active_only);
const showCreateModal = ref(false);
const updatingVetId = ref<number | null>(null);

const applyFilters = async () => {
    vetStore.setFilter('search', searchTerm.value.trim());
    vetStore.setFilter('active_only', activeOnly.value);
    await vetStore.fetchVets();
};

const resetFilters = async () => {
    searchTerm.value = '';
    activeOnly.value = false;
    await vetStore.resetFilters();
};

const changePage = async (page: number) => {
    if (page < 1 || page > pagination.last_page) return;
    await vetStore.fetchVets(page);
};

const refresh = async () => {
    await vetStore.fetchVets(pagination.current_page);
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const handleVetCreated = async () => {
    showCreateModal.value = false;
    await vetStore.fetchVets();
};

const toggleActive = async (vet: Vet) => {
    try {
        updatingVetId.value = vet.id;
        await vetStore.toggleVetActive(vet);
        await vetStore.fetchVets(pagination.current_page);
    } finally {
        updatingVetId.value = null;
    }
};

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '—';
    return date.toLocaleDateString();
};

const activeCount = computed(() => vets.value.filter(vet => vet.is_active).length);
const assignedCount = computed(() => vets.value.filter(vet => Boolean(vet.milk_collection_center_id)).length);
const expiringSoonCount = computed(() => {
    const now = new Date();
    const limit = new Date();
    limit.setDate(now.getDate() + 30);
    return vets.value.filter(vet => {
        if (!vet.license_expiry_date) return false;
        const expiry = new Date(vet.license_expiry_date);
        return !Number.isNaN(expiry.getTime()) && expiry >= now && expiry <= limit;
    }).length;
});

watch(activeOnly, async () => {
    await applyFilters();
});

onMounted(async () => {
    if (!vets.value.length) {
        await vetStore.fetchVets();
    }
});
</script>
