<template>
    <div class="space-y-6 max-w-4xl mx-auto pb-16">
        <!-- Page Header -->
        <div>
            <h1 class="text-xl font-bold tracking-tight text-surface-900">Account Settings</h1>
            <p class="text-xs text-surface-500 font-medium">Update your profile details, contact information, and manage security settings.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <!-- Left Side Card: User Summary -->
            <div class="md:col-span-1">
                <div class="ynex-card p-6 flex flex-col items-center text-center">
                    <div class="h-20 w-20 rounded bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-3xl shadow-sm">
                        {{ initials }}
                    </div>
                    <h3 class="mt-4 text-base font-extrabold text-surface-900">{{ form.name }}</h3>
                    <p class="text-xs text-surface-500 font-medium mt-0.5 capitalize">{{ userRole }}</p>
                    
                    <div class="w-full border-t border-surface-100 mt-6 pt-6 space-y-3.5 text-left text-xs">
                        <div class="flex items-center gap-2.5 text-surface-600">
                            <Icon icon="mdi:email-outline" :size="16" class="text-surface-400" />
                            <span class="truncate">{{ form.email }}</span>
                        </div>
                        <div v-if="form.phone" class="flex items-center gap-2.5 text-surface-600">
                            <Icon icon="mdi:phone-outline" :size="16" class="text-surface-400" />
                            <span>{{ form.phone }}</span>
                        </div>
                        <div v-if="form.location" class="flex items-center gap-2.5 text-surface-600">
                            <Icon icon="mdi:map-marker-outline" :size="16" class="text-surface-400" />
                            <span>{{ form.location }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side Forms: Info & Security -->
            <div class="md:col-span-2 space-y-6">
                <!-- Notifications -->
                <div v-if="successMessage" class="p-4 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-semibold">
                    {{ successMessage }}
                </div>
                <div v-if="errorMessage" class="p-4 rounded border border-rose-200 bg-rose-50 text-rose-800 text-xs font-semibold">
                    {{ errorMessage }}
                </div>

                <form @submit.prevent="saveProfile" class="ynex-card">
                    <div class="ynex-card-header">
                        <h2 class="text-sm font-bold text-surface-800">Profile Information</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Full Name <span class="text-rose-500">*</span></label>
                                <input v-model="form.name" type="text" required
                                    class="mt-1 w-full rounded border border-surface-200 px-3 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Email Address <span class="text-rose-500">*</span></label>
                                <input v-model="form.email" type="email" required
                                    class="mt-1 w-full rounded border border-surface-200 px-3 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Phone Number</label>
                                <input v-model="form.phone" type="tel" placeholder="+256..."
                                    class="mt-1 w-full rounded border border-surface-200 px-3 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Location / Address</label>
                                <input v-model="form.location" type="text" placeholder="City, Country"
                                    class="mt-1 w-full rounded border border-surface-200 px-3 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>
                        </div>
                    </div>

                    <div class="ynex-card-header border-t border-surface-100">
                        <h2 class="text-sm font-bold text-surface-800">Change Password</h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <p class="text-xs text-surface-500">Leave these blank if you do not want to change your password.</p>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">New Password</label>
                                <input v-model="form.password" type="password" minlength="8" placeholder="Minimum 8 characters"
                                    class="mt-1 w-full rounded border border-surface-200 px-3 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>

                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-surface-500">Confirm New Password</label>
                                <input v-model="form.password_confirmation" type="password" minlength="8" placeholder="Repeat new password"
                                    class="mt-1 w-full rounded border border-surface-200 px-3 py-2 text-xs text-surface-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-50 px-6 py-4 flex justify-end gap-3 border-t border-surface-200 rounded-b">
                        <button type="submit" class="ynex-btn-primary py-2 px-4 text-xs flex items-center gap-1.5" :disabled="saving">
                            <Icon v-if="saving" icon="mdi:loading" :size="16" class="animate-spin" />
                            <Icon v-else icon="mdi:content-save-outline" :size="16" />
                            {{ saving ? 'Saving...' : 'Save Settings' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { useAuthStore } from '../../stores/authStore';
import Icon from '../../components/shared/Icon.vue';

const authStore = useAuthStore();

const saving = ref(false);
const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const form = reactive({
    name: authStore.user?.name ?? '',
    email: authStore.user?.email ?? '',
    phone: authStore.user?.phone ?? '',
    location: authStore.user?.location ?? '',
    password: '',
    password_confirmation: '',
});

const initials = computed(() => {
    return (form.name || 'U').charAt(0).toUpperCase();
});

const userRole = computed(() => {
    const type = authStore.user?.user_type ?? 'user';
    return type.replace('_', ' ');
});

const saveProfile = async () => {
    if (form.password && form.password !== form.password_confirmation) {
        errorMessage.value = 'Passwords do not match.';
        successMessage.value = null;
        return;
    }

    saving.value = true;
    errorMessage.value = null;
    successMessage.value = null;

    try {
        const payload: any = {
            name: form.name.trim(),
            email: form.email.trim(),
            phone: form.phone.trim() || null,
            location: form.location.trim() || null,
        };

        if (form.password) {
            payload.password = form.password;
            payload.password_confirmation = form.password_confirmation;
        }

        await authStore.updateProfile(payload);
        
        successMessage.value = 'Profile updated successfully.';
        form.password = '';
        form.password_confirmation = '';
    } catch (err: any) {
        errorMessage.value = err.response?.data?.message || 'Failed to update profile settings.';
    } finally {
        saving.value = false;
    }
};
</script>
