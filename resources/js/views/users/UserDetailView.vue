<template>
    <div class="space-y-6">
        <div v-if="loading" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">
            <div class="flex items-center justify-center gap-2">
                <Icon icon="mdi:loading" :size="20" class="animate-spin text-primary-600" />
                <span>Loading user details...</span>
            </div>
        </div>
        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
        <div v-else-if="!user" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">User not found.</div>

        <template v-else>
            <!-- Banner Section -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 px-6 py-8 sm:px-8 sm:py-10 text-white shadow-lg mb-6">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(76,201,240,0.15),transparent_60%)] opacity-90"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex flex-col gap-4 flex-1">
                        <router-link to="/admin/users"
                            class="inline-flex items-center gap-1.5 self-start rounded-full bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-1.5 text-xs font-semibold text-white transition duration-200">
                            <Icon icon="mdi:arrow-left" :size="14" />
                            Back to Users
                        </router-link>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-white/60">
                                User Profile
                            </p>
                            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl mt-1.5">
                                {{ user.name }}
                            </h1>
                            <p class="mt-2 text-xs text-white/70 flex flex-wrap gap-x-4 gap-y-1 items-center font-medium">
                                <span class="flex items-center gap-1">
                                    <Icon icon="mdi:email-outline" :size="14" class="text-white/60" />
                                    {{ user.email }}
                                </span>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[10px] sm:text-xs">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-semibold"
                                :class="user.is_active ? 'bg-emerald-500/20 text-emerald-100 border border-emerald-500/30' : 'bg-white/10 text-white/80 border border-white/10'">
                                <Icon :icon="user.is_active ? 'mdi:check-circle-outline' : 'mdi:close-circle-outline'" :size="14" />
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                <Icon icon="mdi:shield-account-outline" :size="14" />
                                Role: {{ formatRole(user.user_type) }}
                            </span>
                            <span v-if="user.milk_collection_center" class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                <Icon icon="mdi:store-outline" :size="14" />
                                MCC: {{ user.milk_collection_center.name }}
                            </span>
                            <span v-if="user.partner" class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                <Icon icon="mdi:domain" :size="14" />
                                Partner: {{ user.partner.name }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 sm:items-end">
                        <div class="flex items-center gap-2">
                            <button
                                class="inline-flex items-center gap-1.5 rounded bg-white/10 hover:bg-white/20 border border-white/10 px-3.5 py-1.5 text-xs font-bold text-white transition duration-200"
                                @click="refresh" :disabled="loading">
                                <Icon icon="mdi:refresh" :size="14" />
                                Refresh
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 rounded px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition duration-200"
                                :class="user.is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                                @click="toggleActive" :disabled="loading">
                                <Icon :icon="user.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'" :size="14" />
                                {{ user.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 rounded bg-primary-600 hover:bg-primary-700 px-3.5 py-1.5 text-xs font-bold text-white transition duration-200"
                                @click="showEditModal = true">
                                <Icon icon="mdi:pencil" :size="14" />
                                Edit Account
                            </button>
                        </div>
                        <div class="text-[10px] font-semibold text-white/50">
                            Registered {{ formatDate(user.created_at) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Content Area -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Account Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow border border-surface-200">
                        <h2 class="text-base font-bold text-surface-900 mb-4">Account Details</h2>
                        <dl class="grid gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-surface-500 font-semibold">Full Name</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900">{{ user.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-surface-500 font-semibold">Email Address</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900">{{ user.email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-surface-500 font-semibold">User Role / Type</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900">{{ formatRole(user.user_type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wider text-surface-500 font-semibold">System Access</dt>
                                <dd class="mt-1 text-sm font-medium">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="user.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                        {{ user.is_active ? 'Allowed (Active)' : 'Blocked (Inactive)' }}
                                    </span>
                                </dd>
                            </div>
                            <div v-if="user.milk_collection_center" class="sm:col-span-2">
                                <dt class="text-xs uppercase tracking-wider text-surface-500 font-semibold">Assigned MCC</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900">{{ user.milk_collection_center.name }} (Reg # {{ user.milk_collection_center.registration_number || 'N/A' }})</dd>
                            </div>
                            <div v-if="user.partner" class="sm:col-span-2">
                                <dt class="text-xs uppercase tracking-wider text-surface-500 font-semibold">Assigned Partner</dt>
                                <dd class="mt-1 text-sm font-medium text-surface-900">{{ user.partner.name }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Password Settings / Quick Actions -->
                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow border border-surface-200">
                        <h3 class="text-base font-bold text-surface-900 mb-4">Reset Password</h3>
                        <form @submit.prevent="handleResetPassword" class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">
                                    New Password
                                </label>
                                <input v-model="newPassword" type="password" required minlength="8"
                                    placeholder="Minimum 8 characters"
                                    class="mt-1 w-full rounded-md border border-surface-200 px-3 py-2 text-sm text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>
                            <button type="submit" :disabled="resettingPassword || !newPassword"
                                class="w-full inline-flex justify-center items-center gap-1.5 rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <Icon icon="mdi:lock-reset" :size="16" />
                                <span>{{ resettingPassword ? 'Resetting...' : 'Change Password' }}</span>
                            </button>
                        </form>
                        <div v-if="pwdMessage" class="mt-3 text-xs font-medium" :class="pwdIsError ? 'text-rose-600' : 'text-emerald-600'">
                            {{ pwdMessage }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <CreateUserModal
                :is-open="showEditModal"
                :user-to-edit="user"
                @close="showEditModal = false"
                @created="handleUserUpdated"
            />
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import { useUserStore, type User } from '../../stores/userStore';
import CreateUserModal from '../../components/users/CreateUserModal.vue';

const route = useRoute();
const userStore = useUserStore();

const user = ref<User | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const showEditModal = ref(false);

const newPassword = ref('');
const resettingPassword = ref(false);
const pwdMessage = ref<string | null>(null);
const pwdIsError = ref(false);

const userId = computed(() => Number(route.params.id));

const fetchUser = async () => {
    if (!userId.value || Number.isNaN(userId.value)) {
        error.value = 'Invalid user identifier.';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const result = await userStore.getUser(userId.value);
        user.value = result;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load user.';
        user.value = null;
    } finally {
        loading.value = false;
    }
};

const refresh = async () => {
    await fetchUser();
};

const toggleActive = async () => {
    if (!user.value) return;
    try {
        loading.value = true;
        const updated = await userStore.toggleUserActive(user.value);
        user.value = updated;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to update user status.';
    } finally {
        loading.value = false;
    }
};

const handleUserUpdated = async () => {
    await fetchUser();
};

const handleResetPassword = async () => {
    if (!user.value || !newPassword.value) return;
    resettingPassword.value = true;
    pwdMessage.value = null;
    pwdIsError.value = false;

    try {
        await userStore.updateUser(user.value.id, { password: newPassword.value });
        pwdMessage.value = 'Password updated successfully.';
        newPassword.value = '';
    } catch (err: any) {
        pwdIsError.value = true;
        pwdMessage.value = err.response?.data?.message || 'Failed to reset password.';
    } finally {
        resettingPassword.value = false;
    }
};

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '—';
    return date.toLocaleDateString();
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
            return 'Standard User';
    }
};

onMounted(() => {
    fetchUser();
});
</script>
