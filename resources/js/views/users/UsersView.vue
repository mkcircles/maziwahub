<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="ynex-page-header">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-surface-900 dark:text-white">Users</h1>
                <p class="text-xs text-surface-500 font-medium mt-1">Manage system user accounts, roles, access, and passwords.</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="ynex-btn-secondary py-1.5 px-3.5 text-xs flex items-center gap-1.5"
                    :disabled="loading"
                    @click="fetchUsers"
                >
                    <Icon :icon="loading ? 'mdi:loading' : 'mdi:refresh'" :size="16" :class="loading ? 'animate-spin' : ''" />
                    Refresh
                </button>
                <button
                    class="ynex-btn-primary py-1.5 px-3.5 text-xs flex items-center gap-1.5"
                    @click="openCreateModal"
                >
                    <Icon icon="mdi:account-plus" :size="16" />
                    Add User
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="rounded-md border border-surface-200 bg-white/95 p-6 shadow-sm shadow-slate-100 dark:bg-surface-900 dark:border-surface-800">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="grid gap-4 sm:grid-cols-3 flex-1">
                    <!-- Search input -->
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-2">
                            Search users
                        </label>
                        <div class="flex items-center gap-2 rounded-md border border-surface-200 px-3 py-2 bg-white dark:bg-surface-950 dark:border-surface-800">
                            <Icon icon="mdi:magnify" :size="18" class="text-surface-400" />
                            <input
                                v-model="search"
                                type="search"
                                placeholder="Search name or email"
                                class="w-full bg-transparent text-sm text-surface-800 placeholder:text-surface-400 focus:outline-none dark:text-surface-200"
                            />
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-2">
                            Filter by Role
                        </label>
                        <select
                            v-model="userTypeFilter"
                            class="rounded-md border border-surface-200 px-3 py-2 text-sm text-surface-800 bg-white focus:outline-none dark:bg-surface-950 dark:border-surface-800 dark:text-surface-200"
                        >
                            <option value="">All Roles</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="partner">Partner</option>
                            <option value="mcc">MCC Manager</option>
                            <option value="agent">Agent</option>
                            <option value="user">Standard User</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="flex flex-col">
                        <label class="text-xs font-semibold uppercase tracking-wide text-surface-500 mb-2">
                            Filter by Status
                        </label>
                        <select
                            v-model="statusFilter"
                            class="rounded-md border border-surface-200 px-3 py-2 text-sm text-surface-800 bg-white focus:outline-none dark:bg-surface-950 dark:border-surface-800 dark:text-surface-200"
                        >
                            <option value="">All Statuses</option>
                            <option value="true">Active Only</option>
                            <option value="false">Inactive Only</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        @click="resetAllFilters"
                        class="inline-flex items-center gap-2 rounded-md border border-surface-200 px-4 py-2 text-sm font-semibold text-surface-600 transition hover:bg-surface-50 dark:border-surface-800 dark:hover:bg-surface-800 dark:text-surface-300"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Feedback Alert Messages -->
        <div v-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700 dark:bg-red-950/20 dark:border-red-900/50 dark:text-red-300">
            {{ error }}
        </div>
        <div v-if="updateError" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700 dark:bg-red-950/20 dark:border-red-900/50 dark:text-red-300">
            {{ updateError }}
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto rounded-lg bg-white shadow dark:bg-surface-900">
            <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-800">
                <thead class="bg-surface-50 dark:bg-surface-950">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-surface-500">
                        <th class="px-6 py-3">User Details</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Assigned Scope</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 text-xs text-surface-700 dark:divide-surface-800 dark:text-surface-300">
                    <tr v-if="loading && users.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-surface-500">
                            <div class="flex items-center justify-center gap-2">
                                <Icon icon="mdi:loading" :size="20" class="animate-spin text-primary-600" />
                                <span>Loading users...</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-else-if="users.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-surface-500">
                            No users found.
                        </td>
                    </tr>
                    <tr v-else v-for="user in users" :key="user.id" class="hover:bg-surface-50 dark:hover:bg-surface-800/40">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-surface-900 dark:text-white">{{ user.name }}</div>
                            <div class="text-surface-500">{{ user.email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider" :class="getRoleBadgeClass(user.user_type)">
                                {{ formatRole(user.user_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div v-if="user.partner" class="flex items-center gap-1.5 text-surface-600 dark:text-surface-400">
                                <Icon icon="mdi:domain" :size="14" class="text-primary-500" />
                                <span>{{ user.partner.name }}</span>
                            </div>
                            <div v-else-if="user.milk_collection_center" class="flex items-center gap-1.5 text-surface-600 dark:text-surface-400">
                                <Icon icon="mdi:store-outline" :size="14" class="text-indigo-500" />
                                <span>{{ user.milk_collection_center.name }}</span>
                            </div>
                            <span v-else class="text-surface-400">System Wide</span>
                        </td>
                        <td class="px-6 py-4">
                            <button
                                @click="toggleActive(user)"
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition duration-200"
                                :class="user.is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200/80 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-surface-200 text-surface-600 hover:bg-surface-300/80 dark:bg-surface-800 dark:text-surface-400'"
                                :title="user.is_active ? 'Click to Deactivate' : 'Click to Activate'"
                            >
                                <Icon :icon="user.is_active ? 'mdi:check-circle-outline' : 'mdi:pause-circle-outline'" :size="14" />
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold border border-surface-200 rounded hover:bg-surface-50 transition dark:border-surface-800 dark:hover:bg-surface-800"
                                    @click="openEditModal(user)"
                                    title="Edit user details and settings"
                                >
                                    <Icon icon="mdi:pencil-outline" :size="14" />
                                    <span>Edit</span>
                                </button>
                                <router-link
                                    :to="{ name: 'admin-user-detail', params: { id: user.id } }"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold border border-surface-200 rounded hover:bg-surface-50 transition dark:border-surface-800 dark:hover:bg-surface-800"
                                    title="View activity details"
                                >
                                    <Icon icon="mdi:eye-outline" :size="14" />
                                    <span>View</span>
                                </router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div v-if="pagination.last_page > 1" class="flex items-center justify-between border-t border-surface-200 pt-4 text-sm text-surface-600 dark:border-surface-800 dark:text-surface-400">
            <div>
                Showing page {{ pagination.current_page }} of {{ pagination.last_page }} • {{ pagination.total }} total users
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="rounded-lg border border-surface-200 px-3 py-1 hover:bg-surface-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-surface-800 dark:hover:bg-surface-800"
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                >
                    Previous
                </button>
                <button
                    class="rounded-lg border border-surface-200 px-3 py-1 hover:bg-surface-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-surface-800 dark:hover:bg-surface-800"
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                >
                    Next
                </button>
            </div>
        </div>

        <!-- Create & Edit Modal -->
        <CreateUserModal
            :is-open="showCreateModal"
            :user-to-edit="userToEdit"
            @close="closeCreateModal"
            @created="handleUserCreated"
        />
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch, computed } from 'vue';
import { storeToRefs } from 'pinia';
import Icon from '../../components/shared/Icon.vue';
import { useUserStore, type User } from '../../stores/userStore';
import CreateUserModal from '../../components/users/CreateUserModal.vue';

const userStore = useUserStore();
const { users, loading, error, updateError, pagination, filters } = storeToRefs(userStore);

const showCreateModal = ref(false);
const userToEdit = ref<User | null>(null);

const search = ref('');
const userTypeFilter = ref('');
const statusFilter = ref('');

const fetchUsers = async () => {
    await userStore.fetchUsers(pagination.value.current_page);
};

const changePage = async (page: number) => {
    await userStore.fetchUsers(page);
};

const resetAllFilters = async () => {
    search.value = '';
    userTypeFilter.value = '';
    statusFilter.value = '';
    userStore.resetFilters();
};

const toggleActive = async (user: User) => {
    try {
        await userStore.toggleUserActive(user);
    } catch {
        // errors handled in store and display in UI
    }
};

const openCreateModal = () => {
    userToEdit.value = null;
    showCreateModal.value = true;
};

const openEditModal = (user: User) => {
    userToEdit.value = user;
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    userToEdit.value = null;
};

const handleUserCreated = async () => {
    await fetchUsers();
};

// Map filters to pinia store
watch(search, (newVal) => {
    userStore.setFilter('search', newVal);
    userStore.fetchUsers(1);
});

watch(userTypeFilter, (newVal) => {
    userStore.setFilter('user_type', newVal);
    userStore.fetchUsers(1);
});

watch(statusFilter, (newVal) => {
    userStore.setFilter('is_active', newVal);
    userStore.fetchUsers(1);
});

const getRoleBadgeClass = (role: string) => {
    switch (role) {
        case 'super_admin':
            return 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-950/30 dark:text-red-400 dark:border-red-900/40';
        case 'admin':
            return 'bg-purple-100 text-purple-800 border border-purple-200 dark:bg-purple-950/30 dark:text-purple-400 dark:border-purple-900/40';
        case 'partner':
            return 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-900/40';
        case 'mcc':
            return 'bg-indigo-100 text-indigo-800 border border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-400 dark:border-indigo-900/40';
        case 'agent':
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/40';
        default:
            return 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700/50';
    }
};

const formatRole = (role: string) => {
    switch (role) {
        case 'super_admin':
            return 'Super Admin';
        case 'admin':
            return 'Admin';
        case 'partner':
            return 'Partner';
        case 'mcc':
            return 'MCC Manager';
        case 'agent':
            return 'Agent';
        default:
            return 'Standard';
    }
};

onMounted(() => {
    fetchUsers();
});
</script>
